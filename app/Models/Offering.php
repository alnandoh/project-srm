<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tender_id',
        'vendor_id',
        'title',
        'description',
        'offer',
        'delivery_cost',
        'payment_type',
        'dp_amount',
        'dp_paid',
        'full_paid',
        'image',
        'offering_status'
    ];

    protected $casts = [
        'offer' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'dp_paid' => 'boolean',
        'full_paid' => 'boolean'
    ];

        // Add accessor for total amount
    public function getTotalAmountAttribute(): float
    {
        return $this->offer + $this->delivery_cost;
    }

    // Add method to calculate DP amount (30% of total)
    public function calculateDpAmount(): float
    {
        return $this->total_amount * 0.3;
    }

    // Override setter for payment_type to automatically set dp_amount
    public function setPaymentTypeAttribute($value)
    {
        $this->attributes['payment_type'] = $value;
        if ($value === 'dp') {
            $this->attributes['dp_amount'] = $this->calculateDpAmount();
        }
    }

    // Helper method to check if payment requirements are met for delivery
    public function isReadyForDelivery(): bool
    {
        if ($this->payment_type === 'full') {
            return $this->full_paid;
        }
        return $this->dp_paid;
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class); // Adjust the relationship type as needed
    }
}
