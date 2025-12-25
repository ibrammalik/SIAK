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
        DB::table('penduduks')
            ->whereNotIn('pendidikan', ['Perguruan Tinggi'])
            ->update([
                'pendidikan_id' => DB::raw("
                    (SELECT id FROM kategori_pendidikans
                    WHERE kategori_pendidikans.name = penduduks.pendidikan
                    LIMIT 1)
                ")
            ]);

        DB::table('penduduks')
            ->whereIn('pendidikan', ['Perguruan Tinggi'])
            ->update([
                'pendidikan_id' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
