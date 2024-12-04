<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['Admin', 'Vendor']);
    }

    public function vendorCompany()
    {
        return $this->hasOne(VendorCompany::class, 'vendor_id');
    }

    public function vendorBankAccount()
    {
        return $this->hasMany(VendorBankAccount::class, 'vendor_id');
    }

    public function offering()
    {
        return $this->hasMany(Offering::class, 'vendor_id');
    }

    public function delivery()
    {
        return $this->hasMany(Delivery::class, 'vendor_id');
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'vendor_id');
    }

    public function rating()
    {
        return $this->hasMany(Rating::class, 'vendor_id');
    }

    public function adminTenders()
    {
        return $this->hasMany(Tender::class, 'admin_id');
    }

    public function adminPayments()
    {
        return $this->hasMany(Payment::class, 'admin_id');
    }
}
