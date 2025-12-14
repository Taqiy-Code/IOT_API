<?php

namespace App\Http\Controllers\Lighting;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Lightning\LightingManualCommand;
use App\Models\Lightning\LightingSetting;
use App\Models\Lightning\LightingUnit;
use App\Services\MqttService;
use Illuminate\Http\Request;

class LightController extends Controller
{
    // ===== GET STATUS =====
    public function status($device_code)
    {
        $device = Device::where('device_code', $device_code)->firstOrFail();
        
        $unit = LightingUnit::with('settings')
        ->where('unit_id', $device->id)
        ->firstOrFail();

        return response()->json([
            'device_code' => $device_code,
            'lux' => $unit->current_lux,
            'lamp_status' => $unit->lamp_status,
            'mode' => $unit->mode,
            'settings' => $unit->settings
        ]);
    }

    // ===== CHANGE MODE =====
    public function setMode(Request $r, MqttService $mqtt, $device_code)
    {
        $device = Device::where('device_code', $device_code)->firstOrFail();

        $unit = LightingUnit::where('unit_id', $device->id)->firstOrFail();

        $unit->mode = $r->mode;
        $unit->save();

        $mqtt->publish("lighting/{$device_code}/mode", $r->mode);

        return response()->json(['success' => true, 'mode' => $r->mode]);
    }

    // ===== MANUAL CONTROL =====
    public function manualControl(Request $r, MqttService $mqtt, $device_code)
    {
        $device = Device::where('device_code', $device_code)->firstOrFail();
        // dd($device->id);

        LightingManualCommand::create([
            'lighting_unit_id' => $device->id,
            'command' => strtoupper($r->command),
            'executed' => 0
        ]);

        $mqtt->publish("lighting/{$device_code}/command", json_encode([
            "command" => strtoupper($r->command)
        ]));

        return response()->json(['success' => true]);
    }

    // ===== UPDATE CONFIG =====
    public function updateConfig(Request $r, MqttService $mqtt, $device_code)
    {
        $device = Device::where('device_code', $device_code)->firstOrFail();

        $setting = LightingSetting::updateOrCreate(
            ['unit_id' => $device->id],
            $r->only([
                'lux_threshold',
                'auto_on_delay_sec',
                'auto_off_delay_sec',
                'on_time',
                'off_time',
                'active_days',
                'allow_manual_override'
            ])
        );

        $mqtt->publish("lighting/{$device_code}/config", json_encode($setting));

        return response()->json(['success' => true, 'settings' => $setting]);
    }

}
