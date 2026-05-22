<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop child tables first
        Schema::dropIfExists('billing_transaction_items');
        Schema::dropIfExists('payment_records');

        // Remove complex billing columns
        Schema::table('billing_transactions', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('billing_transactions', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }

            $columns = [
                'subtotal', 'discount_percentage', 'discount_amount',
                'tax_rate', 'tax_amount', 'due_date', 'days_until_due', 'updated_by',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('billing_transactions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->nullable()->after('amount');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            $table->date('due_date')->nullable()->after('payment_date');
            $table->integer('days_until_due')->default(30)->after('due_date');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('notes');
        });

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
        });

        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_transaction_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->dateTime('paid_at');
            $table->timestamps();
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
