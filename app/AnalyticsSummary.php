<?php

namespace App;
use App\Property;

/**
* NB: Again, with a large number of analytic types and more complex calculations, this will likely be more sophisticated. 
* We might decide to use a dedicated view table to summarise our analytics for example.
* For now just a simple class to keeps the data organised.
*/
class AnalyticsSummary
{
    public $analytic_name, $analytic_id, $analytic_is_numeric, $analytic_units, $properties_measured, $properties_not_measured, $properties_measured_percent, $properties_not_measured_percent;
    public $max = 'NA';
    public $min = 'NA';
    public $median = 'NA';

    public function __construct($analytic_type, $analytics)
    {
        $analytics_num = $analytics->count();
        
        // Total number of filtered properties, which may or may not have a value for this analytic type.
        $filtered_properties_num = Property::withFilters()->count();

        // Calculate summaries, using built in Laravel helpers where possible.
        $this->analytic_id = $analytic_type->id;
        $this->analytic_name = $analytic_type->name;
        $this->analytic_is_numeric = $analytic_type->is_numeric;
        $this->analytic_units = $analytic_type->units;

        if ($analytic_type->is_numeric) {
            $this->max = $analytics->max('value');
            $this->min = $analytics->min('value');
            $this->median = $analytics->median('value');
        }

        $this->properties_measured = $analytics_num; // Filtered properties that have a value for this analytic type
        $properties_without_value = $filtered_properties_num - $analytics_num;
        $this->properties_not_measured = $properties_without_value;
        $this->properties_measured_percent = AnalyticsSummary::getPercentage($analytics_num, $filtered_properties_num);
        $this->properties_not_measured_percent = AnalyticsSummary::getPercentage($properties_without_value, $filtered_properties_num);
    }

    // This would normally go into a Helper class. Kept here for ease of review.
    public static function getPercentage($percentage_of, $in)
    {
        return ($in) ? ($percentage_of / $in) * 100 : 0;
    }

}
