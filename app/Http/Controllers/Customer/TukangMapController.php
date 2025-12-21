<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tukang;
use App\Models\Service;
use Illuminate\Http\Request;

class TukangMapController extends Controller
{
    public function index(Request $request)
    {
        $serviceType = $request->get('service_type');
        $customer = auth()->guard('customer')->user();
        
        return view('customer.tukang-map', compact('serviceType', 'customer'));
    }

    public function getTukangs(Request $request)
    {
        $serviceType = $request->get('service_type');
        $userLat = $request->get('lat');
        $userLng = $request->get('lng');
        $radius = $request->get('radius', 50); // Default 50km radius

        $query = Tukang::where('is_available', true)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($serviceType) {
            $query->whereJsonContains('specializations', $serviceType);
        }

        $tukangs = $query->get();

        // Filter by distance if user location is provided
        if ($userLat && $userLng) {
            $tukangs = $tukangs->filter(function ($tukang) use ($userLat, $userLng, $radius) {
                $distance = $tukang->getDistanceFrom($userLat, $userLng);
                return $distance !== null && $distance <= $radius;
            });
        }

        // Add distance and average price to each tukang
        $tukangs = $tukangs->map(function ($tukang) use ($userLat, $userLng) {
            $tukang->distance = ($userLat && $userLng) ? $tukang->getDistanceFrom($userLat, $userLng) : null;
            
            // Calculate average price from portfolios
            $tukang->load('portfolios');
            $portfolioCosts = $tukang->portfolios->pluck('cost')->filter();
            $tukang->average_price = $portfolioCosts->count() > 0 ? $portfolioCosts->avg() : 0;
            
            return $tukang;
        });

        return response()->json($tukangs);
    }

    public function show(Tukang $tukang)
    {
        $tukang->load(['portfolios.images']);
        return response()->json($tukang);
    }

    public function showProfile($id, Request $request)
    {
        $tukang = Tukang::with([
            'services',
            'portfolios.images',
            'tukangServices.service'
        ])->findOrFail($id);

        $serviceId = $request->query('service_id');
        $selectedService = null;

        if ($serviceId) {
            $selectedService = Service::find($serviceId);
        }

        return view('customer.tukang-profile', compact('tukang', 'selectedService'));
    }
}
