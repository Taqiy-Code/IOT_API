<?php

namespace App\Http\Controllers\Temperature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemperatureRealtimeController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'device_code'     => 'required|string',
            'temperature'     => 'required|numeric',
            'humidity'        => 'required|numeric',
            'comfort_status'  => 'nullable|string',
            'signal_strength' => 'nullable|integer',
        ]);

        // Cari device berdasarkan device_code
        $device = DB::table('devices')
            ->where('device_code', $data['device_code'])
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Update atau insert ke temperature_realtime
        DB::table('temperature_realtime')->updateOrInsert(
            ['unit_id' => $device->id], // kondisi
            [
                'room_temperature_c'    => $data['temperature'],
                'room_humidity_percent' => $data['humidity'],
                'comfort_status'        => $data['comfort_status'] ?? 'normal',
                'signal_strength'       => $data['signal_strength'] ?? 0,
                'updated_at'            => now(),
            ]
        );

        return response()->json(['status' => 'success'], 200);
    }

    public function index()
    {
        $units = DB::table('temperature_realtime')
            ->join('devices', 'temperature_realtime.unit_id', '=', 'devices.id')
            ->select('temperature_realtime.*', 'devices.name as device_name')
            ->get();

        return view('temperature', compact('units'));
    }
}
