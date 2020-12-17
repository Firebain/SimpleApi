<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class ProductPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Product $product)
    {
        return $product->user_id === $user->id;
    }

    public function forceDelete(User $user, Product $product)
    {
        return $product->user_id === $user->id;
    }
}