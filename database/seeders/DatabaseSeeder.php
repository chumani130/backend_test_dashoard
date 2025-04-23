<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\TamperAlert;
use App\Models\HeartbeatData;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // 🔐 Plain text: password
        ]);

        // Create test device
        $device = Device::create([
            'device_id' => 'ABC123',
            'device_name' => 'Device-ABC123',
            'last_lat' => '-33.918861',
            'last_lng' => '18.423300',
        ]);

        // Create dummy tamper alerts
        TamperAlert::create([
            'device_id' => $device->id,
            'device_name' => 'Device-ABC123',
            'alert_time' => now(),
            'lat' => '-33.919',
            'lng' => '18.4231',
        ]);

        TamperAlert::create([
            'device_id' => $device->id,
            'device_name' => 'Device-ABC123',
            'alert_time' => now()->subMinutes(10),
            'lat' => '-33.920',
            'lng' => '18.4220',
        ]);

        // Create dummy heartbeat data
        HeartbeatData::create([
            'device_id' => $device->id,
            'device_name' => 'Device-ABC123',
            'station' => 'Station-1',
            'voltage' => 3.7,
            'snr' => '8.5',
            'avg_snr' => '7.0',
            'rssi' => '-90',
            'seq_number' => '001',
            'received_at' => now(),
        ]);
    }
}
