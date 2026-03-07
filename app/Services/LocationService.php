<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    public function getCountries(): array
    {
        return Cache::remember('countries', 86400, function () {
            try {
                $response = Http::timeout(10)->get('https://restcountries.com/v3.1/all?fields=name,cca2');
                if ($response->successful()) {
                    $countries = [];
                    foreach ($response->json() as $country) {
                        $countries[$country['cca2']] = $country['name']['common'];
                    }
                    asort($countries);

                    return $countries;
                }

                Log::warning('Countries API returned non-success', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fetch countries from API', ['error' => $e->getMessage()]);
            }

            // Fallback so UI remains usable when the API or network fails
            return $this->localCountryList();
        });
    }

    public function getRegionsByCountry(string $countryCode): array
    {
        return Cache::remember("regions_{$countryCode}", 3600, function () use ($countryCode) {
            $countryName = $this->getCountryName($countryCode);

            // Try Countries Now API first
            try {
                $response = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/states', [
                    'country' => $countryName,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['error'] === false && isset($data['data']['states']) && ! empty($data['data']['states'])) {
                        $regions = array_column($data['data']['states'], 'name');
                        sort($regions);

                        return $regions;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Countries Now API failed for regions', ['country' => $countryCode, 'error' => $e->getMessage()]);
            }

            // Fallback: Try REST Countries API for subdivisions
            try {
                $response = Http::timeout(10)->get("https://restcountries.com/v3.1/alpha/{$countryCode}?fields=subdivisions");

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['subdivisions']) && ! empty($data['subdivisions'])) {
                        $regions = array_keys($data['subdivisions']);
                        sort($regions);

                        return $regions;
                    }
                }
            } catch (\Exception $e) {
                Log::error('REST Countries subdivisions API failed', ['country' => $countryCode, 'error' => $e->getMessage()]);
            }

            return [];
        });
    }

    public function getCitiesByRegion(string $countryCode, string $region): array
    {
        return Cache::remember("cities_{$countryCode}_{$region}", 3600, function () use ($countryCode, $region) {
            try {
                $response = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/state/cities', [
                    'country' => $this->getCountryName($countryCode),
                    'state' => $region,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['error'] === false && isset($data['data'])) {
                        $cities = $data['data'];
                        sort($cities);

                        return array_slice($cities, 0, 50);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Countries Now API failed for cities', ['country' => $countryCode, 'region' => $region, 'error' => $e->getMessage()]);
            }

            return [];
        });
    }

    private function getCountryName(string $countryCode): string
    {
        $countries = $this->getCountries();

        return $countries[$countryCode] ?? $countryCode;
    }

    private function localCountryList(): array
    {
        $path = resource_path('data/countries.json');
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded) && ! empty($decoded)) {
                return $decoded;
            }
        }

        // Minimal hard-coded fallback
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'NZ' => 'New Zealand',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'GH' => 'Ghana',
            'ZA' => 'South Africa',
            'EG' => 'Egypt',
            'SA' => 'Saudi Arabia',
            'AE' => 'United Arab Emirates',
            'IN' => 'India',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'SG' => 'Singapore',
            'PH' => 'Philippines',
            'MY' => 'Malaysia',
            'ID' => 'Indonesia',
            'CN' => 'China',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'BR' => 'Brazil',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'MX' => 'Mexico',
            'ES' => 'Spain',
            'FR' => 'France',
            'DE' => 'Germany',
            'IT' => 'Italy',
            'NL' => 'Netherlands',
        ];
    }
}
