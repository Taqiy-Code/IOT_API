<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{
    use HasFactory;

    public $timestamps = false; // hanya created_at

    protected $fillable = [
        'device_id',
        'type',
        'message',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read'   => 'boolean',
        'created_at'=> 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
