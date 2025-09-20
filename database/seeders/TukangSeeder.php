<?php

namespace Database\Seeders;

use App\Models\Tukang;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TukangSeeder extends Seeder
{
    public function run()
    {
        $tukangs = [
            [
                'name' => 'Ahmad Santoso',
                'email' => 'ahmad.santoso@example.com',
                'phone' => '+62812345678',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta Pusat',
                'postal_code' => '10220',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'specializations' => ['HVAC', 'Electricity'],
                'description' => 'Experienced HVAC and electrical technician with 8 years of experience.',
                'years_experience' => 8,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Pratama',
                'email' => 'budi.pratama@example.com',
                'phone' => '+62823456789',
                'address' => 'Jl. Thamrin No. 45',
                'city' => 'Jakarta Selatan',
                'postal_code' => '12130',
                'latitude' => -6.2297,
                'longitude' => 106.8179,
                'specializations' => ['Plumbing', 'Carpentry'],
                'description' => 'Professional plumber and carpenter. Quality work guaranteed.',
                'years_experience' => 12,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Chandra Wijaya',
                'email' => 'chandra.wijaya@example.com',
                'phone' => '+62834567890',
                'address' => 'Jl. Gatot Subroto No. 78',
                'city' => 'Jakarta Barat',
                'postal_code' => '11460',
                'latitude' => -6.2127,
                'longitude' => 106.7865,
                'specializations' => ['Painting', 'Carpentry'],
                'description' => 'Skilled painter and wood worker with attention to detail.',
                'years_experience' => 6,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi.kurniawan@example.com',
                'phone' => '+62845678901',
                'address' => 'Jl. Rasuna Said No. 234',
                'city' => 'Jakarta Timur',
                'postal_code' => '13550',
                'latitude' => -6.2235,
                'longitude' => 106.8587,
                'specializations' => ['Electricity', 'HVAC', 'Plumbing'],
                'description' => 'Multi-skilled technician. Available for complex repairs.',
                'years_experience' => 15,
                'is_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($tukangs as $tukangData) {
            $tukang = Tukang::create([
                'name' => $tukangData['name'],
                'email' => $tukangData['email'],
                'password' => Hash::make('password'),
                'phone' => $tukangData['phone'],
                'address' => $tukangData['address'],
                'city' => $tukangData['city'],
                'postal_code' => $tukangData['postal_code'],
                'latitude' => $tukangData['latitude'],
                'longitude' => $tukangData['longitude'],
                'specializations' => $tukangData['specializations'],
                'description' => $tukangData['description'],
                'years_experience' => $tukangData['years_experience'],
                'is_available' => $tukangData['is_available'],
                'is_active' => $tukangData['is_active'],
                'email_verified_at' => now(),
            ]);

            // Create portfolios for each tukang
            $this->createPortfolios($tukang);
        }
    }

    private function createPortfolios($tukang)
    {
        $portfolioData = [
            [
                'title' => 'AC Installation & Repair',
                'description' => 'Complete AC installation for a 3-bedroom apartment including maintenance.',
                'cost' => 1500000,
                'duration_days' => 2,
                'completed_at' => now()->subDays(rand(30, 180)),
            ],
            [
                'title' => 'Kitchen Plumbing Renovation',
                'description' => 'Full kitchen plumbing system renovation including new pipes and fixtures.',
                'cost' => 2500000,
                'duration_days' => 5,
                'completed_at' => now()->subDays(rand(30, 180)),
            ],
            [
                'title' => 'Living Room Painting',
                'description' => 'Professional interior painting for living room and dining area.',
                'cost' => 800000,
                'duration_days' => 3,
                'completed_at' => now()->subDays(rand(30, 180)),
            ],
            [
                'title' => 'Custom Wardrobe Construction',
                'description' => 'Built-in wardrobe construction with custom shelving and drawers.',
                'cost' => 3500000,
                'duration_days' => 7,
                'completed_at' => now()->subDays(rand(30, 180)),
            ],
        ];

        // Create 2-3 random portfolios for each tukang
        $selectedPortfolios = collect($portfolioData)->random(rand(2, 3));

        foreach ($selectedPortfolios as $portfolioInfo) {
            $portfolio = Portfolio::create([
                'tukang_id' => $tukang->id, // Changed from handyman_id to tukang_id
                'title' => $portfolioInfo['title'],
                'description' => $portfolioInfo['description'],
                'cost' => $portfolioInfo['cost'],
                'duration_days' => $portfolioInfo['duration_days'],
                'completed_at' => $portfolioInfo['completed_at'],
            ]);

            // Create sample images for portfolio
            for ($i = 1; $i <= rand(2, 4); $i++) {
                PortfolioImage::create([
                    'portfolio_id' => $portfolio->id,
                    'image_path' => 'https://picsum.photos/600/400?random=' . ($portfolio->id * 10 + $i),
                    'alt_text' => $portfolio->title . ' - Image ' . $i,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}