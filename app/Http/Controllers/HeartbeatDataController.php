<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeartbeatData;

class HeartbeatDataController extends Controller
{
    // Get all heartbeat data
    public function index()
    {
        $heartbeatData = HeartbeatData::all();
        return response()->json($heartbeatData);
    }

    // Get a single heartbeat data by ID
    public function show($id)
    {
        $heartbeatData = HeartbeatData::find($id);
        if (!$heartbeatData) {
            return response()->json(['message' => 'Heartbeat data not found'], 404);
        }
        return response()->json($heartbeatData);
    }

    // Get heartbeat data by device ID
    public function getByDeviceId($deviceId)
    {
        $heartbeatData = HeartbeatData::where('device_id', $deviceId)->get();
        if ($heartbeatData->isEmpty()) {
            return response()->json(['message' => 'No heartbeat data found for this device'], 404);
        }
        return response()->json($heartbeatData);
    }

    // Create a new heartbeat data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'station' => 'nullable|string',
            'voltage' => 'nullable|numeric',
            'snr' => 'nullable|numeric',
            'avg_snr' => 'nullable|numeric',
            'rssi' => 'nullable|numeric',
            'seq_number' => 'nullable|integer',
            'received_at' => 'required|date',
        ]);

        $heartbeatData = HeartbeatData::create($validated);
        return response()->json($heartbeatData, 201);
    }

    // Update an existing heartbeat data
    public function update(Request $request, $id)
    {
        $heartbeatData = HeartbeatData::findOrFail($id);
        if (!$heartbeatData) {
            return response()->json(['message' => 'Heartbeat data not found'], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'station' => 'nullable|string',
            'voltage' => 'nullable|numeric',
            'snr' => 'nullable|numeric',
            'avg_snr' => 'nullable|numeric',
            'rssi' => 'nullable|numeric',
            'seq_number' => 'nullable|integer',
            'received_at' => 'required|date',
        ]);

        $heartbeatData->update($validated);
        return response()->json($heartbeatData);
    }

    // Delete a heartbeat data
    public function destroy($id)
    {
        $heartbeatData = HeartbeatData::findOrFail($id);
        if (!$heartbeatData) {
            return response()->json(['message' => 'Heartbeat data not found'], 404);
        }

        $heartbeatData->delete();
        return response()->json(['message' => 'Heartbeat data deleted successfully']);
    }
}
