<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'invoice_number', 'amount',
        'payment_method', 'payment_status',
        'type', 'description', 'payment_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $amount = (float) ($this->attributes['amount'] ?? 0);
        return '₱' . number_format($amount, 2);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other',
            default => ucfirst($this->payment_method),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'TXN-' . date('Ymd') . '-';
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public static function createForMembership(Membership $membership): ?self
    {
        if ($membership->billed) {
            return null;
        }

        $plan = $membership->membershipPlan;

        if (!$plan) {
            return null;
        }

        $billing = self::create([
            'user_id' => $membership->user_id,
            'invoice_number' => self::generateInvoiceNumber(),
            'type' => 'membership',
            'description' => $plan->name . ' Membership Plan',
            'amount' => $plan->price,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'payment_date' => now(),
        ]);

        return $billing;
    }

    public static function createForAvailedService(AvailedService $availedService): ?self
    {
        if ($availedService->billed) {
            return null;
        }

        $gymService = $availedService->gymService;

        if (!$gymService) {
            return null;
        }

        $billing = self::create([
            'user_id' => $availedService->user_id,
            'invoice_number' => self::generateInvoiceNumber(),
            'type' => 'service',
            'description' => $gymService->name,
            'amount' => $gymService->price,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'payment_date' => now(),
        ]);

        return $billing;
    }
}
