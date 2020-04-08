<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsSummary extends JsonResource
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
            'analytic_type_id' => $this->analytic_id,
            'name' => $this->analytic_name,
            'is_numeric' => $this->analytic_is_numeric,
            'units' => $this->analytic_units,
            'max' => $this->max,
            'min' => $this->min,
            'median' => $this->median,
            'properties_measured' => $this->properties_measured,
            'properties_not_measured' => $this->properties_not_measured,
            'properties_measured_percent' => $this->properties_measured_percent,
            'properties_not_measured_percent' => $this->properties_not_measured_percent,
        ];
    }
}