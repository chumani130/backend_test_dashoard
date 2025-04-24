<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $devices = Device::with([
                'heartbeats' => function ($query) {
                    $query->latest()->limit(1);
                },
                'tamperAlerts' => function ($query) {
                    $query->latest()->limit(1);
                },
                'user'
            ])->paginate(10); // Added pagination

            return response()->json([
                'success' => true,
                'devices' => $devices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
