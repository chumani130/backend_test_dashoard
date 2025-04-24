<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use App\Models\HeartbeatData;
use App\Models\TamperAlert;

class DeviceController extends Controller
{
    // Get all devices with user info
    public function index(): JsonResponse
    {
        try {
            $devices = Device::with(['user', 'latestHeartbeat', 'latestTamperAlert'])
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $devices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch devices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $device = Device::with(['user', 'heartbeats', 'tamperAlerts'])
                ->find($id);

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $device
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|string|unique:devices',
                'device_name' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
                'last_lat' => 'nullable|numeric',
                'last_lng' => 'nullable|numeric',
                'description' => 'nullable|string'
            ]);

            $device = Device::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Device created successfully',
                'data' => $device
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $device = Device::find($id);

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $validated = $request->validate([
                'device_name' => 'sometimes|string|max:255',
                'user_id' => 'sometimes|exists:users,id',
                'last_lat' => 'nullable|numeric',
                'last_lng' => 'nullable|numeric',
                'description' => 'nullable|string'
            ]);

            $device->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Device updated successfully',
                'data' => $device
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $device = Device::find($id);

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $device->delete();

            return response()->json([
                'success' => true,
                'message' => 'Device deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeviceStats($id): JsonResponse
    {
        try {
            $device = Device::find($id);

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $stats = [
                'total_heartbeats' => HeartbeatData::where('device_id', $id)->count(),
                'total_tamper_alerts' => TamperAlert::where('device_id', $id)->count(),
                'last_communication' => $device->last_communication,
                'battery_status' => $this->getBatteryStatus($device->battery_voltage),
                'active_tamper_alerts' => TamperAlert::where('device_id', $id)
                    ->where('status', 'active')
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch device stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function getBatteryStatus($voltage): string
    {
        if ($voltage === null) return 'unknown';
        if ($voltage < 3.0) return 'critical';
        if ($voltage < 3.5) return 'low';
        return 'good';
    }
}
