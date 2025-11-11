<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data = $request->validate([
            'device_code' => 'required|string|unique:devices,device_code',
            'name'        => 'required|string',
            'location'    => 'nullable|string',
            'device_type' => 'required|string',
        ]);

        $data['user_id'] = Auth::id();
        $data['is_claimed'] = true;

        $device = Device::create($data);

        return response()->json($device, 201);
    }

    // GET /api/devices/{device}
    public function show(Device $device)
    {
        $device->load(['settings', 'alerts' => function ($q) {
            $q->latest('created_at')->limit(20);
        }]);

        return $device;
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
