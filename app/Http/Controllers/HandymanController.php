<?php

namespace App\Http\Controllers;

use App\Models\Handyman;
use App\Models\Service;
use Illuminate\Http\Request;

class HandymanController extends Controller
{
    public function show($id, Request $request)
    {
        $handyman = Handyman::with([
            'user',
            'services',
            'portfolios.images',
            'handymanServices.service'
        ])->findOrFail($id);

        $serviceId = $request->query('service_id');
        $selectedService = null;

        if ($serviceId) {
            $selectedService = Service::find($serviceId);
        }

        return view('handymen.show', compact('handyman', 'selectedService'));
    }
}
