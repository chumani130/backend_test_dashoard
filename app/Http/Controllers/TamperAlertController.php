<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TamperAlert;
use Illuminate\Http\JsonResponse;


class TamperAlertController extends Controller
{
    //Get all tamper alerts
    public function index(): JsonResponse
    {
        try {
            $alerts = TamperAlert::with('device')
                ->orderBy('alert_time', 'desc')
                ->paginate(10);
                
            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tamper alerts'
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $alert = TamperAlert::with('device')->find($id);
            
            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamper alert not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tamper alert'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|exists:devices,id',
                'alert_time' => 'required|date',
                'resolved_at' => 'nullable|date',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'status' => 'nullable|in:active,resolved'
            ]);

            $alert = TamperAlert::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tamper alert created successfully',
                'data' => $alert
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tamper alert',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $alert = TamperAlert::find($id);
            
            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamper alert not found'
                ], 404);
            }

            $validated = $request->validate([
                'resolved_at' => 'nullable|date',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'status' => 'nullable|in:active,resolved'
            ]);

            // Auto-set resolved_at if status is changed to resolved
            if (isset($validated['status']) && $validated['status'] === 'resolved') {
                $validated['resolved_at'] = $validated['resolved_at'] ?? now();
            }

            $alert->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tamper alert updated successfully',
                'data' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tamper alert',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $alert = TamperAlert::find($id);
            
            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamper alert not found'
                ], 404);
            }

            $alert->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tamper alert deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tamper alert',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
