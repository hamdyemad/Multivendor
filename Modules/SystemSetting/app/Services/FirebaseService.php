<?php

namespace Modules\SystemSetting\app\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Exception\MessagingException;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = config('services.firebase.service_account');
        
        if ($serviceAccountPath && file_exists($serviceAccountPath)) {
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $this->messaging = $factory->createMessaging();
        }
    }

    /**
     * Send push notification to multiple FCM tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, ?string $image = null, array $data = []): array
    {
        if (empty($tokens) || !$this->messaging) {
            return ['success' => 0, 'failed' => count($tokens), 'errors' => []];
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $notification = Notification::create($title, $body);
        
        if ($image) {
            $notification = $notification->withImageUrl($image);
        }

        // Prepare data payload
        $data = array_merge($data, [
            'title' => $title,
            'body' => $body,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ]);

        if ($image) {
            $data['image'] = $image;
        }

        // Android config with sound
        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'channel_id' => 'high_importance_channel',
            ],
        ]);

        // iOS (APNs) config with sound
        $apnsConfig = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1,
                ],
            ],
        ]);

        // Send to each token individually to track success/failure
        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification)
                    ->withData($data)
                    ->withAndroidConfig($androidConfig)
                    ->withApnsConfig($apnsConfig);

                $this->messaging->send($message);
                $results['success']++;
            } catch (MessagingException $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'token' => $token,
                    'error' => $e->getMessage(),
                ];
                Log::warning('Firebase push notification failed for token: ' . $e->getMessage());
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'token' => $token,
                    'error' => $e->getMessage(),
                ];
                Log::error('Firebase push notification error: ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Send push notification to multiple tokens in batch (more efficient)
     */
    public function sendToTokensBatch(array $tokens, string $title, string $body, ?string $image = null, array $data = []): array
    {
        if (empty($tokens) || !$this->messaging) {
            return ['success' => 0, 'failed' => count($tokens), 'errors' => []];
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $notification = Notification::create($title, $body);
        
        if ($image) {
            $notification = $notification->withImageUrl($image);
        }

        $data = array_merge($data, [
            'title' => $title,
            'body' => $body,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ]);

        if ($image) {
            $data['image'] = $image;
        }

        // Android config with sound
        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'channel_id' => 'high_importance_channel',
            ],
        ]);

        // iOS (APNs) config with sound
        $apnsConfig = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1,
                ],
            ],
        ]);

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig($androidConfig)
            ->withApnsConfig($apnsConfig);

        try {
            $sendReport = $this->messaging->sendMulticast($message, $tokens);
            
            $results['success'] = $sendReport->successes()->count();
            $results['failed'] = $sendReport->failures()->count();

            foreach ($sendReport->failures()->getItems() as $failure) {
                $results['errors'][] = [
                    'token' => $failure->target()->value(),
                    'error' => $failure->error()->getMessage(),
                ];
            }
        } catch (\Exception $e) {
            $results['failed'] = count($tokens);
            $results['errors'][] = [
                'error' => $e->getMessage(),
            ];
            Log::error('Firebase batch push notification error: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Send push notification to a single token
     */
    public function sendToToken(string $token, string $title, string $body, ?string $image = null, array $data = []): bool
    {
        $result = $this->sendToTokens([$token], $title, $body, $image, $data);
        return $result['success'] > 0;
    }
}
