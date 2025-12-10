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
        Schema::create('fasilitases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_fasilitas_id')->nullable()->constrained('kategori_fasilitas')->nullOnDelete();
            $table->foreignId('subkategori_fasilitas_id')->nullable()->constrained('subkategori_fasilitas')->nullOnDelete();

            $table->string('nama');
            $table->string('alamat');

            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
