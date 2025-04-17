<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TamperAlert;
use App\Models\HeartbeatData;
use Carbon\Carbon;


abstract class ApiController extends Controller
{
    public function decodePayload(Request $request)
    {
        $validated = $request->validate([
            'device' => 'required|string',
            'time' => 'required|numeric',
            'snr' => 'required|string',
            'station' => 'required|string',
            'data' => 'required|string',
            'avgSnr' => 'required|string',
            'lat' => 'required|string',
            'lng' => 'required|string',
            'rssi' => 'required|string',
            'seqNumber' => 'required|string',
        ]);

        $device = Device::firstOrCreate(
            ['device_id' => $validated['device']],
            [
                'device_name' => 'Device-' . $validated['device'],
                'last_lat' => $validated['lat'],
                'last_lng' => $validated['lng'],
            ]
        );
        $device->update([
            'last_lat' => $validated['lat'],
            'last_lng' => $validated['lng'],
        ]);

        if ($validated['data'] === '01') {
            TamperAlert::create([
                'device_id' => $device->id,
                'alert_time' => Carbon::createFromTimestamp($validated['time']),
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
            ]);

        } elseif (str_starts_with($validated['data'], '03')) {
            $hex = substr($validated['data'], 2);
            $voltage = hexdec($hex) / 1000;

            HeartbeatData::create([
                'device_id' => $device->id,
                'station' => $validated['station'],
                'voltage' => $voltage,
                'snr' => $validated['snr'],
                'avg_snr' => $validated['avgSnr'],
                'rssi' => $validated['rssi'],
                'seq_number' => $validated['seqNumber'],
                'received_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Payload processed successfully']);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function dashboard()
    {
        $devices = Device::with([
            'heartbeatData' => function($query) {
            $query->latest();
        }, 
        'tamperAlerts' => function($query) {
            $query->latest();
        }])->get();

        return response()->json($devices);
    }
    
}
