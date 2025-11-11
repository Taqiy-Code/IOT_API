<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    // GET /api/devices/{device}/alerts
    public function index(Device $device)
    {
        return $device->alerts()->orderByDesc('created_at')->get();
    }

    // POST /api/devices/{device}/alerts
    public function store(Request $request, Device $device)
    {
        $data = $request->validate([
            'type'    => 'required|string',
            'message' => 'required|string',
        ]);

        $alert = $device->alerts()->create([
            'type'      => $data['type'],
            'message'   => $data['message'],
            'is_read'   => false,
            'created_at'=> now(),
        ]);

        return response()->json($alert, 201);
    }

    // PUT /api/alerts/{alert}/read
    public function markAsRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);

        return $alert;
    }
}
