<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceDataHistory extends Model
{
    use HasFactory;

    public $timestamps = false; // hanya ada created_at

    protected $table = 'device_data_history';

    protected $fillable = [
        'device_id',
        'value',
        'raw_payload',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
