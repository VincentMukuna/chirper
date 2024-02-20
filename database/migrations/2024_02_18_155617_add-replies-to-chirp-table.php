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
        Schema::table('Chirps', function (Blueprint $table) {
            $table->unsignedBigInteger('replying_to')->nullable();

            $table->foreign('replying_to')
                ->references('id')
                ->on('chirps')
                ->noActionOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Chirp', function (Blueprint $table) {
            $table->dropColumn('replying_to');
            $table->dropForeign(['replying_to']);
        });
    }
};
