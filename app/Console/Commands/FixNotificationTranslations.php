<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use Illuminate\Console\Command;

class FixNotificationTranslations extends Command
{
    protected $signature = 'notifications:fix-translations';
    protected $description = 'Fix notifications that have translated text instead of translation keys';

    public function handle()
    {
        $this->info('Fixing notification translations...');

        // Map of Arabic text to translation keys
        $translations = [
            'لم استلام طلب عرض سعر جديد' => 'order::request-quotation.notification_vendor_new_request_message',
            'طلب عرض سعر جديد' => 'order::request-quotation.notification_vendor_new_request_title',
            'تم استلام طلب عرض سعر جديد' => 'order::request-quotation.notification_new_request',
            'تم قبول عرض السعر' => 'order::request-quotation.notification_accepted',
            'تم رفض عرض السعر' => 'order::request-quotation.notification_rejected',
        ];

        $count = 0;

        foreach ($translations as $arabicText => $translationKey) {
            // Fix descriptions
            $updated = AdminNotification::where('description', $arabicText)
                ->update(['description' => $translationKey]);
            $count += $updated;

            // Fix titles
            $updated = AdminNotification::where('title', $arabicText)
                ->update(['title' => $translationKey]);
            $count += $updated;
        }

        $this->info("Fixed {$count} notifications.");
        
        return 0;
    }
}
