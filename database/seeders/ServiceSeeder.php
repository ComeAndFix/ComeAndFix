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
                'name' => 'Plumbing Services',
                'description' => 'Professional plumbing services including pipe repair, installation, leak fixing, and water system maintenance.',
                'icon' => 'bi-water',
                'color' => '#007bff',
                'base_price' => 150000,
            ],
            [
                'name' => 'Electrical Services',
                'description' => 'Complete electrical services including wiring, installation of outlets, switches, lighting, and electrical repairs.',
                'icon' => 'bi-lightning-charge',
                'color' => '#ffc107',
                'base_price' => 200000,
            ],
            [
                'name' => 'Air Conditioning Service',
                'description' => 'AC installation, repair, maintenance, cleaning, and refrigerant refilling services.',
                'icon' => 'bi-thermometer-snow',
                'color' => '#17a2b8',
                'base_price' => 180000,
            ],
            [
                'name' => 'Carpentry',
                'description' => 'Custom furniture making, wood repair, cabinet installation, and general carpentry work.',
                'icon' => 'bi-hammer',
                'color' => '#8B4513',
                'base_price' => 250000,
            ],
            [
                'name' => 'Painting Services',
                'description' => 'Interior and exterior painting, wall preparation, color consultation, and decorative finishes.',
                'icon' => 'bi-brush',
                'color' => '#e83e8c',
                'base_price' => 120000,
            ],
            [
                'name' => 'Tile Installation',
                'description' => 'Professional tile installation for floors, walls, bathrooms, and kitchens including grouting and finishing.',
                'icon' => 'bi-grid-3x3',
                'color' => '#6c757d',
                'base_price' => 300000,
            ],
            [
                'name' => 'Home Cleaning',
                'description' => 'Deep cleaning services for homes including bathrooms, kitchens, floors, and general housekeeping.',
                'icon' => 'bi-house-check',
                'color' => '#28a745',
                'base_price' => 100000,
            ],
            [
                'name' => 'Garden Maintenance',
                'description' => 'Lawn mowing, plant care, garden design, pruning, and landscape maintenance services.',
                'icon' => 'bi-tree',
                'color' => '#198754',
                'base_price' => 150000,
            ],
            [
                'name' => 'Appliance Repair',
                'description' => 'Repair services for washing machines, refrigerators, microwaves, and other household appliances.',
                'icon' => 'bi-gear',
                'color' => '#fd7e14',
                'base_price' => 175000,
            ],
            [
                'name' => 'Roofing Services',
                'description' => 'Roof repair, installation, leak fixing, gutter cleaning, and roof maintenance services.',
                'icon' => 'bi-house-up',
                'color' => '#dc3545',
                'base_price' => 400000,
            ],
            [
                'name' => 'Masonry Work',
                'description' => 'Brick laying, concrete work, stone installation, wall construction, and structural repairs.',
                'icon' => 'bi-bricks',
                'color' => '#6f42c1',
                'base_price' => 350000,
            ],
            [
                'name' => 'Furniture Assembly',
                'description' => 'Professional assembly of IKEA and other furniture, TV mounting, and shelf installation.',
                'icon' => 'bi-tools',
                'color' => '#20c997',
                'base_price' => 80000,
            ],
            [
                'name' => 'Welding Services',
                'description' => 'Metal welding, gate repair, railing installation, and custom metalwork services.',
                'icon' => 'bi-fire',
                'color' => '#ff6b35',
                'base_price' => 220000,
            ],
            [
                'name' => 'Pest Control',
                'description' => 'Professional pest control services for termites, ants, cockroaches, and other household pests.',
                'icon' => 'bi-bug',
                'color' => '#495057',
                'base_price' => 130000,
            ],
            [
                'name' => 'Lock & Security',
                'description' => 'Lock installation, repair, key duplication, and home security system installation.',
                'icon' => 'bi-key',
                'color' => '#343a40',
                'base_price' => 160000,
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
