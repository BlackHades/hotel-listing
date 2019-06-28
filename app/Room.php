<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        "hotel_id",
        "name",
        "room_type_id",
        "image"
    ];
}
