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
        Schema::create('subkategori_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_fasilitas_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        $kategoriPendidikan = DB::table('kategori_fasilitas')->where('name', 'Pendidikan')->value('id');
        $kategoriKesehatan = DB::table('kategori_fasilitas')->where('name', 'Kesehatan')->value('id');
        $kategoriIbadah = DB::table('kategori_fasilitas')->where('name', 'Ibadah')->value('id');
        $kategoriPerdagangan = DB::table('kategori_fasilitas')->where('name', 'Perdagangan')->value('id');
        $kategoriOlahraga = DB::table('kategori_fasilitas')->where('name', 'Olahraga')->value('id');
        $kategoriTransportasi = DB::table('kategori_fasilitas')->where('name', 'Transportasi')->value('id');
        $kategoriPemerintahan = DB::table('kategori_fasilitas')->where('name', 'Pemerintahan')->value('id');
        $kategoriKeamanan = DB::table('kategori_fasilitas')->where('name', 'Keamanan')->value('id');
        $kategoriSosial = DB::table('kategori_fasilitas')->where('name', 'Sosial')->value('id');


        DB::table('subkategori_fasilitas')->insert([

            // Pendidikan
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'PAUD', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'TK', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'SD', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'SMP', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'SMA', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPendidikan, 'name' => 'Perguruan Tinggi', 'created_at' => now(), 'updated_at' => now()],


            // Kesehatan
            ['kategori_fasilitas_id' => $kategoriKesehatan, 'name' => 'Posyandu', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKesehatan, 'name' => 'Puskesmas', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKesehatan, 'name' => 'Klinik', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKesehatan, 'name' => 'Apotek', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKesehatan, 'name' => 'Rumah Sakit', 'created_at' => now(), 'updated_at' => now()],


            // Ibadah
            ['kategori_fasilitas_id' => $kategoriIbadah, 'name' => 'Masjid', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriIbadah, 'name' => 'Mushola', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriIbadah, 'name' => 'Gereja', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriIbadah, 'name' => 'Vihara', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriIbadah, 'name' => 'Pura', 'created_at' => now(), 'updated_at' => now()],


            // Perdagangan
            ['kategori_fasilitas_id' => $kategoriPerdagangan, 'name' => 'Pasar', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPerdagangan, 'name' => 'Toko', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPerdagangan, 'name' => 'Kios', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPerdagangan, 'name' => 'Ruko', 'created_at' => now(), 'updated_at' => now()],


            // Olahraga
            ['kategori_fasilitas_id' => $kategoriOlahraga, 'name' => 'Lapangan', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriOlahraga, 'name' => 'GOR', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriOlahraga, 'name' => 'Fitness', 'created_at' => now(), 'updated_at' => now()],


            // Transportasi
            ['kategori_fasilitas_id' => $kategoriTransportasi, 'name' => 'Terminal', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriTransportasi, 'name' => 'Halte', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriTransportasi, 'name' => 'Parkir', 'created_at' => now(), 'updated_at' => now()],


            // Pemerintahan
            ['kategori_fasilitas_id' => $kategoriPemerintahan, 'name' => 'Kantor Kelurahan', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPemerintahan, 'name' => 'Kantor RW', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriPemerintahan, 'name' => 'Kantor RT', 'created_at' => now(), 'updated_at' => now()],


            // Keamanan
            ['kategori_fasilitas_id' => $kategoriKeamanan, 'name' => 'Pos Kamling', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKeamanan, 'name' => 'Pos Polisi', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriKeamanan, 'name' => 'Pos TNI', 'created_at' => now(), 'updated_at' => now()],


            // Sosial
            ['kategori_fasilitas_id' => $kategoriSosial, 'name' => 'Balai Warga', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_fasilitas_id' => $kategoriSosial, 'name' => 'Panti Asuhan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkategori_fasilitas');
    }
};
