<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE billing_transactions MODIFY type ENUM('membership','service','other','mixed') NOT NULL DEFAULT 'membership'");
    }

    public function down(): void
    {
        DB::statement("UPDATE billing_transactions SET type='other' WHERE type='mixed'");
        DB::statement("ALTER TABLE billing_transactions MODIFY type ENUM('membership','service','other') NOT NULL DEFAULT 'membership'");
    }
};
