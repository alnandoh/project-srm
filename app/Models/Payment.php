<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'tender_id',
        'vendor_id',
        'delivery_id',
        'amount',
        'payment_type',
        'payment_notes',
        'dp_amount',
        'invoice_image',
        'payment_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'payment_status' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
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
        return $this->hasOne(Rating::class, 'tender_id', 'tender_id')
            ->where('ratings.vendor_id', $this->vendor_id);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function offering()
{
    return $this->belongsTo(Offering::class);
}
}
