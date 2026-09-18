<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'sale_id',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // =============================================
    // HELPERS
    // =============================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
