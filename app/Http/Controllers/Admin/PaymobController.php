<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
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
    $user = auth()->guard('api')->user();
    if (!$user) {
        return response()->json([
            'error' => 'User not authenticated.'
        ]);
    }

    $integrationIds = $request->input('payment_methods', []);
    $selectedIndex = $request->input('selected_method_index', 0);

    if (!is_numeric($selectedIndex) || !isset($integrationIds[$selectedIndex])) {
        return response()->json([
            'error' => 'Invalid selected_method_index. Please choose a valid index.',
        ]);
    }

    $paymentMethodId = $integrationIds[$selectedIndex];
    $paymentMethod = PaymobMethod::where('integration_id', $paymentMethodId)->first();

    if (!$paymentMethod) {
        return response()->json([
            'error' => 'Invalid payment method ID (integration_id).',
        ]);
    }


    $course = Course::find($request->input('course_id'));
    if (!$course) {
        return response()->json([
            'error' => 'Course not found.',
        ]);
    }


    $priceInCents = $course->price * 100;
    $billingData = $request->input('billing_data', [
        'first_name' => $user->name ?? 'Unknown',
        'last_name' => 'N/A',
        'email' => $user->email ?? 'Unknown',
        'phone_number' => $user->studentPhoNum ?? 'Unknown',
    ]);

    $data = [
        "amount" => $priceInCents,
        "currency" => $request->input('currency', 'EGP'),
        "billing_data" => $billingData,
        "payment_methods" => $integrationIds,
        "items" => [
            [
                'name' => $course->nameOfCourse,
                'amount' => $priceInCents,
                'description' => $course->description,
            ],
        ],
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

            $paymobOrderId = $response->json()['intention_order_id']; // استخدم المفتاح الصحيح

            $transaction = PaymobTransaction::create([
                'special_reference' => $data['special_reference'],
                'paymob_order_id' => $paymobOrderId, // تخزين الـ Order ID
                'payment_method_id' => $paymentMethod->id,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'price' => $course->price,
                'currency' => $data['currency'],
                'status' => 'pending',
            ]);

            return response()->json([
                'transaction' => $transaction,
                'response' => $response->json(),
            ]);
        } else {
            return response()->json([
                'error' => 'Request failed',
                'details' => $response->json(),
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function generateCheckoutUrl(Request $request)
{
    $publicKey = $request->input('public_key');
    $clientSecret = $request->input('client_secret');

    $checkoutUrl = "https://accept.paymob.com/unifiedcheckout/?publicKey={$publicKey}&clientSecret={$clientSecret}";

    return response()->json(['checkout_url' => $checkoutUrl]);
}


public function postPayment(Request $request)
{
    $integrationId = $request->input('integration_id');
    $orderId = $request->input('order_id');
    $clientSecret = $request->input('client_secret');
    $intentionOrderId = $request->input('intention_order_id');

    $publicKey = config('paymob.public_key');

    return response()->json([
        'integration_id' => $integrationId,
        'order_id' => $orderId,
        'client_secret' => $clientSecret,
        'intention_order_id' => $intentionOrderId,
        'public_key' => $publicKey,
    ]);
}
}


