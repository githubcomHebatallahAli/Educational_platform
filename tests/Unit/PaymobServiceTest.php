<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\PaymobService;

class PaymobServiceTest extends TestCase
{
    protected $paymobService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymobService = new PaymobService();
    }

    public function testRegisterOrderAuthFailure()
    {
        $authToken = 'invalid-token'; // توكن غير صالح
        $orderData = [/* بيانات طلب */];
        $paymentMethod = 'card';

        Http::fake([
            'https://accept.paymobsolutions.com/api/acceptance/transaction/initiate' => Http::response([
                'message' => 'Unauthorized'
            ], 401)
        ]);

        $response = $this->paymobService->registerOrder($authToken, $orderData, $paymentMethod);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
    }

    public function testRegisterOrderInvalidData()
    {
        $authToken = 'valid-token';
        $orderData = [/* بيانات غير صالحة */];
        $paymentMethod = 'card';

        Http::fake([
            'https://accept.paymobsolutions.com/api/acceptance/transaction/initiate' => Http::response([
                'message' => 'Invalid data provided'
            ], 400)
        ]);

        $response = $this->paymobService->registerOrder($authToken, $orderData, $paymentMethod);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid data provided', $response['error']);
    }

    public function testRegisterOrderUnexpectedError()
    {
        $authToken = 'valid-token';
        $orderData = [/* بيانات طلب صالحة */];
        $paymentMethod = 'card';

        // إعداد خطأ غير متوقع باستخدام رد استجابة HTTP وهمية
        Http::fake([
            'https://accept.paymobsolutions.com/api/acceptance/transaction/initiate' => function () {
                throw new \Exception('Unexpected error occurred');
            },
        ]);

        $response = $this->paymobService->registerOrder($authToken, $orderData, $paymentMethod);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('An unexpected error occurred', $response['error']);
    }

}
