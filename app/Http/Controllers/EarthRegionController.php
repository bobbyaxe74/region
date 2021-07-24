<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Http\Requests\EarthRegionControllerRequests\EarthRegionSortRequest;
use App\Http\Requests\EarthRegionControllerRequests\EarthRegionIndexRequest;
use Illuminate\Http\Request;

class EarthRegionController extends Controller
{
    protected $earth_regions;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() 
    {
        // Retrieve all earth regions from file and store in memory
        $this->earth_regions = Cache::rememberForever('EARTH_REGIONS', function () {

            $contents = file_get_contents('../dependencies/countries.json');
            $country_list = collect(json_decode($contents, true))->collapse();

            $contents = file_get_contents('../dependencies/states.json');
            $state_list = collect(json_decode($contents, true))->collapse();

            $contents = file_get_contents('../dependencies/cities.json');
            $city_list = collect(json_decode($contents, true))->collapse();

            $countries = $country_list->keyBy('id');
            $states = $state_list->groupBy('country_id');
            $cities = $city_list->groupBy('state_id');

            // Map through all the countries, adding its states and cities
            $all = $countries->map(function ($country) use ($states, $cities) {

                return array_merge (
                    $country, ['states' => collect($states->get($country['id']))->map(function ($state) use ($cities) {

                        return array_merge (
                            $state, ['cities' => collect($cities->get($state['id']))]
                        );
                    })]
                );
            });

            // Categories
            return [
                'countries' => $country_list->toArray(),
                'states' => $state_list->toArray(),
                'cities' => $city_list->toArray(),
                'all' => $all->toArray()
            ];
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->input('properties')){

            // Get all regions by category with all their properties
            $regions = collect(
                $this->earth_regions[$request->input('category')]
            );

        } else {

            // Get all regions by category with out their relations
            $regions = collect(
                $this->earth_regions[$request->input('category')]
            )->pluck('name','id');
        }

        // Return success
        if ($regions) {

            if (count($regions) > 0) {
                return $this->success($regions);
            } else {
               return $this->noContent('Specified category was found for this region');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sortIndex(Request $request)
    {
        $country_id = is_null($request->input('country_id'))? false : $request->input('country_id');
        $state_id = is_null($request->input('state_id'))? false : $request->input('state_id');
        $city_id = is_null($request->input('city_id'))? false : $request->input('city_id');

        // Build search query
        $regions = collect($this->earth_regions['all'])->when($country_id, function ($collection, $country_id) {
            
            // Find the given country by id
            $collection = collect($collection->get($country_id));
            return $collection;

        })->when($state_id, function ($collection, $state_id) {

            // Find the given state by id
            $collection = collect($collection->get('states'));
            return collect($collection->firstWhere('id', $state_id));

        })->when($city_id, function ($collection, $city_id) {

            // Find the given city by id
            $collection = collect($collection->get('cities'));
            return collect($collection->firstWhere('id', $city_id));

        })->whenEmpty(function ($collection) use ($state_id, $city_id) {

            return $collection->when($state_id && $city_id, function($collection)  use ($state_id, $city_id){

                // Find the given state and city by id
                $collection = collect($this->earth_regions['all'])->pluck('states')->flatten(1);
                $collection = collect($collection->firstWhere('id', $state_id))->get('cities');
                return collect($collection->firstWhere('id', $city_id));

            })->when($state_id && !$city_id, function($collection)  use ($state_id, $city_id){

                // Find the given state by id
                $collection = collect($this->earth_regions['all'])->pluck('states')->flatten(1);
                return collect($collection->firstWhere('id', $state_id));

            })->when(!$state_id && $city_id, function($collection)  use ($state_id, $city_id){

                // Find the given city by id
                $collection = collect($this->earth_regions['all'])->pluck('states.*.cities')->flatten(2);
                return collect($collection->firstWhere('id', $city_id));

            });

        });

        // Return success
        if ($regions) {
            if ($regions->isNotEmpty()) {
                return $this->success($regions);
            } else {
               return $this->noContent('No specified constrain was found for this region');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }
}