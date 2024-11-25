<?php

namespace App\Http\Controllers\Admin;

use Basketin\Paymob\Pay;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\PaymobService;
use App\Models\PaymobTransaction;
use App\Http\Controllers\Controller;
use Basketin\Paymob\Configs\AmountToCent;
use Basketin\Paymob\Configs\PaymentMethod;

class PaymobController extends Controller
{
    // public function getPaymentLink()
    // {
    //     $pay = new Pay;
    //     $pay->setMethod(new PaymentMethod('wallet'));
    //     $pay->setAmount(new AmountToCent(1000));
    //     $pay->setMerchantOrderId(1234567);

    //     return $pay->getLink();
    // }

    protected $paymobService;
    protected $orderService;

    public function __construct(PaymobService $paymobService,
     OrderService $orderService)
    {
        $this->paymobService = $paymobService;
        $this->orderService = $orderService;
    }

    public function initiatePayment(Request $request)
    {
        $user = auth()->guard('api')->user();

    if (!$user) {
        return response()->json(['message' => 'Auth failed'], 401);
    }
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'payment_method' => 'required|in:card,wallet',
        ]);

        try {
            // 1. إنشاء أوردر بحالة pending
            $order = $this->orderService->createOrder(
                auth()->guard('api')->user()->id,
                $request->course_id,
                $request->payment_method
            );

            // 2. الحصول على التوكن من Paymob
            $authToken = $this->paymobService->authenticate();

            // 3. تسجيل الطلب في Paymob
            $orderData = [
                'amount_cents' => $order->course->price * 100, // فرضًا أن جدول الكورسات يحتوي على عمود `price`
                'currency' => 'EGP',
                'delivery_needed' => 'false',
                'merchant_order_ext_ref' => 'ORDER_' . $order->id,
                'items' => [
                    [
                        'name' => $order->course->title,
                        'amount_cents' => $order->course->price * 100,
                        'quantity' => 1,
                    ]
                ],
            ];

            $paymobOrder = $this->paymobService->registerOrder($authToken, $orderData, $request->payment_method);

            // 4. إنشاء Payment Key
            $paymentData = [
                'amount_cents' => $order->course->price * 100,
                'expiration' => time() + 3600,
                'order_id' => $paymobOrder['id'],
                'billing_data' => [
                    'email' =>  auth()->guard('api')->user()->email,
                    'phone_number' =>  auth()->guard('api')->user()->phone,
                    'first_name' =>  auth()->guard('api')->user()->name,
                ],
            ];

            $paymentKey = $this->paymobService->generatePaymentKey($authToken, $paymentData, $request->payment_method);

            // 5. إعادة رابط الدفع للمستخدم
            return response()->json([
                'payment_key' => $paymentKey,
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

       // دالة لمعالجة رد بايموب (تحديث الحالة بعد الدفع)
       public function handlePaymentCallback(Request $request)
       {
           // تحقق من HMAC
           $hmac = $request->header('hmac');
           $computedHmac = hash_hmac('sha512', json_encode($request->all()), config('paymob.hmac'));

           if ($hmac !== $computedHmac) {
               return response()->json([
                'message' => 'Invalid HMAC'
            ]);
           }

           // تحديث حالة المعاملة بناءً على الرد من بايموب
           $transaction = PaymobTransaction::where('order_id', $request->order_id)->first();
           if ($transaction) {
               $transaction->status = $request->success ? 'successful' : 'failed';
               $transaction->save();
           }

           return response()->json([
            'message' => 'Transaction updated successfully'
        ]);
       }

       // دالة لمعالجة Webhook من بايموب
       public function handleWebhook(Request $request)
       {
           // تحقق من HMAC
           $hmac = $request->header('hmac');
           $computedHmac = hash_hmac('sha512', json_encode($request->all()), config('paymob.hmac'));

           if ($hmac !== $computedHmac) {
               return response()->json([
                'message' => 'Invalid HMAC'
            ]);
           }

           // معالجة الطلب حسب التفاصيل التي تم إرسالها في الـ Webhook
           // مثال: تحديث حالة المعاملة بناءً على المعلومات الواردة
           // يمكنك إضافة أي تفاصيل أو إجراءات حسب حاجتك

           return response()->json([
            'message' => 'Webhook received successfully'
        ]);
       }


}
