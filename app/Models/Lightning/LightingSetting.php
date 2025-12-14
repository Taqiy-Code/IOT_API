<?php

namespace App\Models\Lightning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightingSetting extends Model
{
    protected $table = 'lighting_settings';
    protected $primaryKey = 'unit_id';
    public $incrementing = false;

    protected $fillable = [
        'unit_id',
        'lux_threshold',
        'auto_on_delay_sec',
        'auto_off_delay_sec',
        'on_time',
        'off_time',
        'active_days',
        'allow_manual_override'
    ];

    public function unit()
    {
        return $this->belongsTo(LightingUnit::class, 'unit_id', 'id');
    }
}

