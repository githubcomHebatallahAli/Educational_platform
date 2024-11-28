<?php
return [
    'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
    'card_integration_id' => env('PAYMOB_CARD_INTEGRATION_ID'),
    'api_key' => env('PAYMOB_API_KEY'),
    'hmac' => env('PAYMOB_HMAC'),
    'secret_key' => env('PAYMOB_SECRET_KEY'),
    'public_key' => env('PAYMOB_PUBLIC_KEY'),
    'currency' => env('PAYMOB_CURRENCY', 'EGP'),
    'base_url' => env('PAYMOB_BASE_URL'),
    'merchant_id' => env('PAYMOB_MERCHANT_ID'),
    // 'iframe_id' => env('PAYMOB_IFRAME_ID'),
];

