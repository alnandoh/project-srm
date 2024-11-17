<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'tender_id',
        'offering_id',
        'work_quality',
        'timelines',
        'communication'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
