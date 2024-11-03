<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    //     return [
    //         'id' => $this->id,
    //         'message_id' => $this->message_id,
    //         'type' => $this->type,
    //         'reactable' => [
    //             'id' => $this->reactable_id,
    //             'type' => class_basename($this->reactable_type),
    //         ],
    //         'created_at' => $this->created_at->format('Y-m-d H:i:s'),
    //     ];
    // }



    return [
        'id' => $this->id,
        'message_id' => $this->message_id,
        'type' => $this->type,
        'reactable' => [
            'id' => $this->reactable_id,
            'type' => class_basename($this->reactable_type),
            'name' => $this->getReactableName(),
            'email' => $this->getReactableEmail(),
        ],
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
    ];
}

/**
 * Get the name of the reactable person.
 *
 * @return string|null
 */
private function getReactableName()
{
    if ($this->reactable_type === 'App\Models\Admin') {
        return \App\Models\Admin::find($this->reactable_id)?->name;
    } elseif ($this->reactable_type === 'App\Models\Parnt') {
        return \App\Models\Parnt::find($this->reactable_id)?->name;
    } elseif ($this->reactable_type === 'App\Models\User') {
        return \App\Models\User::find($this->reactable_id)?->name;
    }

    return null;
}

/**
 * Get the email of the reactable person.
 *
 * @return string|null
 */
private function getReactableEmail()
{
    if ($this->reactable_type === 'App\Models\Admin') {
        return \App\Models\Admin::find($this->reactable_id)?->email;
    } elseif ($this->reactable_type === 'App\Models\Parnt') {
        return \App\Models\Parnt::find($this->reactable_id)?->email;
    } elseif ($this->reactable_type === 'App\Models\User') {
        return \App\Models\User::find($this->reactable_id)?->email;
    }

    return null;
}
}
