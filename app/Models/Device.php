<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_code',
        'name',
        'location',
        'device_type',
        'last_seen_at',
        'is_claimed',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'is_claimed'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataHistory()
    {
        return $this->hasMany(DeviceDataHistory::class);
    }

    public function settings()
    {
        return $this->hasOne(DeviceSetting::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
