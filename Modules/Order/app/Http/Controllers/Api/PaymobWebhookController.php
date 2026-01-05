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
            // Paymob can send data in two formats:
            // 1. Nested format: { obj: { id, order: { id }, success, ... } }
            // 2. Flat format: { id, order, success, ... }
            
            $transactionData = $data;
            $transactionId = null;
            $paymobOrderId = null;
            $success = false;

            // Check for nested format (obj wrapper)
            if (isset($data['obj']) && isset($data['obj']['id'])) {
                $transactionData = $data['obj'];
                $transactionId = $transactionData['id'];
                $paymobOrderId = $transactionData['order']['id'] ?? $transactionData['order'] ?? null;
                $success = filter_var($transactionData['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
            }
            // Check for flat format
            elseif (isset($data['id'])) {
                $transactionId = $data['id'];
                // In flat format, 'order' is the paymob order ID directly
                $paymobOrderId = $data['order'] ?? null;
                $success = filter_var($data['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
            }
            else {
                Log::warning('Paymob webhook: Invalid data structure', $data);
                return response('Invalid webhook data', 400);
            }

            if (!$paymobOrderId) {
                Log::warning('Paymob webhook: Missing order ID', $data);
                return response('Missing order ID', 400);
            }

            Log::info('Paymob webhook: Processing payment', [
                'transaction_id' => $transactionId,
                'paymob_order_id' => $paymobOrderId,
                'success' => $success,
            ]);

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

            // Store additional transaction data (handle both flat and nested formats)
            $sourceType = $transactionData['source_data']['type'] ?? $data['source_data_type'] ?? null;
            $sourceSubType = $transactionData['source_data']['sub_type'] ?? $data['source_data_sub_type'] ?? null;
            $sourcePan = $transactionData['source_data']['pan'] ?? $data['source_data_pan'] ?? null;
            
            $payment->update([
                'transaction_id' => $transactionId,
                'payment_data' => [
                    'transaction_id' => $transactionId,
                    'amount_cents' => $transactionData['amount_cents'] ?? $data['amount_cents'] ?? null,
                    'currency' => $transactionData['currency'] ?? $data['currency'] ?? null,
                    'source_type' => $sourceType,
                    'source_sub_type' => $sourceSubType,
                    'source_pan' => $sourcePan,
                    'is_3d_secure' => filter_var($transactionData['is_3d_secure'] ?? $data['is_3d_secure'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'integration_id' => $transactionData['integration_id'] ?? $data['integration_id'] ?? null,
                    'data_message' => $data['data_message'] ?? null,
                    'txn_response_code' => $data['txn_response_code'] ?? null,
                    'merchant_order_id' => $data['merchant_order_id'] ?? null,
                    'created_at' => $transactionData['created_at'] ?? $data['created_at'] ?? null,
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
