<?php

return [

    'sms' => [
        // Shown in the audit log as the "from" label — a global platform default,
        // not a real sender identity. Which gateway actually sends (if any) and its
        // credentials are configured per salon in Settings → SMS Reminders
        // (App\Models\Salon::sms_driver / sms_credentials), not here.
        'sender_id' => env('SMS_SENDER_ID', env('APP_NAME', 'Salon')),

        'log' => [
            'path' => storage_path('logs/sms.log'),
        ],
    ],

];
