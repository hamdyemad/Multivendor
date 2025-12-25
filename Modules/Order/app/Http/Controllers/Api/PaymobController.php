<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Http\Requests\Api\CreatePaymentRequest;
use Modules\Order\app\Http\Requests\Api\CheckPaymentRequest;
use Modules\Order\app\Models\Order;
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
    public function createPayment(CreatePaymentRequest $request): JsonResponse
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
     * Check payment status
     */
    public function checkPayment(CheckPaymentRequest $request, string $paymobOrderId): JsonResponse
    {
        $payment = $this->paymobService->findPaymentByPaymobOrderId($paymobOrderId);

        if (!$payment) {
            return $this->sendRes(__('order::order.payment_not_found'), false, [], [], 404);
        }

        $statusInfo = $this->paymobService->getPaymentStatusInfo($payment);

        return $this->sendRes($statusInfo['message'], true, [
            'order_id' => $statusInfo['order_id'],
            'status' => $statusInfo['status'],
            'amount' => $statusInfo['amount'],
        ]);
    }
}
