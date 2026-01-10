<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class LocationCategorizationService
{
    /**
     * Group users by country
     */
    public function groupByCountry(): Collection
    {
        return User::selectRaw('country, COUNT(*) as user_count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('user_count')
            ->get();
    }

    /**
     * Group users by region within a country
     */
    public function groupByRegion(string $country = null): Collection
    {
        $query = User::selectRaw('country, region, COUNT(*) as user_count')
            ->whereNotNull('region');
            
        if ($country) {
            $query->where('country', $country);
        }
        
        return $query->groupBy('country', 'region')
            ->orderBy('country')
            ->orderByDesc('user_count')
            ->get();
    }

    /**
     * Group users by city within a region
     */
    public function groupByCity(string $country = null, string $region = null): Collection
    {
        $query = User::selectRaw('country, region, city, COUNT(*) as user_count')
            ->whereNotNull('city');
            
        if ($country) {
            $query->where('country', $country);
        }
        
        if ($region) {
            $query->where('region', $region);
        }
        
        return $query->groupBy('country', 'region', 'city')
            ->orderBy('country')
            ->orderBy('region')
            ->orderByDesc('user_count')
            ->get();
    }

    /**
     * Get complete location hierarchy with user counts
     */
    public function getLocationHierarchy(): array
    {
        return User::selectRaw('country, region, city, COUNT(*) as user_count')
            ->whereNotNull('country')
            ->groupBy('country', 'region', 'city')
            ->orderBy('country')
            ->orderBy('region')
            ->orderBy('city')
            ->get()
            ->groupBy('country')
            ->map(function ($countryUsers, $country) {
                return [
                    'country' => $country,
                    'total_users' => $countryUsers->sum('user_count'),
                    'regions' => $countryUsers->groupBy('region')->map(function ($regionUsers, $region) {
                        return [
                            'region' => $region,
                            'total_users' => $regionUsers->sum('user_count'),
                            'cities' => $regionUsers->map(function ($cityData) {
                                return [
                                    'city' => $cityData->city,
                                    'user_count' => $cityData->user_count
                                ];
                            })->values()
                        ];
                    })->values()
                ];
            })->values()->toArray();
    }

    /**
     * Get top locations by user count
     */
    public function getTopLocations(int $limit = 10): Collection
    {
        return User::selectRaw('CONCAT(city, ", ", region, ", ", country) as location, COUNT(*) as user_count')
            ->whereNotNull('country')
            ->whereNotNull('region')
            ->whereNotNull('city')
            ->groupByRaw('city, region, country')
            ->orderByDesc('user_count')
            ->limit($limit)
            ->get();
    }
}