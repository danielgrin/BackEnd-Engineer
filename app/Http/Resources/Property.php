<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Property extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'guid' => $this->guid,
            'suburb' => $this->suburb,
            'state' => $this->state,
            'country' => $this->country,
            // May or may not be needed by API consumer.
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
