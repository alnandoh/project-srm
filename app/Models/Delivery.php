<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delivery';

    protected $fillable = [
        'tender_id',
        'vendor_id',
        'offering_id',
        'shipping_track_number',
        'courier',
        'status',
        'quantity_received',
        'quality_check',
        'quantity_check',
        'qc_notes'
    ];

    protected $casts = [
        'quality_check' => 'boolean',
        'quantity_check' => 'boolean',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'tender_id', 'tender_id')
            ->where('payments.vendor_id', $this->vendor_id);
    }
}
