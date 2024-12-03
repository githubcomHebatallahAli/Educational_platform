<?php

namespace App\Http\Controllers\Admin;




use App\Models\PaymobMethod;
use Illuminate\Http\Request;
use App\Models\PaymobTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

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

public function getPaymobSecretKey(Request $request)
{
    $this->authorize('manage_users');

    return response()->json([
        'secret_key' => config('paymob.secret_key'),
    ]);
}


// public function createIntention(Request $request)
// {

//     $data = [
//         "amount" => $request->input('amount'),
//         "currency" => $request->input('currency', 'EGP'),
//         "billing_data" => $request->input('billing_data'),
//         "payment_methods" => $request->input('payment_methods', []),
//         "items" => $request->input('items', []),
//         "special_reference" => $request->input('special_reference'),
//         "expiration" => $request->input('expiration', 3600),
//         "notification_url" => $request->input('notification_url'),
//         "redirection_url" => $request->input('redirection_url'),
//     ];

//     try {
//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . config('paymob.secret_key'),
//             'Content-Type' => 'application/json',
//         ])->post('https://accept.paymob.com/v1/intention/', $data);

//         if ($response->successful()) {
//             return response()->json($response->json());
//         } else {
//             return response()->json([
//                 'error' => 'Request failed',
//                 'details' => $response->json()
//             ], 400);
//         }

//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage()], 500);
//     }
// }


public function createIntention(Request $request)
{
    $integrationIds = $request->input('payment_methods', []); // مصفوفة طرق الدفع
    $selectedIndex = $request->input('selected_method_index', 0); // الفهرس الافتراضي هو 0

    // التحقق من صحة الفهرس
    if (!is_numeric($selectedIndex) || !isset($integrationIds[$selectedIndex])) {
        return response()->json([
            'error' => 'Invalid selected_method_index. Please choose a valid index.',
        ], 400);
    }

    // استخراج القيمة بناءً على الفهرس
    $paymentMethodId = $integrationIds[$selectedIndex];

    // التحقق من وجود integration_id في قاعدة البيانات
    $paymentMethod = PaymobMethod::where('integration_id', $paymentMethodId)->first();

    if (!$paymentMethod) {
        return response()->json([
            'error' => 'Invalid payment method ID (integration_id).',
        ], 400);
    }

    $data = [
        "amount" => $request->input('amount'),
        "currency" => $request->input('currency', 'EGP'),
        "billing_data" => $request->input('billing_data'),
        "payment_methods" => $integrationIds,
        "items" => $request->input('items', []),
        "special_reference" => uniqid('ref_', true),
        "expiration" => $request->input('expiration', 3600),
        "notification_url" => $request->input('notification_url'),
        "redirection_url" => $request->input('redirection_url'),
    ];

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('paymob.secret_key'),
            'Content-Type' => 'application/json',
        ])->post('https://accept.paymob.com/v1/intention/', $data);

        if ($response->successful()) {
            // إنشاء المعاملة
            $transaction = PaymobTransaction::create([
                'special_reference' => $data['special_reference'],
                'paymob_order_id' => $response->json()['id'],
                'payment_method_id' => $paymentMethod->id, // استخدام الـ id الخاص بطريقة الدفع
                'user_id' => $request->user()->id ?? null,
                'price' => $data['amount'],
                'currency' => $data['currency'],
                'status' => 'pending', // سيتم تحديث الحالة بعد الدفع
            ]);

            return response()->json([
                'transaction' => $transaction,
                'response' => $response->json(),
            ]);
        } else {
            return response()->json([
                'error' => 'Request failed',
                'details' => $response->json(),
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}




public function postPayment(Request $request)
{
    $data = [
        "integration_id" => $request->input('integration_id'),
        "order_id" => $request->input('order_id'),
        "client_secret" => $request->input('client_secret'),
        "intention_order_id" => $request->input('intention_order_id'),
    ];

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('paymob.secret_key'), // استخدام التوكن السري
            'Content-Type' => 'application/json',
        ])->post('https://accept.paymobsolutions.com/api/acceptance/post_pay', $data);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'Request failed',
                'details' => $response->json()
            ], 400);
        }

    } catch (\Exception $e) {
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
