<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeartbeatData extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'station',
        'voltage',
        'snr',
        'avg_snr',
        'rssi',
        'seq_number',
        'received_at',
    ];

    // belongs to a device
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
