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
        Schema::table('penduduks', function (Blueprint $table) {
            $table->foreign('pendidikan_id')
                ->references('id')
                ->on('kategori_pendidikans')
                ->nullOnDelete();

            $table->dropColumn('pendidikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->string('pendidikan')->nullable();

            $table->dropForeign(['pendidikan_id']);
        });
    }
};
