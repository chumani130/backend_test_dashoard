<?php

namespace App\Http\Controllers;

use App\Models\HeartbeatData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HeartbeatDataController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $heartbeatData = HeartbeatData::with('device')->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $heartbeatData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch heartbeat data'
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $heartbeatData = HeartbeatData::with('device')->find($id);
            
            if (!$heartbeatData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Heartbeat data not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $heartbeatData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch heartbeat data'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|exists:devices,id',
                'station' => 'nullable|string|max:255',
                'voltage' => 'nullable|numeric',
                'snr' => 'nullable|numeric',
                'avg_snr' => 'nullable|numeric',
                'rssi' => 'nullable|numeric',
                'seq_number' => 'nullable|integer',
                'received_at' => 'required|date'
            ]);

            $heartbeatData = HeartbeatData::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Heartbeat data created successfully',
                'data' => $heartbeatData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create heartbeat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $heartbeatData = HeartbeatData::find($id);
            
            if (!$heartbeatData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Heartbeat data not found'
                ], 404);
            }

            $validated = $request->validate([
                'device_id' => 'required|exists:devices,id',
                'station' => 'nullable|string|max:255',
                'voltage' => 'nullable|numeric',
                'snr' => 'nullable|numeric',
                'avg_snr' => 'nullable|numeric',
                'rssi' => 'nullable|numeric',
                'seq_number' => 'nullable|integer',
                'received_at' => 'required|date'
            ]);

            $heartbeatData->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Heartbeat data updated successfully',
                'data' => $heartbeatData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update heartbeat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $heartbeatData = HeartbeatData::find($id);
            
            if (!$heartbeatData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Heartbeat data not found'
                ], 404);
            }

            $heartbeatData->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Heartbeat data deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete heartbeat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}