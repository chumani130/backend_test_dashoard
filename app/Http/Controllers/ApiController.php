<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\HeartbeatData;
use App\Models\TamperAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validate against the payload 
        $validated = $request->validate([
            'device' => 'required|string',  // device_id in your DB
            'time' => 'required|numeric',   // received_at/alert_time
            'snr' => 'required|numeric',
            'station' => 'required|string', // station
            'data' => 'required|string',    // determines type (01 or 03xxx)
            'avgSnr' => 'required|numeric', // avg_snr
            'lat' => 'required|numeric',    // last_lat/lat
            'lng' => 'required|numeric',    // last_lng/lng
            'rssi' => 'required|numeric',   // rssi
            'seqNumber' => 'required|numeric' // seq_number
        ]);

        try {
            // Find or create device
            $device = Device::firstOrCreate(
                ['device_id' => $validated['device']],
                [
                    'device_name' => 'Device ' . $validated['device'], 
                    'user_id' => 1,
                    'last_lat' => $validated['lat'],
                    'last_lng' => $validated['lng'],
                    'last_communication' => now()
                ]
            );

            // Convert Unix timestamp to datetime
            $receivedAt = \Carbon\Carbon::createFromTimestamp($validated['time']);

            // Process based on data type from specs
            if ($validated['data'] === '01') {
                TamperAlert::create([
                    'device_id' => $device->device_id, 
                    'alert_time' => now(),
                    'lat' => $validated['lat'],
                    'lng' => $validated['lng'],
                    'status' => 'active' // Added status field
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Tamper alert stored successfully'
                ]);
            }

            if (str_starts_with($validated['data'], '03')) {
                $voltage = hexdec(substr($validated['data'], 3)) / 1000;
                
                HeartbeatData::create([
                    'device_id' => $device->device_id,
                    'station' => $validated['station'],
                    'voltage' => $voltage,
                    'snr' => $validated['snr'],
                    'avg_snr' => $validated['avgSnr'],
                    'rssi' => $validated['rssi'],
                    'seq_number' => $validated['seqNumber'],
                    'received_at' => $receivedAt
                ]);

                // Update device last communication
                $device->update([
                    'last_communication' => $receivedAt,
                    'last_lat' => $validated['lat'],
                    'last_lng' => $validated['lng'],
                    'battery_voltage' => $voltage
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Heartbeat stored successfully',
                    'voltage' => $voltage
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Unknown data type'
            ], 400);

        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}