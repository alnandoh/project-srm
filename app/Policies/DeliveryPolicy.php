<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;
use App\Models\Offering;
use Illuminate\Auth\Access\Response;

class DeliveryPolicy
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
    public function view(User $user, Delivery $delivery): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->role !== 'Vendor') {
            return false;
        }

        // Check if vendor has any accepted offerings without deliveries
        return Offering::where('vendor_id', $user->id)
            ->where('offering_status', 'accepted')
            ->whereDoesntHave('delivery')
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Vendor' && $user->id === $delivery->vendor_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Vendor' && $user->id === $delivery->vendor_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Vendor' && $user->id === $delivery->vendor_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Vendor' && $user->id === $delivery->vendor_id;
    }
}
