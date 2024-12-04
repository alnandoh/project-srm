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
        'image',
        'offering_status'
    ];

    protected $casts = [
        'offer' => 'decimal:2'
    ];

    // Ensure total_price is always calculated when quantity or unit_price changes
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotalPrice();
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        $this->calculateTotalPrice();
    }

    private function calculateTotalPrice()
    {
        $quantity = $this->attributes['quantity'] ?? 0;
        $unitPrice = $this->attributes['unit_price'] ?? 0;
        $this->attributes['total_price'] = $quantity * $unitPrice;
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
}
