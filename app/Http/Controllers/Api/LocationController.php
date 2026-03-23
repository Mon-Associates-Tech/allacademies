<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function __construct(private LocationService $locationService) {}

    public function countries()
    {
        $countries = $this->locationService->getCountries();

        // Convert associative array to array of [code => name] for JavaScript
        return response()->json($countries);
    }

    public function detectCountry(Request $request)
    {
        // Get user's IP address
        $ip = $request->ip();
        
        // Try to detect country from IP using a free service
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['countryCode'])) {
                    return response()->json([
                        'country_code' => strtolower($data['countryCode']),
                        'country' => $data['country'] ?? null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
        
        // Default to Nigeria
        return response()->json(['country_code' => 'ng', 'country' => 'Nigeria']);
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
