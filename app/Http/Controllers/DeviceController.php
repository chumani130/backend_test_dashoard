<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    // Get all devices with user info
    public function index()
    {
        $devices = Device::with('user')->get();
        return response()->json($devices);
    }
    
    // Get a single device by ID with user info
    public function show($id)
    {
        $device = Device::with('user')->find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        return response()->json($device);
    }

    // Create a new device
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'last_lat' => 'required|numeric',
            'last_long' => 'required|numeric',
        ]);

        $device = Device::create($validated);
        return response()->json([
            'message' => 'Device created successfully',
            'device' => $device
        ], 201);
    }

    // Update an existing device
    public function update(Request $request, $id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }

        $validated = $request->validate([
            'device_name' => 'sometimes|required|string|max:255',
            'device_id' => 'sometimes|required|string|max:255',
            'last_lat' => 'sometimes|required|numeric',
            'last_long' => 'sometimes|required|numeric',
        ]);

        $device->update($validated);  
        return response()->json([
            'message' => 'Device updated successfully',
            'device' => $device
        ]);
    }

    // Delete a device
    public function destroy($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }
    
        $device->delete();
        return response()->json([
            'message' => 'Device deleted successfully'
        ]);
    }
}
