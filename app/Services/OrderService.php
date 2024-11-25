<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function createOrder($userId, $courseId, $paymentMethod)
    {
        return Order::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'payment_method' => $paymentMethod,
            'purchase_date' => now(),
            'status' => 'pending',
        ]);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        return $order;
    }
}
