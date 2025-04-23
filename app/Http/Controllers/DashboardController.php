<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $devices = Device::with([
            'heartbeats' => function ($query) {
                $query->latest()->limit(1);
            },
            'tamperAlerts' => function ($query) {
                $query->latest()->limit(1);
            },
            'user'])->get(); // user linked to the device

        return response()->json([
            'devices' => $devices
        ]);
    }
}
