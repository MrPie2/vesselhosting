<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hostings')) {
            Schema::create('hostings', function (Blueprint $table) {
                $table->id();
                $table->string('user_id');
                $table->string('plan_id')->nullable();
                $table->string('plan_name')->nullable();
                $table->string('bandwidth')->nullable();
                $table->string('username')->nullable();
                $table->string('domain')->nullable();
                $table->string('domain_id')->nullable();
                $table->string('server_hostname')->nullable();
                $table->text('password')->nullable();
                $table->dateTime('expiry_date')->nullable();
                $table->unsignedInteger('duration')->nullable();
                $table->string('status')->default('pending');
                $table->string('provisioning_status')->default('pending');
                $table->text('provisioning_error')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('hostings', function (Blueprint $table) {
            if (!Schema::hasColumn('hostings', 'username')) $table->string('username')->nullable();
            if (!Schema::hasColumn('hostings', 'domain')) $table->string('domain')->nullable();
            if (!Schema::hasColumn('hostings', 'domain_id')) $table->string('domain_id')->nullable();
            if (!Schema::hasColumn('hostings', 'server_hostname')) $table->string('server_hostname')->nullable();
            if (!Schema::hasColumn('hostings', 'password')) $table->text('password')->nullable();
            if (!Schema::hasColumn('hostings', 'expiry_date')) $table->dateTime('expiry_date')->nullable();
            if (!Schema::hasColumn('hostings', 'duration')) $table->unsignedInteger('duration')->nullable();
            if (!Schema::hasColumn('hostings', 'status')) $table->string('status')->default('pending');
            if (!Schema::hasColumn('hostings', 'provisioning_status')) $table->string('provisioning_status')->default('pending');
            if (!Schema::hasColumn('hostings', 'provisioning_error')) $table->text('provisioning_error')->nullable();
        });
    }

    public function down(): void
    {
        // The original hostings table is used by earlier application versions.
    }
};
