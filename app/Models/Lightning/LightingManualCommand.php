<?php

namespace App\Models\Lightning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightingManualCommand extends Model
{
    protected $table = 'lighting_manual_commands';

    protected $fillable = [
        'id',
        'lighting_unit_id',
        'command',
        'executed',
        'created_at',
        'executed_at'
    ];
}

