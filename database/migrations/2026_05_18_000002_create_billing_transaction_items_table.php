<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_transaction_id')->constrained()->onDelete('cascade');
            $table->enum('item_type', ['membership', 'service', 'other']);
            $table->foreignId('membership_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('availed_service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->index(['item_type']);
            $table->index(['membership_id']);
            $table->index(['availed_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_transaction_items');
    }
};
