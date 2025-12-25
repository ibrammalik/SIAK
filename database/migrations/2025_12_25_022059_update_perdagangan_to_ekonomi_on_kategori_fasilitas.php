<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('kategori_fasilitas')
            ->where('name', 'Perdagangan')
            ->update([
                'name' => 'Ekonomi',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('kategori_fasilitas')
            ->where('name', 'Ekonomi')
            ->update([
                'name' => 'Perdagangan',
                'updated_at' => now(),
            ]);
    }
};
