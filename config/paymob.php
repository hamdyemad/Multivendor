<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paymob API Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('PAYMOB_API_KEY', 'ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2TVRFd01qSXhOU3dpYm1GdFpTSTZJbWx1YVhScFlXd2lmUS5vc3BaRWkxNWs2NEpsUUVEdkJvTmUyMHRuRzFpZ0R1cG9FNTAxYlh4c1ByN2FGXzJCQUI5S2w5eDYxMlBDWUJxVmowU0ZYVldHYVJLeHZjaGJ1ZWg5QQ=='),
    'secret_key' => env('PAYMOB_SECRET_KEY', 'egy_sk_test_64721a21425d144f356f05f917df27bc524217e021fb1150615ccdc4a7b8f0e7'),
    'public_key' => env('PAYMOB_PUBLIC_KEY', 'egy_pk_test_YZR28Abg2rcUupfGapRPPMST8IFUMwrU'),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Integration IDs for different payment methods
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'card' => env('PAYMOB_CARD_INTEGRATION_ID'),
        'wallet' => env('PAYMOB_WALLET_INTEGRATION_ID'),
        'valu' => env('PAYMOB_VALU_INTEGRATION_ID'),
        'souhola' => env('PAYMOB_SOUHOLA_INTEGRATION_ID'),
        'forsa' => env('PAYMOB_FORSA_INTEGRATION_ID'),
        'bank_installment' => env('PAYMOB_BANK_INSTALLMENT_INTEGRATION_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paymob API URLs
    |--------------------------------------------------------------------------
    */
    'base_url' => env('PAYMOB_BASE_URL', 'https://accept.paymob.com/v1'),
    'checkout_url' => env('PAYMOB_CHECKOUT_URL', 'https://accept.paymob.com/unifiedcheckout'),

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    */
    'callback_url' => env('PAYMOB_CALLBACK_URL'),
    'webhook_url' => env('PAYMOB_WEBHOOK_URL'),
];
