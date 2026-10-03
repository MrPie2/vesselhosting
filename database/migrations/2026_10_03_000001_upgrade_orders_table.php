<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'number')) $table->string('number')->nullable();
            if (!Schema::hasColumn('orders', 'status')) $table->string('status')->default('pending');
            if (!Schema::hasColumn('orders', 'provisioning_status')) $table->string('provisioning_status')->default('not_started');
            if (!Schema::hasColumn('orders', 'provisioning_error')) $table->text('provisioning_error')->nullable();
            if (!Schema::hasColumn('orders', 'currency')) $table->string('currency', 3)->default('USD');
            if (!Schema::hasColumn('orders', 'subtotal')) $table->decimal('subtotal', 12, 2)->default(0);
            if (!Schema::hasColumn('orders', 'tax')) $table->decimal('tax', 12, 2)->default(0);
            if (!Schema::hasColumn('orders', 'total')) $table->decimal('total', 12, 2)->default(0);
            if (!Schema::hasColumn('orders', 'payment_reference')) $table->string('payment_reference')->nullable();
            if (!Schema::hasColumn('orders', 'paid_at')) $table->dateTime('paid_at')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('number');
            $table->unique('payment_reference');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['orders_number_unique']);
            $table->dropUnique(['orders_payment_reference_unique']);
            foreach ([
                'number','status','provisioning_status','provisioning_error',
                'currency','subtotal','tax','total','payment_reference','paid_at'
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) $table->dropColumn($column);
            }
        });
    }
};
