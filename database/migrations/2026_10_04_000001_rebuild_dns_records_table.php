<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('dns_records');

        Schema::create('dns_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id');
            $table->string('type', 16);
            $table->string('name', 253);
            $table->text('content');
            $table->unsignedSmallInteger('priority')->nullable();
            $table->string('provider_status', 32)->default('pending');
            $table->text('provider_error')->nullable();
            $table->timestamps();

            $table->index(['domain_id', 'type']);
            $table->foreign('domain_id')
                ->references('id')
                ->on('domains')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
