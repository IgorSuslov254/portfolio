<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingObject extends Model
{
    use HasFactory;

    public function measuring()
    {
        return $this->belongsTo(Measuring::class);
    }
}
