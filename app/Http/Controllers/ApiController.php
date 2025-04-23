<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\TamperAlert;
use App\Models\HeartbeatData;


class ApiController extends Controller
{
    public function store(Request $request)
    {
        $payload = $request->all();

        if (!isset($payload['device_id']) ||  !isset($payload['data'])) {
            return response()->json(['error' => 'Invalid data format'], 422);
        }

        $device = Device::where('device_id', $payload['device_id'])->first();
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $data = $payload['data'];

        if ($data === 01){
            // Handle tamper alert
            TamperAlert::create([
                'device_id' => $device->id,
                'timestamp' => $payload['time'],
                'lat' => $payload['lat'] ?? null,
                'lng' => $payload['lng'] ?? null,
                'station' => $payload['station'],
                'rssi' => $payload['rssi'],
                'snr' => $payload['snr'],
                'avgSnr' => $payload['avgSnr'],
                'seqNumber' => $payload['seqNumber'],
            ]);
            
        } elseif (str_starts_with($data, '03')) {
            // Handle heartbeat
            $voltageHex = substr($data, 2); // remove "03"
            $voltage = hexdec($voltageHex) / 1000;

            HeartbeatData::create([
                'device_id' => $device->id,
                'timestamp' => $payload['time'],
                'voltage' => $voltage,
                'lat' => $payload['lat'] ?? null,
                'lng' => $payload['lng'] ?? null,
                'station' => $payload['station'],
                'rssi' => $payload['rssi'],
                'snr' => $payload['snr'],
                'avgSnr' => $payload['avgSnr'],
                'seqNumber' => $payload['seqNumber'],
            ]);
        }

        // Update device last location
        $device->update([
            'last_lat' => $payload['lat'] ?? $device->last_lat,
            'last_long' => $payload['lng'] ?? $device->last_long,
        ]);

        return response()->json(['message' => 'Data processed successfully'], 200);
    }
}