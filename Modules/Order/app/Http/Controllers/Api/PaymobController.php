<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Http\Requests\Api\CreatePaymentRequest;
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
    public function createPayment(CreatePaymentRequest $request)
    {
        try {
            $order = Order::with('customer')->findOrFail($request->order_id);

            $billingData = [
                'first_name' => $order->customer?->first_name ?? $order->customer?->name ?? 'N/A',
                'last_name' => $order->customer?->last_name ?? 'N/A',
                'email' => $order->customer?->email ?? 'N/A',
                'phone_number' => $order->customer?->phone ?? 'N/A',
            ];

            $result = $this->paymobService->initiatePayment(
                $order,
                $billingData,
                $request->method
            );

            $order->update(['payment_visa_status' => 'pending']);

            return $this->sendRes(__('order::order.payment_created'), true, [
                'payment_url' => $result['checkout_url'],
                'order_id' => $order->id,
                'paymob_order_id' => $result['paymob_order_id'],
            ]);

        } catch (Exception $e) {
            Log::error('Paymob create payment error', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
            ]);

            // Try to parse JSON error from PaymobService
            $errorData = [];
            $errorMessage = $e->getMessage();
            
            try {
                $parsedError = json_decode($e->getMessage(), true);
                if (is_array($parsedError)) {
                    $errorData = $parsedError;
                    $errorMessage = $parsedError['message'] ?? $parsedError['detail'] ?? $parsedError['merchant_order_id'] ?? 'Payment creation failed';
                }
            } catch (\Exception $parseEx) {
                // Keep original message
            }

            return $this->sendRes($errorMessage, false, [], $errorData, 500);
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
            $result = $this->paymobService->processCallback($data);

            if ($result['success']) {
                return $this->sendRes(__('order::order.payment_successful'), true, [
                    'order_id' => $result['order_id'],
                    'status' => $result['status'],
                ]);
            } else {
                return $this->sendRes(__('order::order.payment_failed'), false, [
                    'order_id' => $result['order_id'],
                    'status' => $result['status'],
                ]);
            }

        } catch (Exception $e) {
            Log::error('Paymob callback error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return $this->sendRes($e->getMessage(), false, [], [], 500);
        }
    }

    /**
     * Check payment status by paymob_order_id
     * Fetches latest status from Paymob API
     */
    public function checkPayment(Request $request, string $paymobOrderId): JsonResponse
    {
        try {
            // Find payment by paymob_order_id
            $payment = Payment::where('paymob_order_id', $paymobOrderId)->first();

            if (!$payment) {
                return $this->sendRes(__('order::order.payment_not_found'), false, [], [], 404);
            }

            // If we have a transaction_id, fetch latest status from Paymob
            if ($payment->transaction_id) {
                $paymobData = $this->paymobService->retrieveTransaction($payment->transaction_id);
                
                if ($paymobData) {
                    // Update local payment status based on Paymob response
                    $isSuccess = filter_var($paymobData['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    $isPending = filter_var($paymobData['pending'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    
                    $newStatus = $isPending ? Payment::STATUS_PENDING : ($isSuccess ? Payment::STATUS_PAID : Payment::STATUS_FAILED);
                    
                    // Update payment if status changed
                    if ($payment->status !== $newStatus) {
                        $payment->update(['status' => $newStatus]);
                        
                        // Update order status if payment is now successful
                        if ($newStatus === Payment::STATUS_PAID) {
                            $this->paymobService->handlePaymentSuccess($payment, $payment->transaction_id);
                        } elseif ($newStatus === Payment::STATUS_FAILED) {
                            $this->paymobService->handlePaymentFailure($payment);
                        }
                    }

                    return $this->sendRes(__('order::order.payment_status'), true, [
                        'order_id' => $payment->order_id,
                        'payment_id' => $payment->id,
                        'paymob_order_id' => $payment->paymob_order_id,
                        'status' => $newStatus,
                        'is_paid' => $newStatus === Payment::STATUS_PAID,
                        'is_pending' => $newStatus === Payment::STATUS_PENDING,
                        'is_failed' => $newStatus === Payment::STATUS_FAILED,
                        'amount' => $payment->amount,
                        'transaction_id' => $payment->transaction_id,
                        'paymob_data' => [
                            'success' => $isSuccess,
                            'pending' => $isPending,
                            'amount_cents' => $paymobData['amount_cents'] ?? null,
                            'currency' => $paymobData['currency'] ?? null,
                            'source_data_type' => $paymobData['source_data']['type'] ?? null,
                            'source_data_sub_type' => $paymobData['source_data']['sub_type'] ?? null,
                            'data_message' => $paymobData['data']['message'] ?? null,
                        ],
                    ]);
                }
            }

            // Fallback to local status if can't fetch from Paymob
            $statusInfo = $this->paymobService->getPaymentStatusInfo($payment);

            return $this->sendRes($statusInfo['message'], true, [
                'order_id' => $statusInfo['order_id'],
                'payment_id' => $payment->id,
                'paymob_order_id' => $payment->paymob_order_id,
                'status' => $statusInfo['status'],
                'is_paid' => $payment->isPaid(),
                'is_pending' => $payment->isPending(),
                'is_failed' => $payment->isFailed(),
                'amount' => $statusInfo['amount'],
                'transaction_id' => $payment->transaction_id,
            ]);

        } catch (Exception $e) {
            Log::error('Paymob check payment error', [
                'error' => $e->getMessage(),
                'paymob_order_id' => $paymobOrderId,
            ]);

            return $this->sendRes($e->getMessage(), false, [], [], 500);
        }
    }
}
