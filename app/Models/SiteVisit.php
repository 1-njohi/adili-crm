<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'booked_by_id',
        'attending_owner_id',
        'lead_tether_id',
        'plot_id',
        'scheduled_at',
        'duration_minutes',
        'buyer_attendee_count',
        'status',
        'checklist',
        'feedback',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'checklist' => 'array',
        'feedback' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by_id');
    }

    public function attendingOwner()
    {
        return $this->belongsTo(User::class, 'attending_owner_id');
    }

    public function leadTether()
    {
        return $this->belongsTo(LeadTether::class);
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function softHold()
    {
        return $this->hasOne(SoftHold::class);
    }

    public function isPending()
    {
        return $this->status === 'pending_owner_confirmation';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}