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
        Schema::table('usahas', function (Blueprint $table) {
            $table->string('nomor_pemilik')->nullable()->after('nama_pemilik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usahas', function (Blueprint $table) {
            $table->dropColumn('nomor_pemilik');
        });
    }
};
