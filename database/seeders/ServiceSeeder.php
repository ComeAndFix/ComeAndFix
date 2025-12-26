<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Plumbing',
                'description' => 'Professional plumbing services including pipe repair, installation, leak fixing, and water system maintenance.',
                'icon' => 'bi-water',
                'color' => '#007bff',
                'base_price' => 150000,
            ],
            [
                'name' => 'Electricity',
                'description' => 'Complete electrical services including wiring, installation of outlets, switches, lighting, and electrical repairs.',
                'icon' => 'bi-lightning-charge',
                'color' => '#ffc107',
                'base_price' => 200000,
            ],
            [
                'name' => 'HVAC/AC',
                'description' => 'AC and HVAC installation, repair, maintenance, cleaning, and refrigerant refilling services.',
                'icon' => 'bi-thermometer-snow',
                'color' => '#17a2b8',
                'base_price' => 180000,
            ],
            [
                'name' => 'Painting',
                'description' => 'Interior and exterior painting, wall preparation, color consultation, and decorative finishes.',
                'icon' => 'bi-brush',
                'color' => '#e83e8c',
                'base_price' => 120000,
            ],
            [
                'name' => 'Appliance Repair',
                'description' => 'Repair services for washing machines, refrigerators, microwaves, and other household appliances.',
                'icon' => 'bi-gear',
                'color' => '#fd7e14',
                'base_price' => 175000,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'name' => $service['name'],
                'slug' => Str::slug($service['name']),
                'description' => $service['description'],
                'icon' => $service['icon'],
                'color' => $service['color'],
                'base_price' => $service['base_price'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
