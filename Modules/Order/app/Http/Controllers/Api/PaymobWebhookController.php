<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Models\Payment;
use Modules\Order\app\Services\PaymobService;

class PaymobWebhookController extends Controller
{
    public function __construct(protected PaymobService $paymobService)
    {
    }

    /**
     * Handle Paymob webhook notifications
     */
    public function handle(Request $request): Response
    {
        $data = $request->all();

        Log::info('Paymob webhook received', $data);

        try {
            // Check if this is a transaction callback
            if (!isset($data['obj']) || !isset($data['obj']['id'])) {
                Log::warning('Paymob webhook: Invalid data structure', $data);
                return response('Invalid webhook data', 400);
            }

            $transactionData = $data['obj'];
            $transactionId = $transactionData['id'];
            $paymobOrderId = $transactionData['order']['id'] ?? null;
            $success = $transactionData['success'] ?? false;

            if (!$paymobOrderId) {
                Log::warning('Paymob webhook: Missing order ID', $data);
                return response('Missing order ID', 400);
            }

            // Find the payment record
            $payment = Payment::where('paymob_order_id', $paymobOrderId)
                ->latest()
                ->first();

            if (!$payment) {
                Log::warning('Paymob webhook: Payment not found', [
                    'paymob_order_id' => $paymobOrderId,
                ]);
                return response('Payment not found', 404);
            }

            // Skip if payment is already processed
            if ($payment->status === Payment::STATUS_PAID) {
                Log::info('Paymob webhook: Payment already processed', [
                    'payment_id' => $payment->id,
                ]);
                return response('OK', 200);
            }

            // Process the payment based on success status
            if ($success) {
                $this->paymobService->handlePaymentSuccess($payment, $transactionId);

                Log::info('Paymob webhook: Payment marked as successful', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'transaction_id' => $transactionId,
                ]);
            } else {
                $this->paymobService->handlePaymentFailure($payment);

                Log::info('Paymob webhook: Payment marked as failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'transaction_id' => $transactionId,
                ]);
            }

            // Store additional transaction data
            $payment->update([
                'payment_data' => [
                    'transaction_id' => $transactionId,
                    'amount_cents' => $transactionData['amount_cents'] ?? null,
                    'currency' => $transactionData['currency'] ?? null,
                    'source_type' => $transactionData['source_data']['type'] ?? null,
                    'source_sub_type' => $transactionData['source_data']['sub_type'] ?? null,
                    'is_3d_secure' => $transactionData['is_3d_secure'] ?? null,
                    'integration_id' => $transactionData['integration_id'] ?? null,
                    'created_at' => $transactionData['created_at'] ?? null,
                ],
            ]);

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Paymob webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return response('Internal Server Error', 500);
        }
    }
}
