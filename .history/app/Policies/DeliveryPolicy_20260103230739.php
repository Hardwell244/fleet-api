<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Delivery;

class DeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Delivery $delivery): bool
    {
        return $user->company_id === $delivery->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Delivery $delivery): bool
    {
        return $user->company_id === $delivery->company_id;
    }

    public function delete(User $user, Delivery $delivery): bool
    {
        return $user->company_id === $delivery->company_id;
    }
}
