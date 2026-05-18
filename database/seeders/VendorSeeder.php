<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRating;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $categories = ['catering', 'photography', 'decoration', 'music', 'florist', 'venue', 'makeup', 'transport', 'wedding planner'];
        $cities = ['Mumbai, Maharashtra', 'Delhi NCR', 'Bengaluru, Karnataka', 'Jaipur, Rajasthan', 'Goa', 'Udaipur, Rajasthan', 'Hyderabad, Telangana', 'Pune, Maharashtra'];
        $images = [
            'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505236858219-8359eb29e329?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=1200&q=80',
        ];

        for ($i = 1; $i <= 110; $i++) {
            $category = $faker->randomElement($categories);
            $priceMin = $faker->numberBetween(5000, 180000);
            $vendor = Vendor::create([
                'user_id' => $i === 1 ? (string) User::where('email', 'vendor@eventra.test')->first()?->getKey() : null,
                'business_name' => $faker->company().' '.str($category)->headline(),
                'category' => $category,
                'description' => $faker->sentence(18).' Known for polished execution, fast communication, and luminous event styling.',
                'price_min' => $priceMin,
                'price_max' => $priceMin + $faker->numberBetween(25000, 320000),
                'location' => $faker->randomElement($cities),
                'rating' => $faker->randomFloat(1, 3.7, 5),
                'total_reviews' => $faker->numberBetween(12, 480),
                'image_url' => $faker->randomElement($images),
                'gallery' => $faker->randomElements($images, 3),
                'phone' => '+91 '.$faker->numerify('9#########'),
                'email' => $faker->companyEmail(),
                'availability_json' => collect(range(1, 8))->map(fn () => [
                    'date' => now()->addDays($faker->numberBetween(3, 120))->toDateString(),
                    'status' => $faker->randomElement(['available', 'limited', 'booked']),
                ])->values()->all(),
            ]);

            foreach (range(1, 3) as $review) {
                VendorRating::create([
                    'vendor_id' => (string) $vendor->getKey(),
                    'user_id' => null,
                    'rating' => $faker->numberBetween(4, 5),
                    'review_text' => $faker->sentence(14),
                ]);
            }
        }
    }
}
