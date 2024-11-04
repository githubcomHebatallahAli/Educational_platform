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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_parnt_id_foreign');

            // إعادة إضافة التقييد مع nullOnDelete
            $table->foreign('parnt_id')
                  ->references('id')->on('parnts')
                  ->nullOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parnt_id']);

            // إعادة إضافة التقييد السابق
            $table->foreign('parnt_id')
                  ->references('id')->on('parnts')
                  ->cascadeOnDelete();
        });


    }
};
