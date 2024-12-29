<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Auth\Access\Response;

class RatingPolicy
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
    public function view(User $user, Rating $rating): bool
    {
        return $user->role === 'Admin' || 
            ($user->role === 'Vendor' && $user->id === $rating->vendor_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         if ($user->role === 'Vendor') {
            // Vendor can create rating if delivery is completed and payment is full
            return Delivery::whereHas('offering', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            })
            ->whereHas('payment', function ($query) {
                $query->where('payment_type', 'full')
                    ->where('status', 'confirmed');
            })
            ->where('status', 'completed')
            ->exists();
        }

        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rating $rating): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Rating $rating): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Rating $rating): bool
    {
        return $user->role == 'Admin';
    }
}
