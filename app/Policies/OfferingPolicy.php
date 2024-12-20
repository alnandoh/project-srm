<?php

namespace App\Policies;

use App\Models\Offering;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfferingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;    
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offering $offering): bool
    {
        return true;    
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 'Vendor';    
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offering $offering): bool
    {
        // Vendors can update their own offerings
        if ($user->role === 'Vendor') {
            return $user->id === $offering->vendor_id;
        }
        
        // Admins can only update status (handled in the form configuration)
        return $user->role === 'Admin';    
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offering $offering): bool
    {
        return $user->role == 'Vendor' ;    
    }

    public function deleteAny(User $user): bool
    {
        return $user->role == 'Vendor';    
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Offering $offering): bool
    {
        return $user->role == 'Vendor';    
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Offering $offering): bool
    {
        return $user->role == 'Vendor';    
    }
}
