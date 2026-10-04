<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_prices', function (Blueprint $table) {
            $table->id();
            $table->string('tld', 100)->unique();
            $table->decimal('registration_price', 12, 2)->default(0);
            $table->decimal('renewal_price', 12, 2)->default(0);
            $table->decimal('transfer_price', 12, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_prices');
    }
};
