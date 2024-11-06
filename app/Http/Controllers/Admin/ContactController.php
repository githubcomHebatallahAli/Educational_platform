<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactUs;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Contacts = ContactUs::get();
      return response()->json([
          'data' => ContactResource::collection($Contacts),
          'message' => "Show All Contacts Successfully."
      ]);
  }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Contact = ContactUs::find($id);

      if (!$Contact) {
          return response()->json([
              'message' => "Contact not found."
          ]);
      }

      return response()->json([
          'data' =>new ContactResource($Contact),
          'message' => "Edit Contact By ID Successfully."
      ]);
  }


  public function destroy(string $id)
  {
      return $this->destroyModel(ContactUs::class, ContactResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$Contact=ContactUs::onlyTrashed()->get();
return response()->json([
    'data' =>ContactResource::collection($Contact),
    'message' => "Show Deleted Contact Successfully."
]);
}

public function restore(string $id)
{
$this->authorize('manage_users');
$Contact = ContactUs::withTrashed()->where('id', $id)->first();
if (!$Contact) {
    return response()->json([
        'message' => "Contact not found."
    ]);
}

$Contact->restore();
return response()->json([
    'data' =>new ContactResource($Contact),
    'message' => "Restore Contact By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(ContactUs::class, $id);
  }
}
