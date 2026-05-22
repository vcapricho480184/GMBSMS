<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->boolean('billed')->default(false)->after('status');
        });

        Schema::table('availed_services', function (Blueprint $table) {
            $table->boolean('billed')->default(false)->after('status');
        });

        // Backfill: mark memberships/services that already have billing transactions
        // Match by user_id + description pattern
        $billedDescriptions = DB::table('billing_transactions')->pluck('description', 'user_id');

        DB::table('memberships')
            ->join('membership_plans', 'memberships.membership_plan_id', '=', 'membership_plans.id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('billing_transactions')
                    ->whereColumn('billing_transactions.user_id', 'memberships.user_id')
                    ->where('billing_transactions.type', 'membership');
            })
            ->update(['memberships.billed' => true]);

        DB::table('availed_services')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('billing_transactions')
                    ->whereColumn('billing_transactions.user_id', 'availed_services.user_id')
                    ->where('billing_transactions.type', 'service');
            })
            ->update(['availed_services.billed' => true]);
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('billed');
        });

        Schema::table('availed_services', function (Blueprint $table) {
            $table->dropColumn('billed');
        });
    }
};
