<?php

namespace App\Models\WaterTank;

use App\Models\Device;
use App\Models\WaterTank\WaterTank;
use Illuminate\Database\Eloquent\Model;

class TankRealtime extends Model
{
    protected $table = 'tank_realtime';

    protected $fillable = [
        'tank_unit_id',
        'water_level_cm',
        'water_level_percent',
        'distance_cm',
        'flow_rate_lpm',
        'total_liters_today',
        'total_liters_alltime',
        'is_water_flowing',
        'pump_status',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'tank_unit_id');
    }

    public function tank()
    {
        return $this->belongsTo(WaterTank::class, 'tank_unit_id', 'unit_id');
    }
}
