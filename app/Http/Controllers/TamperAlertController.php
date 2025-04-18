<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TamperAlertController extends Controller
{
    //Get all tamper alerts
    public function index()
    {
        return response()->json(TamperAlert::with('device')->get());
    }

    //Get a single tamper alert
    public function show($id)
    {
        return response()->json(TamperAlert::findOrFail($id));
    }

    //Create a new tamper alert
    public function store(Request $request)
    {
         $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'alert_time' => 'required|date',
            'resolved_at' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $tamperAlert = TamperAlert::create($validated);
        return response()->json($tamperAlert, 201);
    }
    //Update a tamper alert
    public function update(Request $request, $id)
    {
        $tamperAlert = tamperAlerts::find($id);
        if (!$tamperAlert) {
            return response()->json(['message' => 'Tamper alert not found'], 404);
        }

        $alert->update($request->only([
            'resolved_at', 'lat', 'lng',
        ]));

        return response()->json($alert);
    }

    //Delete a tamper alert
    public function destroy($id)
    {
         $tamperAlert = TamperAlert::findOrFail($id);
        if (!$tamperAlert) {
            return response()->json(['message' => 'Tamper alert not found'], 404);
        }

        $tamperAlert->delete();

        return response()->json(['message' => 'Tamper alert deleted'], 200);
    }
}
