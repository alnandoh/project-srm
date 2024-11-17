<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'name',
        'special_preference',
        'food_type',
        'budget',
        'note',
        'quantity',
        'end_registration',
        'delivery_date'
    ];

    protected $casts = [
        'end_registration' => 'datetime',
        'delivery_date' => 'datetime',
        'budget' => 'decimal:2'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function offering()
    {
        return $this->hasMany(Offering::class);
    }

    public function delivery()
    {
        return $this->hasMany(Delivery::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }
}
