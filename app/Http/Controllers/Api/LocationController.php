<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private LocationService $locationService) {}

    public function countries()
    {
        $countries = $this->locationService->getCountries();

        // Convert associative array to array of [code => name] for JavaScript
        return response()->json($countries);
    }

    public function regions(Request $request)
    {
        $request->validate(['country' => 'required|string']);

        $regions = $this->locationService->getRegionsByCountry($request->country);

        return response()->json($regions);
    }

    public function cities(Request $request)
    {
        $request->validate([
            'country' => 'required|string',
            'region' => 'required|string',
        ]);

        return response()->json(
            $this->locationService->getCitiesByRegion($request->country, $request->region)
        );
    }
}
