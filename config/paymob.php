<?php
return [
    'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
    'card_integration_id' => env('PAYMOB_CARD_INTEGRATION_ID'),
    'api_key' => env('PAYMOB_API_KEY'),
    'hmac' => env('PAYMOB_HMAC'),
    'currency' => env('PAYMOB_CURRENCY', 'EGP'),
    // 'iframe_id' => env('PAYMOB_IFRAME_ID'),
];

