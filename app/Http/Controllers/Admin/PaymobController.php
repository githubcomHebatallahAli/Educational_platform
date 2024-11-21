<?php

namespace App\Http\Controllers\Admin;

use Basketin\Paymob\Pay;

use Illuminate\Http\Request;
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

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

       // دالة لبدء الدفع (Initiate Payment)
       public function initiatePayment(Request $request)
       {
           // التحقق من أن جميع المدخلات المطلوبة موجودة
           $request->validate([
               'price' => 'required|numeric|min:1',
               'name' => 'required|string',
               'email' => 'required|email',
               'phone' => 'required|string',
               'payment_method' => 'required|in:card,wallet', // يمكن أن يكون إما "card" أو "wallet"
           ]);

           try {
               // 1. الحصول على التوكن من بايموب (من خلال المصادقة)
               $authToken = $this->paymobService->authenticate();

               // 2. تسجيل الطلب (Order)
               $orderData = [
                   'amount_cents' => $request->price * 100, // تحويل المبلغ إلى القروش
                   'currency' => 'EGP',
                   'delivery_needed' => 'false', // يمكن تخصيصها حسب الحاجة
                   'merchant_order_ext_ref' => 'ORDER_' . uniqid(),
                   'items' => [] // إذا كان هناك منتجات أو عناصر، يمكن تضمينها هنا
               ];

               $order = $this->paymobService->registerOrder($authToken, $orderData, $request->payment_method);

               // 3. إنشاء Payment Key
               $paymentData = [
                   'amount_cents' => $request->price * 100, // تحويل المبلغ إلى القروش
                   'expiration' => time() + 3600, // المهلة (1 ساعة)
                   'order_id' => $order['id'],
                   'billing_data' => [
                       'email' => $request->email,
                       'phone_number' => $request->phone,
                       'first_name' => $request->name,
                   ],
               ];

               $paymentKey = $this->paymobService->generatePaymentKey($authToken, $paymentData, $request->payment_method);

               // 4. إرسال الـ Payment Key للعميل لبدء الدفع
               return response()->json([
                   'payment_key' => $paymentKey,
                   'order_id' => $order['id'],
               ]);

           } catch (\Exception $e) {
               // في حال حدوث خطأ في أي خطوة من الخطوات
               return response()->json([
                   'message' => $e->getMessage(),
               ]);
           }
       }

       // دالة لمعالجة رد بايموب (تحديث الحالة بعد الدفع)
       public function handlePaymentCallback(Request $request)
       {
           // تحقق من HMAC
           $hmac = $request->header('hmac');
           $computedHmac = hash_hmac('sha512', json_encode($request->all()), config('paymob.hmac'));

           if ($hmac !== $computedHmac) {
               return response()->json(['message' => 'Invalid HMAC'], 400);
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
