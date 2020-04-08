<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* NB: If we start working with a large number of analytic types, or if they needed more complex calculations, we'd likely consider custom classes.
* I.e some sort of abstraction layer and a library of helpers would be desirable here. For now, lets just keep it simple.
*/

class AnalyticType extends Model
{

    public function analytics()
    {
        return $this->hasMany('App\PropertyAnalytic');
    }

    public static function summariseAnalytics($analytic_type, $analytics)
    {
        return new AnalyticsSummary($analytic_type, $analytics);
    }
}
