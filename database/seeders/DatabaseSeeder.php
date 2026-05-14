<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MembershipPlanSeeder::class,
            GymServiceSeeder::class,
            DemoMemberSeeder::class,
        ]);
    }
}
