<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceSetting extends Model
{
    use HasFactory;

    public $timestamps = false; // kita pakai updated_at manual

    protected $fillable = [
        'device_id',
        'lamp_mode',
        'water_min_level',
        'water_max_level',
        'tank_height_cm',
        'cfg_version',
        'updated_by',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
