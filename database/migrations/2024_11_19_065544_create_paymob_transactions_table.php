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
        Schema::create('paymob_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('special_reference')->nullable()->unique();
            $table->string('paymob_order_id')->nullable();
            $table->foreignId('payment_method_id')->constrained('paymob_methods')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('EGP');
            $table->enum('status', ['paid', 'pending', 'canceled'])->default('pending')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymob_transactions');
    }
};
