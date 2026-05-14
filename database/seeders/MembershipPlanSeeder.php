<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'Access to basic gym equipment and facilities. Perfect for beginners who want to start their fitness journey.',
                'price' => 1500.00,
                'duration_days' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Standard',
                'description' => 'Access to all gym equipment, group classes, and locker facilities. Great value for regular gym-goers.',
                'price' => 2500.00,
                'duration_days' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Premium',
                'description' => 'Unlimited access to all facilities including sauna, pool, and 2 personal training sessions per month.',
                'price' => 4000.00,
                'duration_days' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Basic Quarterly',
                'description' => 'Basic plan for 3 months with a 10% discount. Save more with a longer commitment.',
                'price' => 4050.00,
                'duration_days' => 90,
                'status' => 'active',
            ],
            [
                'name' => 'Premium Semi-Annual',
                'description' => 'Premium plan for 6 months with a 15% discount. Best value for dedicated fitness enthusiasts.',
                'price' => 20400.00,
                'duration_days' => 180,
                'status' => 'active',
            ],
            [
                'name' => 'VIP Annual',
                'description' => 'Full year VIP access to everything. Includes personal training, nutrition consultation, and priority booking.',
                'price' => 40000.00,
                'duration_days' => 365,
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }
    }
}
