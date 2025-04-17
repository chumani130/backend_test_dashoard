<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TamperAlert extends Model
{
    use HasFactory;

    protected $table = 'tamper_alerts';

    protected $fillable = [
        'device_id',
        'alert_time',
        'resolved_at',
        'lat',
        'lng',
    ];

    // Belongs to a device
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}
