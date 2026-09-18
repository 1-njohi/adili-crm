<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadBehavior extends Model
{
    use HasFactory;

    protected $table = 'lead_behavior';

    protected $fillable = [
        'lead_tether_id',
        'event_type',
        'event_data',
        'device_type',
        'device_os',
        'browser',
        'location_country',
        'location_city',
        'timezone',
        'ip_hash',
        'session_id',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function leadTether()
    {
        return $this->belongsTo(LeadTether::class);
    }
}
