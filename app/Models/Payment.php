<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'installment_id',
        'amount',
        'method',
        'reference',
        'receipt_path',
        'reconciled_at',
        'reconciled_by',
        'allocated_to',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function reconciler()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeReconciled($query)
    {
        return $query->whereNotNull('reconciled_at');
    }

    public function scopeUnreconciled($query)
    {
        return $query->whereNull('reconciled_at');
    }

    // =============================================
    // HELPERS
    // =============================================

    public function isReconciled(): bool
    {
        return $this->reconciled_at !== null;
    }

    public function markAsReconciled($userId = null)
    {
        $this->update([
            'reconciled_at' => now(),
            'reconciled_by' => $userId ?? auth()->id(),
        ]);
    }
}