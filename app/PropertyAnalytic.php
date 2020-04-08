<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyAnalytic extends Model
{
    public function property()
    {
        return $this->belongsTo('App\Property', 'property_id');
    }

    public function analyticType()
    {
        return $this->belongsTo('App\AnalyticType', 'analytic_type_id');
    }

    protected $fillable = [
        'property_id',
        'analytic_type_id',
        'value',
    ];

    public static function getSummaryByType($analytic_type)
    {
        /**
        * Fetch analytic values for this analytic type, filtered by properties that use it.
        * This will also show orphan analytic types (i.e those not measured on any property).
        */
        $analytics = PropertyAnalytic::where('analytic_type_id', $analytic_type->id)
            ->whereHas('property', function ($query) {
                $query->withFilters();
            })->get();

        return AnalyticType::summariseAnalytics($analytic_type, $analytics);
    }

}
