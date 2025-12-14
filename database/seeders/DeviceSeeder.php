<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Lightning\LightingUnit;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat device
        $device = Device::create([
            'user_id'     => 1,
            'device_code' => 'unit01',
            'name'        => 'Lampu Depan Rumah',
            'location'    => 'Halaman',
            'device_type' => 'lighting',
            'last_seen_at' => now(),
            'is_claimed'  => true,
        ]);

        // 2. Buat Lighting Unit terhubung ke device
        LightingUnit::create([
            'unit_id' => $device->id,    // unit_id = device_id
            'current_lux' => 0,
            'lamp_status' => 'OFF',
            'mode' => 'MANUAL',
            'schedule_active' => 0,
            'last_schedule_check' => now(),
        ]);
    }
}
