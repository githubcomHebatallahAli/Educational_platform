<?php

namespace App\Services;

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
            // استخدام تكامل المحفظة
            return config('paymob.wallet_integration_id');
        }

        // افتراضيًا استخدام تكامل بطاقة الائتمان
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
    public function registerOrder($authToken, $orderData, $paymentMethod = 'card')
    {
        // الحصول على رقم التكامل بناءً على نوع الدفع
        $integrationId = $this->getIntegrationId($paymentMethod);

        $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/ecommerce/orders', array_merge($orderData, [
            'integration_id' => $integrationId, // استخدام التكامل المناسب
        ]));

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to register order');
    }

    // دالة لإنشاء الـ Payment Key
    public function generatePaymentKey($authToken, $paymentData, $paymentMethod = 'card')
    {
        // الحصول على رقم التكامل بناءً على نوع الدفع
        $integrationId = $this->getIntegrationId($paymentMethod);

        $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/acceptance/payment_keys', array_merge($paymentData, [
            'integration_id' => $integrationId, // استخدام التكامل المناسب
        ]));

        if ($response->successful()) {
            return $response->json()['token'];
        }

        throw new \Exception('Failed to generate payment key');
    }
}
