<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaterController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input dari ESP / UI
        $data = $request->validate([
            'device_code'       => 'required|string',
            'min_level_percent' => 'required|numeric',
            'max_level_percent' => 'required|numeric',
            'auto_mode'         => 'required|in:0,1',
        ]);

        // Cari device
        $device = DB::table('devices')
            ->where('device_code', $data['device_code'])
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Insert / Update ke water_tank
        DB::table('water_tank')->updateOrInsert(
            ['unit_id' => $device->id],
            [
                'min_level_percent' => $data['min_level_percent'],
                'max_level_percent' => $data['max_level_percent'],
                'auto_mode'         => $data['auto_mode'],
                'created_at'        => DB::raw('IFNULL(created_at, NOW())'),
                'updated_at'        => now(),
            ]
        );

        return response()->json(['status' => 'success'], 200);
    }

    public function index()
    {
        $units = DB::table('water_tank')
            ->join('devices', 'water_tank.unit_id', '=', 'devices.id')
            ->select(
                'water_tank.*',
                'devices.name as device_name',
                'devices.device_code'
            )
            ->get();

        return view('water_tank.index', compact('units'));
    }
}
