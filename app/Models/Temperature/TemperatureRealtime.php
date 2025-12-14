<?php

namespace App\Models\Temperature;

use Illuminate\Database\Eloquent\Model;

class TemperatureRealtime extends Model
{
    protected $table = "temperature_unit_realtime";

    protected $fillable = [
        "unit_id",
        "room_temperature_c",
        "room_humidity_percent",
        "comfort_status",
    ];

    public $timestamps = true;
}
