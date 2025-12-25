<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usaha extends Model
{
    protected $table = 'usahas';

    protected $fillable = [
        'kategori_usaha_id',
        'subkategori_usaha_id',
        'nama',
        'nama_pemilik',
        'nomor_pemilik',
        'alamat',
        'rw_id',
        'rt_id',
    ];

    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    public function kategoriUsaha()
    {
        return $this->belongsTo(KategoriUsaha::class);
    }

    public function subkategoriUsaha()
    {
        return $this->belongsTo(SubkategoriUsaha::class);
    }
}
