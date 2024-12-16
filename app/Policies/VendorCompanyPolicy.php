<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorCompany;
use Illuminate\Auth\Access\Response;

class VendorCompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role == 'Vendor';
    }

    public function view(User $user, VendorCompany $vendorCompany): bool
    {
        return $user->role == 'Vendor';
    }

    public function create(User $user): bool
    {
        return $user->role == 'Vendor';
    }

    public function update(User $user, VendorCompany $vendorCompany): bool
    {
        return $user->role === 'Vendor' && $user->id === $vendorCompany->vendor_id;
    }

    public function delete(User $user, VendorCompany $vendorCompany): bool
    {
        return $user->role === 'Vendor' && $user->id === $vendorCompany->vendor_id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'Vendor';
    }
} 