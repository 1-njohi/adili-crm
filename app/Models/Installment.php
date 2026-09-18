<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'paid_amount',
        'remaining_amount',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '>', now());
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', 'pending')
            ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // =============================================
    // HELPERS
    // =============================================

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function isDue(): bool
    {
        return $this->due_date->isToday() || $this->due_date->isPast();
    }

    public function markAsPaid($amount = null, $paidDate = null)
    {
        $paidAmount = $amount ?? $this->amount;
        $remaining = $this->amount - $paidAmount;

        $this->update([
            'paid_amount' => $paidAmount,
            'remaining_amount' => max(0, $remaining),
            'paid_date' => $paidDate ?? now(),
            'status' => $remaining <= 0 ? 'paid' : 'partial',
        ]);

        return $this;
    }

    public function markAsOverdue()
    {
        if ($this->status === 'pending' && $this->due_date->isPast()) {
            $this->update(['status' => 'overdue']);
        }

        return $this;
    }

    public function getFormattedDueDate(): string
    {
        return $this->due_date->format('d M Y');
    }
}
