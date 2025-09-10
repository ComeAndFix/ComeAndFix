<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Handyman;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function show($slug, Request $request)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        // Get user's location (you can also use IP geolocation)
        $latitude = $request->query('lat');
        $longitude = $request->query('lng');

        // Default to a central location if no coordinates provided
        if (!$latitude || !$longitude) {
            $latitude = 40.7128; // New York City
            $longitude = -74.0060;
        }

        // Find handymen who offer this service, ordered by distance
        $handymen = Handyman::whereHas('services', function($query) use ($service) {
            $query->where('service_id', $service->id);
        })
            ->where('is_available', true)
            ->with(['services', 'portfolios.images', 'user'])
            ->get()
            ->map(function($handyman) use ($latitude, $longitude) {
                $handyman->distance = $handyman->getDistanceFrom($latitude, $longitude);
                return $handyman;
            })
            ->sortBy('distance')
            ->take(10);

        return view('services.show', compact('service', 'handymen', 'latitude', 'longitude'));
    }
}
