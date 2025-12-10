<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_usahas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('kategori_usahas')->insert([
            ['name' => 'Perdagangan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jasa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Industri Rumahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pertanian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Peternakan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pariwisata', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kesehatan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_usahas');
    }
};
