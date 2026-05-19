<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->nullable()->after('amount');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            $table->text('notes')->nullable()->after('description');
            $table->date('due_date')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_percentage',
                'discount_amount',
                'tax_rate',
                'tax_amount',
                'notes',
                'due_date',
            ]);
        });
    }
};
