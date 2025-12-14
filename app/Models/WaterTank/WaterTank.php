<?php

namespace App\Models\WaterTank;

use App\Models\Device;
use Illuminate\Database\Eloquent\Model;

class WaterTank extends Model
{
    protected $table = 'water_tank';

    protected $fillable = [
        'unit_id',
        'min_level_percent',
        'max_level_percent',
        'auto_mode',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'unit_id');
    }

    public function realtime()
    {
        return $this->hasOne(TankRealtime::class, 'tank_unit_id', 'unit_id');
    }
}
