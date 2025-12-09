<?php

namespace App\Models;

use App\Models\Scopes\RoleFilterScope;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    protected $table = 'penduduks';
    protected $fillable = [
        'keluarga_id',
        'rw_id',
        'rt_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pendidikan',
        'status_perkawinan',
        'status_kependudukan',
        'shdk',
        'no_telp',
        'pekerjaan_id',
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }
}
