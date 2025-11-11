<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceSettingController extends Controller
{
    // GET /api/devices/{device}/settings
    public function show(Device $device)
    {
        $settings = $device->settings;

        if (!$settings) {
            // default kosong
            $settings = new DeviceSetting([
                'device_id' => $device->id,
            ]);
        }

        return $settings;
    }

    // PUT /api/devices/{device}/settings
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'lamp_mode'       => 'nullable|string',
            'water_min_level' => 'nullable|integer',
            'water_max_level' => 'nullable|integer',
            'tank_height_cm'  => 'nullable|integer',
        ]);

        $settings = $device->settings ?? new DeviceSetting(['device_id' => $device->id]);

        $data['cfg_version'] = ($settings->cfg_version ?? 0) + 1;
        $data['updated_by']  = Auth::id();
        $data['updated_at']  = now();

        $settings->fill($data);
        $settings->save();

        return $settings;
    }
}
