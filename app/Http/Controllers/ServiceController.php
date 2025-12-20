<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Tukang;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function show($slug, Request $request)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        
        $latitude = $request->get('lat', -6.2088); // Default to Jakarta
        $longitude = $request->get('lng', 106.8456);

        // Get tukangs that offer this service
        $tukangs = Tukang::whereJsonContains('specializations', $service->name)
            ->where('is_available', true)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($tukang) use ($latitude, $longitude) {
                $tukang->distance = $tukang->getDistanceFrom($latitude, $longitude);
                return $tukang;
            })
            ->sortBy('distance')
            ->take(10);

        return view('services.show', compact('service', 'tukangs', 'latitude', 'longitude'));
    }
}
