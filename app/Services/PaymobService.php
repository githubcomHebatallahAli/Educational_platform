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

    // دالة لإنشاء الطلب (Order) على بايموب
    // public function registerOrder($authToken, $orderData, $paymentMethod = 'card')
    // {
    //     // الحصول على رقم التكامل بناءً على نوع الدفع
    //     $integrationId = $this->getIntegrationId($paymentMethod);

    //     $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/ecommerce/orders', array_merge($orderData, [
    //         'integration_id' => $integrationId, // استخدام التكامل المناسب
    //     ]));

    //     if ($response->successful()) {
    //         return $response->json();
    //     }

    //     throw new \Exception('Failed to register order');
    // }

    // // دالة لإنشاء الـ Payment Key
    // public function generatePaymentKey($authToken, $paymentData, $paymentMethod = 'card')
    // {
    //     // الحصول على رقم التكامل بناءً على نوع الدفع
    //     $integrationId = $this->getIntegrationId($paymentMethod);

    //     $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/acceptance/payment_keys', array_merge($paymentData, [
    //         'integration_id' => $integrationId, // استخدام التكامل المناسب
    //     ]));

    //     if ($response->successful()) {
    //         return $response->json()['token'];
    //     }

    //     throw new \Exception('Failed to generate payment key');
    // }


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
}
