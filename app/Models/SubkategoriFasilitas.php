<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubkategoriFasilitas extends Model
{
    protected $fillable = ['kategori_fasilitas_id', 'name'];

    public function kategoriFasilitas()
    {
        return $this->belongsTo(KategoriFasilitas::class);
    }
}
