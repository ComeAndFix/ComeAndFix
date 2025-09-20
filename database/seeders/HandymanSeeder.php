<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Handyman;
use App\Models\Service;
use App\Models\HandymanService;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class HandymanSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $services = Service::all();

        // Create dummy users and handymen
        for ($i = 1; $i <= 20; $i++) {
            // Create user
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password')
            ]);

            // Create handyman
            $handyman = Handyman::create([
                'user_id' => $user->id,
                'business_name' => $faker->optional(0.7)->company,
                'bio' => $faker->paragraph(3),
                'phone' => $faker->phoneNumber,
                'experience_years' => $faker->numberBetween(2, 25),
                'rating' => $faker->randomFloat(1, 3.5, 5.0),
                'total_reviews' => $faker->numberBetween(5, 150),
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->state,
                'zip_code' => $faker->postcode,
                'latitude' => $faker->latitude(40.4774, 40.9176), // NYC area
                'longitude' => $faker->longitude(-74.2591, -73.7004),
                'is_verified' => $faker->boolean(80),
                'is_available' => $faker->boolean(90)
            ]);

            // Assign random services to handyman
            $randomServices = $services->random($faker->numberBetween(2, 5));
            foreach ($randomServices as $service) {
                HandymanService::create([
                    'handyman_id' => $handyman->id,
                    'service_id' => $service->id,
                    'custom_rate' => $faker->optional(0.5)->numberBetween($service->base_price - 10, $service->base_price + 30),
                    'description' => $faker->optional(0.7)->sentence
                ]);
            }

            // Create portfolio items
            for ($j = 1; $j <= $faker->numberBetween(2, 6); $j++) {
                $portfolio = Portfolio::create([
                    'handyman_id' => $handyman->id,
                    'title' => $faker->sentence(3),
                    'description' => $faker->paragraph(2),
                    'cost' => $faker->optional(0.8)->numberBetween(100, 5000),
                    'duration_days' => $faker->optional(0.7)->numberBetween(1, 30),
                    'completed_at' => $faker->optional(0.9)->dateTimeBetween('-2 years', 'now')
                ]);

                // Add portfolio images
                for ($k = 1; $k <= $faker->numberBetween(1, 4); $k++) {
                    PortfolioImage::create([
                        'portfolio_id' => $portfolio->id,
                        'image_path' => 'https://picsum.photos/400/300?random=' . ($i * 10 + $j * 5 + $k),
                        'alt_text' => $portfolio->title . ' - Image ' . $k,
                        'sort_order' => $k
                    ]);
                }
            }
        }
    }
}
