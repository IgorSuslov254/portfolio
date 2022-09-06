<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhotoObject extends Model
{
    use HasFactory;

    public function getPhotoLeft(array $where = array()){
        $photosLeft = DB::table('photo_objects as po')
            ->select(DB::raw("CONCAT('/data/img/new_fourth_block/',
                        tb.type,
                        '/',
                        sb.square,
                        '/',
                        nb.name,
                        '/',
                        po.photo
                    ) url"),
                    'po.building_object_id',
                    'po.type_building_id',
                    'po.square_building_id',
                    'po.number_building_id',
                    'bo.name'
            )
            ->leftJoin('type_buildings as tb', 'tb.id', '=', 'po.type_building_id')
            ->leftJoin('square_buildings as sb', 'sb.id', '=', 'po.square_building_id')
            ->leftJoin('number_buildings as nb', 'nb.id', '=', 'po.number_building_id')
            ->leftJoin('building_objects as bo', 'bo.id', '=', 'po.building_object_id')
            ->where($where)
            ->get();

        foreach ($photosLeft as $photoLeft){
            $response[] = array(
                'url' => $photoLeft->url,
                'id' => $photoLeft->building_object_id,
                'typeBuilding' => $photoLeft->type_building_id,
                'squareBuilding' => $photoLeft->square_building_id,
                'numberBuilding' => $photoLeft->number_building_id,
                'name' => $photoLeft->name,
            );
        }

        return $response;
    }
}
