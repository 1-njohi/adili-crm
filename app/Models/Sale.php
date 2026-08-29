<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plot_id',
        'project_id',
        'buyer_id',
        'agent_id',
        'total_price',
        'deposit',
        'months',
        'monthly_payment',
        'remaining_balance',
        'payment_tier_selected',
        'commission_rate',
        'commission_type',
        'status',
        'deposit_receipt_path'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function generateInstallments()
    {
        $this->installments()->delete();

        $remainingBalance = $this->total_price - $this->deposit;
        $installmentAmount = $remainingBalance / $this->months;

        for ($i = 1; $i <= $this->months; $i++) {
            Installment::create([
                'sale_id' => $this->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => now()->addMonths($i)->startOfMonth(),
                'status' => 'pending',
                'paid_amount' => 0,
                'remaining_amount' => $installmentAmount,
            ]);
        }

        $this->update([
            'remaining_balance' => $remainingBalance,
            'status' => 'active', // ✅ Ensure sale is active
        ]);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function commissionReleases()
    {
        return $this->hasMany(CommissionRelease::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDefaulted($query)
    {
        return $query->where('status', 'defaulted');
    }

    // =============================================
    // HELPERS
    // =============================================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDefaulted(): bool
    {
        return $this->status === 'defaulted';
    }

    public function getRemainingBalance(): float
    {
        $totalPaid = $this->payments()->sum('amount');
        return max(0, $this->total_price - $totalPaid);
    }

    public function getProgressPercentage(): float
    {
        if ($this->total_price <= 0) {
            return 0;
        }
        $totalPaid = $this->payments()->sum('amount');
        return round(($totalPaid / $this->total_price) * 100, 2);
    }

    public function getAgentCommissionTotal(): float
    {
        if (!$this->commission_rate) {
            return 0;
        }

        if ($this->commission_type === 'percentage') {
            return ($this->total_price * $this->commission_rate) / 100;
        }

        return $this->commission_rate;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'remaining_balance' => 0,
        ]);

        // Update plot status
        if ($this->plot) {
            $this->plot->update(['status' => 'sold']);
        }
    }

    public function isFullyPaid(): bool
    {
        return $this->payments()->sum('amount') >= $this->total_price;
    }
}