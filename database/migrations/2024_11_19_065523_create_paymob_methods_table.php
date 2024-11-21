<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paymob_methods', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->text('api_key');
            $table->string('integration_id');
            $table->string('currency')->default('EGP');
            $table->enum('status', ['active', 'notActive'])->default('active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymob_methods');
    }
};
