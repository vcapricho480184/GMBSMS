<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\AvailedService;
use App\Models\GymService;
use App\Models\BillingTransaction;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@email.com', 'phone' => '09171111111', 'gender' => 'male', 'date_of_birth' => '1995-03-15', 'address' => 'Makati City, Metro Manila'],
            ['name' => 'Maria Santos', 'email' => 'maria@email.com', 'phone' => '09172222222', 'gender' => 'female', 'date_of_birth' => '1998-07-22', 'address' => 'Quezon City, Metro Manila'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro@email.com', 'phone' => '09173333333', 'gender' => 'male', 'date_of_birth' => '1992-11-08', 'address' => 'Pasig City, Metro Manila'],
            ['name' => 'Ana Garcia', 'email' => 'ana@email.com', 'phone' => '09174444444', 'gender' => 'female', 'date_of_birth' => '2000-01-30', 'address' => 'Taguig City, Metro Manila'],
            ['name' => 'Carlos Mendoza', 'email' => 'carlos@email.com', 'phone' => '09175555555', 'gender' => 'male', 'date_of_birth' => '1988-05-12', 'address' => 'Mandaluyong City, Metro Manila'],
            ['name' => 'Lisa Fernandez', 'email' => 'lisa@email.com', 'phone' => '09176666666', 'gender' => 'female', 'date_of_birth' => '1996-09-18', 'address' => 'San Juan City, Metro Manila'],
            ['name' => 'Mark Villanueva', 'email' => 'mark@email.com', 'phone' => '09177777777', 'gender' => 'male', 'date_of_birth' => '1993-12-25', 'address' => 'Marikina City, Metro Manila'],
            ['name' => 'Grace Aquino', 'email' => 'grace@email.com', 'phone' => '09178888888', 'gender' => 'female', 'date_of_birth' => '1999-04-07', 'address' => 'Parañaque City, Metro Manila'],
        ];

        $plans = MembershipPlan::all();
        $services = GymService::all();
        $paymentMethods = ['cash', 'card', 'gcash', 'paymaya', 'bank_transfer'];

        foreach ($members as $index => $memberData) {
            $user = User::create(array_merge($memberData, [
                'password' => Hash::make('password'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]));

            // Create membership
            $plan = $plans->random();
            $startDate = Carbon::now()->subDays(rand(1, 25));
            $membership = Membership::create([
                'user_id' => $user->id,
                'membership_plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addDays($plan->duration_days),
                'status' => 'active',
            ]);

            // Create billing for membership
            BillingTransaction::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'amount' => $plan->price,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => 'paid',
                'type' => 'membership',
                'description' => $plan->name . ' Membership Plan',
                'payment_date' => $startDate,
            ]);

            // Create some availed services
            $numServices = rand(1, 3);
            for ($i = 0; $i < $numServices; $i++) {
                $service = $services->random();
                $availedDate = Carbon::now()->subDays(rand(1, 20));

                $availed = AvailedService::create([
                    'user_id' => $user->id,
                    'gym_service_id' => $service->id,
                    'availed_date' => $availedDate,
                    'notes' => null,
                ]);

                BillingTransaction::create([
                    'user_id' => $user->id,
                    'availed_service_id' => $availed->id,
                    'invoice_number' => 'INV-' . $availedDate->format('Ymd') . '-' . str_pad(100 + ($index * 3) + $i, 4, '0', STR_PAD_LEFT),
                    'amount' => $service->price,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'payment_status' => 'paid',
                    'type' => 'service',
                    'description' => $service->name,
                    'payment_date' => $availedDate,
                ]);
            }

            // Create attendance records
            $numAttendance = rand(5, 15);
            for ($i = 0; $i < $numAttendance; $i++) {
                $date = Carbon::now()->subDays(rand(1, 25));
                $checkInHour = rand(5, 18);
                $checkIn = sprintf('%02d:%02d:00', $checkInHour, rand(0, 59));
                $checkOut = rand(0, 1) ? sprintf('%02d:%02d:00', $checkInHour + rand(1, 3), rand(0, 59)) : null;

                // Avoid duplicate date entries for same user
                $exists = Attendance::where('user_id', $user->id)->where('date', $date->format('Y-m-d'))->exists();
                if (!$exists) {
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                    ]);
                }
            }
        }
    }
}
