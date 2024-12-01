<?php

namespace App\Http\Controllers\Admin;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class PaymobController extends Controller
{

    public function generateToken(Request $request)
{
    $apiKey = $request->input('api_key');

    $response = Http::post('https://accept.paymobsolutions.com/api/auth/tokens', [
        'api_key' => $apiKey,
    ]);

    return $response->json();
}

public function createIntention(Request $request)
{

    $data = [
        "amount_cents" => $request->input('amount_cents'),
        "currency" => $request->input('currency', 'EGP'), // العملة الافتراضية هي EGP
        "billing_data" => $request->input('billing_data'),
        "payment_methods" => $request->input('payment_methods', []), // طرق الدفع
        "items" => $request->input('items', []), // قائمة السلع
        "special_reference" => $request->input('special_reference'), // المرجع الخاص
        "expiration" => $request->input('expiration', 3600), // مدة انتهاء الطلب
        "notification_url" => $request->input('notification_url'), // رابط الإشعار
        "redirection_url" => $request->input('redirection_url'), // رابط إعادة التوجيه
    ];

    try {
        // إرسال الطلب باستخدام HTTP Client
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('paymob.secret_key'),
            'Content-Type' => 'application/json',
        ])->post('https://accept.paymob.com/v1/intention/', $data);

        // التحقق من نجاح الطلب
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'Request failed',
                'details' => $response->json()
            ], 400);
        }

    } catch (\Exception $e) {
        // معالجة الأخطاء
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function createPaymentRequest(Request $request)
    {
        $data = [
            "amount" => 1000,  // المبلغ المطلوب دفعه
            "currency" => "EGP",  // العملة
            "payment_methods" => [4873707, 4871116],  // طرق الدفع المدعومة (البطاقات والمحفظة)
            "items" => [
                [
                    "name" => "Item name",
                    "amount" => 1000,  // سعر السلعة
                    "description" => "Item description",  // وصف السلعة
                    "quantity" => 1  // الكمية
                ]
            ],
            "billing_data" => [
                "apartment" => "dumy",
                "first_name" => "ala",
                "last_name" => "zain",
                "street" => "dumy",
                "building" => "dumy",
                "phone_number" => "+92345111111",
                "city" => "dumy",
                "country" => "dumy",
                "email" => "ali@gmail.com",
                "floor" => "dumy",
                "state" => "dumy"
            ],
            "extras" => [
                "ee" => 22  // بيانات إضافية
            ],
            "special_reference" => "phe4sjw11q-1xxxxxxxxx",  // مرجع خاص
            "expiration" => 3600,  // مدة انتهاء الطلب (بالثواني)
            "notification_url" => "https://example.com/webhook",  // رابط إشعار وهمي
            "redirection_url" => "https://example.com/success"  // رابط التوجيه بعد الدفع
        ];

        try {
            // إرسال الطلب إلى Paymob باستخدام HTTP Client الخاص بـ Laravel
            $response = Http::withHeaders([
                'Authorization' => 'Token YOUR_API_KEY',  // استخدم التوكن الخاص بك
                'Content-Type' => 'application/json'
            ])
            ->post('https://accept.paymob.com/v1/intention/', $data);

            // التحقق من حالة الاستجابة
            if ($response->successful()) {
                $responseData = $response->json();
                return response()->json($responseData);
            } else {
                return response()->json(['error' => 'Request failed', 'details' => $response->json()], 400);
            }

        } catch (\Exception $e) {
            // معالجة الأخطاء في حال حدوث أي مشكلة أثناء الطلب
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function generateCheckoutUrl(Request $request)
{
    $publicKey = $request->input('public_key');
    $clientSecret = $request->input('client_secret');

    $checkoutUrl = "https://accept.paymob.com/unifiedcheckout/?publicKey={$publicKey}&clientSecret={$clientSecret}";

    return response()->json(['checkout_url' => $checkoutUrl]);
}
}




// use Illuminate\Http\Request;
// use App\Services\OrderService;
// use App\Services\PaymobService;
// use App\Models\PaymobTransaction;
// use App\Http\Controllers\Controller;
// use Basketin\Paymob\Configs\AmountToCent;
// use Basketin\Paymob\Configs\PaymentMethod;

// class PaymobController extends Controller
// {
//     // public function getPaymentLink()
//     // {
//     //     $pay = new Pay;
//     //     $pay->setMethod(new PaymentMethod('wallet'));
//     //     $pay->setAmount(new AmountToCent(1000));
//     //     $pay->setMerchantOrderId(1234567);

//     //     return $pay->getLink();
//     // }

//     protected $paymobService;
//     protected $orderService;

//     public function __construct(PaymobService $paymobService,
//      OrderService $orderService)
//     {
//         $this->paymobService = $paymobService;
//         $this->orderService = $orderService;
//     }

//     public function initiatePayment(Request $request)
//     {
//         $user = auth()->guard('api')->user();

//     if (!$user) {
//         return response()->json(['message' => 'Auth failed'], 401);
//     }
//         $request->validate([
//             'course_id' => 'required|exists:courses,id',
//             'payment_method' => 'required|in:card,wallet',
//         ]);

//         try {
//             // 1. إنشاء أوردر بحالة pending
//             $order = $this->orderService->createOrder(
//                 auth()->guard('api')->user()->id,
//                 $request->course_id,
//                 $request->payment_method
//             );

//             // 2. الحصول على التوكن من Paymob
//             $authToken = $this->paymobService->authenticate();
//             if (!$authToken) {
//                 return response()->json(['message' => 'Authentication failed'], 401);
//             }

//             // 3. تسجيل الطلب في Paymob
//             $orderData = [
//                 'amount_cents' => $order->course->price * 100, // فرضًا أن جدول الكورسات يحتوي على عمود `price`
//                 'currency' => 'EGP',
//                 'delivery_needed' => 'false',
//                 'merchant_order_ext_ref' => 'ORDER_' . $order->id,
//                 'items' => [
//                     [
//                         'name' => $order->course->title,
//                         'amount_cents' => $order->course->price * 100,
//                         'quantity' => 1,
//                     ]
//                 ],
//             ];

//             $paymobOrder = $this->paymobService->registerOrder($authToken, $orderData, $request->payment_method);
//             if (isset($paymobOrder['error'])) {
//                 return response()->json(['error' => $paymobOrder['error']], 500);
//             }

//             // 4. إنشاء Payment Key
//             $paymentData = [
//                 'amount_cents' => $order->course->price * 100,
//                 'expiration' => time() + 3600,
//                 'order_id' => $paymobOrder['id'],
//                 'billing_data' => [
//                     'email' =>  auth()->guard('api')->user()->email,
//                     'phone_number' =>  auth()->guard('api')->user()->phone,
//                     'first_name' =>  auth()->guard('api')->user()->name,
//                 ],
//             ];

//             $paymentKey = $this->paymobService->generatePaymentKey($authToken, $paymentData, $request->payment_method);

//             // 5. إعادة رابط الدفع للمستخدم
//             return response()->json([
//                 'payment_key' => $paymentKey,
//                 'order_id' => $order->id,
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => $e->getMessage()], 500);
//         }
//     }

