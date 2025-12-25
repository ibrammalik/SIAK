<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPendidikan extends Model
{
    protected $fillable = ['name'];

    public function penduduks()
    {
        return $this->hasMany(Penduduk::class, 'pendidikan_id');
    }
}
