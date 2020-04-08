<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAnalytic extends JsonResource
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
            'analytic_type_id' => $this->analyticType->id,
            'name' => $this->analyticType->name,
            'value' => $this->value,
            'units' => $this->analyticType->units,
            'num_decimal_place' => $this->analyticType->num_decimal_place,
            'is_numeric' => $this->analyticType->is_numeric,
            // May or may not be needed by API consumer.
            // 'created_at' => $this->created_at, 
            // 'updated_at' => $this->updated_at,
        ];
    }
}