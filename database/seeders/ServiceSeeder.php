<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Plumbing',
                'slug' => 'plumbing',
                'description' => 'Professional plumbing services including repairs, installations, and maintenance',
                'icon' => 'bi bi-wrench',
                'color' => 'primary',
                'base_price' => 75.00
            ],
            [
                'name' => 'Electrical',
                'slug' => 'electrical',
                'description' => 'Electrical installations, repairs, and safety inspections',
                'icon' => 'bi bi-lightning',
                'color' => 'success',
                'base_price' => 80.00
            ],
            [
                'name' => 'AC Service',
                'slug' => 'ac-service',
                'description' => 'Air conditioning installation, repair, and maintenance services',
                'icon' => 'bi bi-fan',
                'color' => 'info',
                'base_price' => 90.00
            ],
            [
                'name' => 'Painting',
                'slug' => 'painting',
                'description' => 'Interior and exterior painting services',
                'icon' => 'bi bi-paint-bucket',
                'color' => 'warning',
                'base_price' => 45.00
            ],
            [
                'name' => 'Carpentry',
                'slug' => 'carpentry',
                'description' => 'Custom woodwork, furniture repair, and carpentry services',
                'icon' => 'bi bi-hammer',
                'color' => 'danger',
                'base_price' => 65.00
            ],
            [
                'name' => 'Appliance Repair',
                'slug' => 'appliance-repair',
                'description' => 'Repair services for home appliances',
                'icon' => 'bi bi-tools',
                'color' => 'secondary',
                'base_price' => 70.00
            ],
            [
                'name' => 'Home Maintenance',
                'slug' => 'home-maintenance',
                'description' => 'General home maintenance and repair services',
                'icon' => 'bi bi-house-gear',
                'color' => 'success',
                'base_price' => 55.00
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
