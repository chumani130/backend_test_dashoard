<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'last_lat',
        'last_lng',
    ];

    // belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // has many tamper alerts
    public function tamperAlerts()
    {
        return $this->hasMany(TamperAlert::class);
    }

    // has many heartbeat data records
    public function heartbeatData()
    {
        return $this->hasMany(HeartbeatData::class);
    }
}
