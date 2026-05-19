<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'invoice_number',
        'amount', 'subtotal', 'discount_percentage', 'discount_amount',
        'tax_rate', 'tax_amount', 'payment_method', 'payment_status',
        'type', 'description', 'payment_date', 'notes', 'due_date',
        'days_until_due', 'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function availedService()
    {
        return $this->belongsTo(AvailedService::class);
    }

    public function items()
    {
        return $this->hasMany(BillingTransactionItem::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class);
    }

    protected static function booting()
    {
        static::creating(function ($model) {
            // Auto-calculate due_date if not set and payment is pending
            if (!$model->due_date && $model->payment_status === 'pending') {
                $daysUntilDue = $model->days_until_due ?? 30;
                $model->due_date = now()->addDays($daysUntilDue);
            }
        });
    }

    public function getFormattedAmountAttribute(): string
    {
        /** @var float $amount */
        $amount = (float) ($this->attributes['amount'] ?? 0);

        return '₱' . number_format($amount, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        /** @var float $subtotal */
        $subtotal = (float) ($this->attributes['subtotal'] ?? $this->attributes['amount'] ?? 0);

        return '₱' . number_format($subtotal, 2);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return '₱' . number_format($this->discount_amount ?? 0, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '₱' . number_format($this->tax_amount ?? 0, 2);
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

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->payment_status === 'paid') {
            return false;
        }

        /** @var Carbon $dueDate */
        $dueDate = Carbon::parse($this->getRawOriginal('due_date'));

        return $dueDate->isPast();
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
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
        if ($membership->billingTransactions()->exists()) {
            return $membership->billingTransactions()->latest('created_at')->first();
        }

        $plan = $membership->membershipPlan;

        if (!$plan) {
            return null;
        }

        return DB::transaction(function () use ($membership, $plan) {
            $billing = self::create([
                'user_id' => $membership->user_id,
                'invoice_number' => self::generateInvoiceNumber(),
                'type' => 'membership',
                'description' => $plan->name . ' Membership Plan',
                'amount' => $plan->price,
                'subtotal' => $plan->price,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'payment_date' => now(),
                'notes' => null,
            ]);

            $billing->items()->create([
                'item_type' => 'membership',
                'membership_id' => $membership->id,
                'availed_service_id' => null,
                'description' => $plan->name . ' Membership Plan',
                'quantity' => 1,
                'unit_price' => $plan->price,
                'amount' => $plan->price,
            ]);

            return $billing;
        });
    }

    public static function createForAvailedService(AvailedService $availedService): ?self
    {
        if ($availedService->billingTransaction()->exists()) {
            return $availedService->billingTransaction()->latest('created_at')->first();
        }

        $gymService = $availedService->gymService;

        if (!$gymService) {
            return null;
        }

        return DB::transaction(function () use ($availedService, $gymService) {
            $billing = self::create([
                'user_id' => $availedService->user_id,
                'invoice_number' => self::generateInvoiceNumber(),
                'type' => 'service',
                'description' => $gymService->name,
                'amount' => $gymService->price,
                'subtotal' => $gymService->price,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'payment_date' => now(),
                'notes' => null,
            ]);

            $billing->items()->create([
                'item_type' => 'service',
                'membership_id' => null,
                'availed_service_id' => $availedService->id,
                'description' => $gymService->name,
                'quantity' => 1,
                'unit_price' => $gymService->price,
                'amount' => $gymService->price,
            ]);

            return $billing;
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }
}
