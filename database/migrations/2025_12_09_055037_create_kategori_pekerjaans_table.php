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
        Schema::create('kategori_pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('kategori_pekerjaans')->insert([
            ['name' => 'Petani Sendiri'],
            ['name' => 'Buruh Tani'],
            ['name' => 'Nelayan'],
            ['name' => 'Pengusaha'],
            ['name' => 'Buruh Industri'],
            ['name' => 'Buruh Bangunan'],
            ['name' => 'Dagang'],
            ['name' => 'Pengangkutan'],
            ['name' => 'ASN'],
            ['name' => 'Polri'],
            ['name' => 'TNI'],
            ['name' => 'Pensiunan'],
            ['name' => 'Karyawan Swasta'],
            ['name' => 'Pelajar/Mahasiswa'],
            ['name' => 'Lain-lain'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pekerjaans');
    }
};
