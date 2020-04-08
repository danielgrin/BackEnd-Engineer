<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
    * NB: As mentioned in Readme, we normally wouldn't allow mass assignment by default, but would rather implement more robust (and safe) validation.
    */
    protected $fillable = [
        'suburb',
        'state',
        'city',
        'country',
    ];

    public function analytics()
    {
        return $this->hasMany('App\PropertyAnalytic');
    }


    /**
    * Use scopes for filtering property records.
    * This function makes it easy to add URL queries / filters.
    * By adding a new scope function below (e.g function scopeByPlanet), the final word of the function name will be available as url.com?planet=value.
    * We'd likely make this global (base class, traits?) to allow on any/all models.
    */
    public function scopeWithFilters($query)
    {
        foreach (request()->query() as $k => $v) {
            $filter_function = 'scopeBy' . ucfirst($k);
            if (method_exists($this, $filter_function)) {
                $query = $this->$filter_function($query, $v);
            }
        }
        return $query;
    }

    public function scopeBySuburb($query, $suburb)
    {
        return $query->where('suburb', $suburb);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

}
