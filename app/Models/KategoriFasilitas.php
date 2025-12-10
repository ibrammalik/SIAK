<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriFasilitas extends Model
{
    protected $fillable = ['name'];

    public function subkategoriFasilitas()
    {
        return $this->hasMany(SubkategoriFasilitas::class);
    }
}
