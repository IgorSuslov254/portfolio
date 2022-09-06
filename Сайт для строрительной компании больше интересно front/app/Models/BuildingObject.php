<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BuildingObject extends Model
{
    use HasFactory;

    public function photoObjects()
    {
        return $this->hasMany(PhotoObject::class);
    }

    public function getphotoObjects(array $where = array()){
        $photoObjects = DB::table('photo_objects as po')
            ->select(DB::raw("CONCAT('/data/img/new_fourth_block/',
                        tb.type,
                        '/',
                        sb.square,
                        '/',
                        nb.name,
                        '/',
                        po.photo
                    ) url")
            )
            ->leftJoin('type_buildings as tb', 'tb.id', '=', 'po.type_building_id')
            ->leftJoin('square_buildings as sb', 'sb.id', '=', 'po.square_building_id')
            ->leftJoin('number_buildings as nb', 'nb.id', '=', 'po.number_building_id')
            ->where($where)
            ->get();

        return $photoObjects;
    }
}
