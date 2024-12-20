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
        'invoice_image',
        'payment_status'
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
}
