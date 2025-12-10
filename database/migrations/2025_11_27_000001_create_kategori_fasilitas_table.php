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
        Schema::create('kategori_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('kategori_fasilitas')->insert([
            ['name' => 'Pendidikan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kesehatan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ibadah', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Perdagangan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Olahraga', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transportasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pemerintahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Keamanan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sosial', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_fasilitas');
    }
};
