<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    // GET /api/devices
    public function index()
    {
        // Kalau mau filter per user:
        return Device::where('user_id', Auth::id())->get();
    }

    // POST /api/devices
    public function store(Request $request)
    {
        // Validasi data
        $data = $request->validate([
            'user_id'     => 'required|integer',
            'device_code' => 'required|string',
            'name'        => 'required|string',
            'device_type' => 'required|string',
        ]);

        // Kolom tambahan
        $data['is_claimed']   = true;
        $data['last_seen_at'] = now();
        $data['created_at']   = now();
        $data['updated_at']   = now();

        // Simpan ke tabel devices
        $device = Device::firstOrCreate(
            ['device_code' => $data['device_code']],
            [
                'user_id'      => $data['user_id'],
                'name'         => $data['name'],
                'device_type'  => $data['device_type'],
                'is_claimed'   => true,
                'last_seen_at' => now(),
            ]
        );

        // Jika device_type = "temperatur", insert ke temperature_unit
        if ($data['device_type'] === 'temperatur') {
            DB::table('temperature_unit')->insert([
                'temperature_unit_id'   => $device->id,
                'room_status' => 'inactive', // default, bisa diubah admin
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return response()->json($device, 201);
    }

    public function showByCode($device_code)
    {
        dd('hai');
        $device = Device::where('device_code', $device_code)->first();
        if (!$device) {
            return response()->json([
                'status' => false,
                'message' => 'Device tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id'           => $device->id,
                'device_code'  => $device->device_code,
                'name'         => $device->name,
                'device_type'  => $device->device_type,
                'user_id'      => $device->user_id,
                'is_active'    => $device->is_active,
                'created_at'   => $device->created_at,
                'updated_at'   => $device->updated_at
            ]
        ]);
    }

    // PUT/PATCH /api/devices/{device}
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string',
            'location'    => 'sometimes|nullable|string',
            'device_type' => 'sometimes|required|string',
            'is_claimed'  => 'sometimes|boolean',
        ]);

        $device->update($data);

        return $device;
    }

    // DELETE /api/devices/{device}
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json(null, 204);
    }
}
