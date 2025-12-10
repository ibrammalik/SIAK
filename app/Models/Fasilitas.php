<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitases';

    protected $fillable = [
        'kategori_fasilitas_id',
        'subkategori_fasilitas_id',
        'nama',
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

    public function kategoriFasilitas()
    {
        return $this->belongsTo(KategoriFasilitas::class);
    }

    public function subkategoriFasilitas()
    {
        return $this->belongsTo(SubkategoriFasilitas::class);
    }
}
