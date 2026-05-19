<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_transactions', function (Blueprint $table) {
            // Drop foreign key constraints for redundant columns
            $table->dropForeign(['membership_id']);
            $table->dropForeign(['availed_service_id']);
            $table->dropColumn(['membership_id', 'availed_service_id']);
        });

        Schema::table('billing_transactions', function (Blueprint $table) {
            // Make payment_date NOT NULL with default
            $table->dateTime('payment_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();

            // Add payment tracking fields
            $table->integer('days_until_due')->default(30)->after('due_date');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['days_until_due', 'updated_by']);
            $table->date('payment_date')->nullable()->change();
        });

        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->foreignId('membership_id')->nullable()->constrained()->onDelete('set null')->after('user_id');
            $table->foreignId('availed_service_id')->nullable()->constrained()->onDelete('set null')->after('membership_id');
        });
    }
};
