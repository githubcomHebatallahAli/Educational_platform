<?php

namespace App\Services;

use Log;
use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $apiKey;
    protected $integrationId;
    protected $hmac;

    public function __construct()
    {
        $this->apiKey = config('paymob.api_key');
        $this->hmac = config('paymob.hmac');
    }

    // دالة لاختيار رقم التكامل بناءً على نوع الدفع
    public function getIntegrationId($paymentMethod)
    {
        if ($paymentMethod == 'wallet') {

            return config('paymob.wallet_integration_id');
        }

        return config('paymob.card_integration_id');
    }

    // الدالة الخاصة بالمصادقة للحصول على التوكن
    public function authenticate()
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        throw new \Exception('Authentication failed');
    }




    public function registerOrder($authToken, $orderData, $paymentMethod)
    {
        $url = "https://accept.paymobsolutions.com/api/acceptance/transaction/initiate";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $authToken
            ])->post($url, $orderData);

            if ($response->failed()) {
                $error = $response->json();
                Log::error('Failed to register order:', [
                    'authToken' => $authToken,
                    'orderData' => $orderData,
                    'paymentMethod' => $paymentMethod,
                    'response' => $error,
                ]);
                return [
                    'error' => $error['message'] ?? 'Failed to register order'
                ];
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Exception occurred while registering order:', [
                'authToken' => $authToken,
                'orderData' => $orderData,
                'paymentMethod' => $paymentMethod,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'error' => 'An unexpected error occurred'
            ];
        }
    }

    public function generatePaymentKey($authToken, $paymentData, $paymentMethod)
{
    $url = "https://accept.paymobsolutions.com/api/acceptance/payment_keys";

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authToken,
        ])->post($url, $paymentData);

        if ($response->failed()) {
            \Log::error('Failed to generate payment key:', [
                'authToken' => $authToken,
                'paymentData' => $paymentData,
                'paymentMethod' => $paymentMethod,
                'response' => $response->json(),
            ]);
            return [
                'error' => $response->json()['message'] ?? 'Failed to generate payment key',
            ];
        }

        return $response->json();

    } catch (\Exception $e) {
        \Log::error('Exception occurred while generating payment key:', [
            'authToken' => $authToken,
            'paymentData' => $paymentData,
            'paymentMethod' => $paymentMethod,
            'exception' => $e->getMessage(),
        ]);
        return [
            'error' => 'An unexpected error occurred while generating payment key',
        ];
    }
}

public function createIntention($amount, $currency, $paymentMethods, $billingData)
{
    $response = Http::withHeaders([
       'Authorization' => 'Token ' . env('PAYMOB_SECRET_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://accept.paymob.com/v1/intention/', [
        'amount' => $amount,
        'currency' => $currency,
        'payment_methods' => $paymentMethods,
        'billing_data' => $billingData,
    ]);

    if ($response->failed()) {
        throw new \Exception('Failed to create intention: ' . $response->body());
    }

    return $response->json();
}


}
