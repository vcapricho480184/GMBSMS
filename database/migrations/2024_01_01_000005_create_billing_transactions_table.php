<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('availed_service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'gcash', 'paymaya', 'bank_transfer', 'other'])->default('cash');
            $table->enum('payment_status', ['paid', 'pending', 'failed', 'refunded'])->default('pending');
            $table->enum('type', ['membership', 'service', 'other'])->default('membership');
            $table->text('description')->nullable();
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_transactions');
    }
};
