<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Http\Resources\Admin\OrderResource;

class OrderController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Orders = Order::get();
      return response()->json([
          'data' => OrderResource::collection($Orders),
          'message' => "Show All Orders Successfully."
      ]);
  }



  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Order = Order::find($id);

      if (!$Order) {
          return response()->json([
              'message' => "Order not found."
          ]);
      }

      return response()->json([
          'data' =>new OrderResource($Order),
          'message' => "Edit Order By ID Successfully."
      ]);
  }



  public function update(OrderRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Order =Order::findOrFail($id);

     if (!$Order) {
      return response()->json([
          'message' => "Order not found."
      ]);
  }
     $Order->update([
        'user_id' => $request->user_id,
        'Order_id' => $request->Order_id,
        'purchase_date'  => $request-> purchase_date,
        'status' => $request->status,
        'payment_method'=> $request-> payment_method,
      ]);

     $Order->save();
     return response()->json([
      'data' =>new OrderResource($Order),
      'message' => " Update Order By Id Successfully."
  ]);

}

public function pending(string $id)
{
    $this->authorize('manage_users');
    $Order =Order::findOrFail($id);

    if (!$Order) {
     return response()->json([
         'message' => "Order not found."
     ], 404);
 }

    $Order->update(['status' => 'pending']);

    return response()->json([
        'data' => new OrderResource($Order),
        'message' => 'Order has been pending.'
    ]);
}

public function paid(string $id)
{
    $this->authorize('manage_users');
    $Order =Order::findOrFail($id);

    if (!$Order) {
     return response()->json([
         'message' => "Order not found."
     ], 404);
 }

    $Order->update(['status' => 'paid']);

    return response()->json([
        'data' => new OrderResource($Order),
        'message' => 'Order has been Paid.'
    ]);
}

public function canseled(string $id)
{
    $this->authorize('manage_users');
    $Order =Order::findOrFail($id);

    if (!$Order) {
     return response()->json([
         'message' => "Order not found."
     ], 404);
 }

    $Order->update(['status' => 'canseled']);

    return response()->json([
        'data' => new OrderResource($Order),
        'message' => 'Order has been Canseled.'
    ]);
}



  public function destroy(string $id)
  {
      return $this->destroyModel(Order::class, OrderResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$orders=Order::onlyTrashed()->get();
return response()->json([
    'data' =>OrderResource::collection($orders),
    'message' => "Show Deleted Orders Successfully."
]);
}

public function restore(string $id)
{
$this->authorize('manage_users');
$Order = Order::withTrashed()->where('id', $id)->first();
if (!$Order) {
    return response()->json([
        'message' => "Order not found."
    ], 404);
}

$Order->restore();
return response()->json([
    'data' =>new OrderResource($Order),
    'message' => "Restore Order By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Order::class, $id);
  }
}
