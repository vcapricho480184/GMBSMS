<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymService;

class GymServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Personal Training',
                'description' => 'One-on-one training session with a certified fitness trainer. Customized workout plan included.',
                'price' => 800.00,
                'status' => 'available',
            ],
            [
                'name' => 'Yoga Class',
                'description' => 'Group yoga session for flexibility, strength, and mindfulness. All levels welcome.',
                'price' => 350.00,
                'status' => 'available',
            ],
            [
                'name' => 'Zumba Class',
                'description' => 'High-energy dance fitness class. Burn calories while having fun!',
                'price' => 300.00,
                'status' => 'available',
            ],
            [
                'name' => 'Boxing Training',
                'description' => 'Boxing fundamentals and cardio workout with professional boxing trainer.',
                'price' => 500.00,
                'status' => 'available',
            ],
            [
                'name' => 'Nutrition Consultation',
                'description' => 'Personalized diet and nutrition plan by a certified nutritionist.',
                'price' => 1000.00,
                'status' => 'available',
            ],
            [
                'name' => 'Body Composition Analysis',
                'description' => 'Comprehensive body fat, muscle mass, and BMI analysis with detailed report.',
                'price' => 250.00,
                'status' => 'available',
            ],
            [
                'name' => 'Swimming Pool Access',
                'description' => 'Single-day access to the swimming pool facility.',
                'price' => 200.00,
                'status' => 'available',
            ],
            [
                'name' => 'Sauna Session',
                'description' => '30-minute sauna session for relaxation and recovery.',
                'price' => 150.00,
                'status' => 'available',
            ],
        ];

        foreach ($services as $service) {
            GymService::create($service);
        }
    }
}
