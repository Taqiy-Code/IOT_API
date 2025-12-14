<?php

namespace App\Models\Lightning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightingUnit extends Model
{
    use HasFactory;

    protected $table = 'lighting_units';
    protected $primaryKey = 'unit_id';   // WAJIB!
    public $incrementing = false;        // PK bukan auto increment
    protected $keyType = 'int';          // Karena unit_id integer

    protected $fillable = [
        'unit_id',
        'current_lux',
        'lamp_status',
        'mode',
        'schedule_active',
        'last_schedule_check',
    ];

    public function settings()
    {
        return $this->hasOne(LightingSetting::class, 'unit_id', 'unit_id');
    }
}
