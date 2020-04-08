<?php

use App\AnalyticType;
use App\Http\Resources\AnalyticsSummary as AnalyticsSummary;
use App\Http\Resources\AnalyticsSummaryCollection as AnalyticsSummaryCollection;
use App\Http\Resources\PropertyAnalyticCollection as PropertyAnalyticCollection;
use App\Http\Resources\Property as PropertyResource;
use App\Http\Resources\PropertyCollection as PropertyCollection;
use App\Property;
use App\PropertyAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
* NB: As mentioned in Readme, most of the route code here would be contained in custom controllers, which would be setup to cover all REST verbs.
* For brevity and to minimise boilerplate code in this review, everything is done here or in our models.
*/
Route::middleware('throttle:60,1')->group(function () {

    // All properties, filterable by suburb/state/country (as url queries), e.g analytics/summary?suburb=ryde&state=NSW&country=Australia.
    // NB: see Readme file for comments about enforcing these queries.
    Route::get('properties', function () {
        // Simply wrap response in resource collections, to allow for useful JSON response objects.
        return new PropertyCollection(Property::withFilters()->get());
    });

    // Property by id.
    Route::get('properties/{id}', function ($id) {
        return new PropertyResource(App\Property::findOrFail($id));
    });

    // Post new property. NB: Refer to readme for comments on mass assginment.
    Route::post('properties', function () {
        $property = new Property(request()->all());
        $property->guid = Str::uuid();
        return ($property->save()) ? 'ok' : 'not ok'; // NB: Refer to JSON response in Readme
    });

    // Get analytics by property id.
    Route::get('properties/{id}/analytics', function ($property_id) {
        $property = App\Property::with('analytics')->where('id', $property_id)->firstOrFail();
        return new PropertyAnalyticCollection($property->analytics);
    });

    // Update or create new analytic for a property, by analytic_type_id and property_id.
    Route::match(['post', 'put'], 'properties/{id}/analytics', function ($property_id) {
        $property_analytic = App\PropertyAnalytic::updateOrCreate(
            ['analytic_type_id' => request()->input('analytic_type_id'),
                'property_id' => $property_id],
            ['value' => request()->input('value')]
        );
        return ($property_analytic) ? 'ok' : 'not ok';
    });


    // Get summary of analytics by all types, filtered by URL queries.
    Route::get('analytic_types/analytics/summary', function () {

        if (!Property::withFilters()->count()) {
            return []; //Quick Exit. Nothing to show.
        }

        /**
        * NB: As discussed in the Readme, we are summarising analytics by each analytic type.
        * Profiling information and scaling / caching considerations will better inform a practical approach.
        */

        $collection = collect();
        foreach (AnalyticType::all() as $type) {
            $collection->push(PropertyAnalytic::getSummaryByType($type));
        }
        return new AnalyticsSummaryCollection($collection);
    });

    // Get summary of analytics by type.
    Route::get('analytic_types/{id?}/analytics/summary', function ($id) {

        if (!Property::withFilters()->count()) {
            return []; //Quick Exit. Nothing to show.
        }

        $analytic_type = AnalyticType::findOrFail($id);

        return new AnalyticsSummary(PropertyAnalytic::getSummaryByType($analytic_type));
    });
    
    
});
