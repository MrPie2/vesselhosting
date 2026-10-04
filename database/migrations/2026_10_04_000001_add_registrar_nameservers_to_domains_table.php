<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->string('reseller_order_id')->nullable()->after('domain');
            $table->string('nameserver_1')->nullable()->after('reseller_order_id');
            $table->string('nameserver_2')->nullable()->after('nameserver_1');
            $table->string('nameserver_3')->nullable()->after('nameserver_2');
            $table->string('nameserver_4')->nullable()->after('nameserver_3');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'reseller_order_id',
                'nameserver_1',
                'nameserver_2',
                'nameserver_3',
                'nameserver_4',
            ]);
        });
    }
};
