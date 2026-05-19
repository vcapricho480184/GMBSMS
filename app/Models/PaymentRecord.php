<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_transaction_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'recorded_by',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function billingTransaction()
    {
        return $this->belongsTo(BillingTransaction::class);
    }

    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount_paid ?? 0, 2);
    }
}