//        // دالة لمعالجة رد بايموب (تحديث الحالة بعد الدفع)
//        public function handlePaymentCallback(Request $request)
//        {
//            // تحقق من HMAC
//            $hmac = $request->header('hmac');
//            $computedHmac = hash_hmac('sha512', json_encode($request->all()), config('paymob.hmac'));

//            if ($hmac !== $computedHmac) {
//                return response()->json([
//                 'message' => 'Invalid HMAC'
//             ]);
//            }

//            // تحديث حالة المعاملة بناءً على الرد من بايموب
//            $transaction = PaymobTransaction::where('order_id', $request->order_id)->first();
//            if ($transaction) {
//                $transaction->status = $request->success ? 'successful' : 'failed';
//                $transaction->save();
//            }

//            return response()->json([
//             'message' => 'Transaction updated successfully'
//         ]);
//        }

//        // دالة لمعالجة Webhook من بايموب
//        public function handleWebhook(Request $request)
//        {
//            // تحقق من HMAC
//            $hmac = $request->header('hmac');
//            $computedHmac = hash_hmac('sha512', json_encode($request->all()), config('paymob.hmac'));

//            if ($hmac !== $computedHmac) {
//                return response()->json([
//                 'message' => 'Invalid HMAC'
//             ]);
//            }

//            // معالجة الطلب حسب التفاصيل التي تم إرسالها في الـ Webhook
//            // مثال: تحديث حالة المعاملة بناءً على المعلومات الواردة
//            // يمكنك إضافة أي تفاصيل أو إجراءات حسب حاجتك

//            return response()->json([
//             'message' => 'Webhook received successfully'
//         ]);
//        }


//        public function createPaymentIntent(Request $request)
// {
//     $amount = $request->amount; // قيمة الطلب
//     $currency = 'EGP'; // العملة
//     $billingData = [
//         'apartment' => $request->apartment,
//         'first_name' => $request->first_name,
//         'last_name' => $request->last_name,
//         'street' => $request->street,
//         'phone_number' => $request->phone_number,
//         'email' => $request->email,
//     ];

//     // إضافة طرق الدفع المطلوبة
//     $paymentMethods = [4873707,
//     4871116];

//     try {
//         $response = $this->paymobService->createIntention($amount, $currency, $paymentMethods, $billingData);
//         // dd($response);
//         $paymentLink = $response['payment_keys'][0]['redirection_url'];
//         return response()->json(['payment_link' => $paymentLink]);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }



// }
