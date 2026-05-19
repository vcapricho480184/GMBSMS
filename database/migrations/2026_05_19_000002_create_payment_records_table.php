<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_transaction_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method');
            $table->string('reference_number')->nullable()->comment('Cheque #, Receipt #, Transaction ID, etc.');
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->dateTime('paid_at');
            $table->timestamps();

            $table->index(['billing_transaction_id']);
            $table->index(['payment_method']);
            $table->index(['paid_at']);
            
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_records');
    }
};
