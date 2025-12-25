<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\Payment;
use Modules\Order\app\Services\PaymobService;

class PaymobController extends Controller
{
    use Res;

    public function __construct(protected PaymobService $paymobService)
    {
    }

    /**
     * Create a payment for an order
     */
    public function createPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'order_id' => ['required', Rule::exists('orders', 'id')],
            'method' => ['required', 'in:card,souhola,valu,forsa,wallet,bank_installment'],
        ]);

        $validator->after(function ($validator) {
            $orderId = $validator->getData()['order_id'] ?? null;
            if (!$orderId) return;

            $order = Order::find($orderId);
            if (!$order) return;

            if ($order->payment_type !== 'online') {
                $validator->errors()->add('order_id', 'Order payment type is not online');
            }

            // Check if order is in "new" stage (stage_id = 1 or first stage)
            if ($order->stage_id && $order->stage_id != 1) {
                $validator->errors()->add('order_id', 'Your Order Status is not (new)');
            }

            // Check if there's already a successful payment
            $existingPayment = Payment::where('order_id', $orderId)
                ->where('status', Payment::STATUS_PAID)
                ->first();
            
            if ($existingPayment) {
                $validator->errors()->add('order_id', 'This order has already been paid');
            }
        });

        if ($validator->fails()) {
            return $this->sendRes(
                implode(', ', $validator->errors()->all()),
                false,
                [],
                $validator->errors()->all(),
                422
            );
        }

        try {
            $order = Order::findOrFail($request->order_id);

            $billingData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone,
            ];

            $result = $this->paymobService->initiatePayment(
                $order,
                $billingData,
                $request->method
            );

            // Update order payment status to pending
            $order->update([
                'payment_visa_status' => 'pending',
            ]);

            return $this->sendRes('Payment created successfully', true, [
                'payment_url' => $result['checkout_url'],
                'order_id' => $order->id,
                'paymob_order_id' => $result['paymob_order_id'],
            ]);

        } catch (Exception $e) {
            Log::error('Paymob create payment error', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
            ]);

            return $this->sendRes($e->getMessage(), false, [], [], 500);
        }
    }

    /**
     * Handle Paymob callback (redirect after payment)
     */
    public function callback(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('Paymob callback received', $data);

        try {
            // Get transaction ID from callback data
            $transactionId = $data['obj']['id'] ?? $data['id'] ?? null;

            if (!$transactionId) {
                return $this->sendRes('Invalid callback data', false, [], [], 400);
            }

            // Retrieve transaction from Paymob
            $transaction = $this->paymobService->retrieveTransaction($transactionId);

            if (!$transaction) {
                return $this->sendRes('Transaction not found', false, [], [], 404);
            }

            $success = $transaction['success'] ?? false;
            $paymobOrderId = $transaction['order']['id'] ?? null;

            $payment = Payment::where('paymob_order_id', $paymobOrderId)
                ->latest()
                ->first();

            if (!$payment) {
                return $this->sendRes('Payment not found', false, [], [], 404);
            }

            if ($success) {
                $this->paymobService->handlePaymentSuccess($payment, $transactionId);
                return $this->sendRes('Payment successful', true, [
                    'order_id' => $payment->order_id,
                    'status' => 'success',
                ]);
            } else {
                $this->paymobService->handlePaymentFailure($payment);
                return $this->sendRes('Payment failed', false, [
                    'order_id' => $payment->order_id,
                    'status' => 'failed',
                ]);
            }

        } catch (Exception $e) {
            Log::error('Paymob callback error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return $this->sendRes('Callback processing failed', false, [], [], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPayment(Request $request, string $paymobOrderId): JsonResponse
    {
        $payment = Payment::where('paymob_order_id', $paymobOrderId)
            ->latest()
            ->first();

        if (!$payment) {
            return $this->sendRes('Payment not found', false, [], [], 404);
        }

        $statusMessage = match ($payment->status) {
            Payment::STATUS_PAID => 'payment_success',
            Payment::STATUS_FAILED => 'payment_failed',
            Payment::STATUS_PENDING => 'payment_pending',
            default => 'payment_unknown',
        };

        return $this->sendRes($statusMessage, true, [
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => $payment->amount,
        ]);
    }
}
