<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorBankAccount;
use Illuminate\Auth\Access\Response;

class VendorBankAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VendorBankAccount $vendorBankAccount): bool
    {
        if ($user->role === 'Admin') {
            return true;
        }
        
        return $user->id === $vendorBankAccount->vendor_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VendorBankAccount $vendorBankAccount): bool
    {
        return $user->id === $vendorBankAccount->vendor_id;
    }

    public function delete(User $user, VendorBankAccount $vendorBankAccount): bool
    {
        return $user->id === $vendorBankAccount->vendor_id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'Admin';
    }
} 