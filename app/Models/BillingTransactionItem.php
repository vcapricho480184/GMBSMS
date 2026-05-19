<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_transaction_id',
        'item_type',
        'membership_id',
        'availed_service_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function billingTransaction()
    {
        return $this->belongsTo(BillingTransaction::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function availedService()
    {
        return $this->belongsTo(AvailedService::class);
    }
}
