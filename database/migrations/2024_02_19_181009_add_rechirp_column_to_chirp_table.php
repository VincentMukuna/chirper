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
        Schema::table('chirps', function (Blueprint $table) {
            $table->unsignedBigInteger('rechirping')->nullable();

            //cannot rechirp same post more than once
            $table->unique(['user_id', 'rechirping']);

            $table
                ->foreign('rechirping')
                ->references('id')
                ->on('chirps')
                ->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chirp', function (Blueprint $table) {
            $table->dropColumn('rechirping');
            $table->dropForeign(['rechirping']);
            $table->dropUnique(['user_id', 'rechirping']);

        });
    }
};
