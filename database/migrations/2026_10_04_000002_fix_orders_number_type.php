<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('number', 50)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Keep the order number as a string. Existing VH-* order numbers cannot
        // safely be converted back to an integer column.
    }
};
