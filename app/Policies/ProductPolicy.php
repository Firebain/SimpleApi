<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class ProductPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Category $category)
    {
        return $user->can("update", $category);
    }

    public function update(User $user, Product $product, Category $category)
    {
        return $user->can("update", $category);
    }

    public function forceDelete(User $user, Product $product, Category $category)
    {
        return $user->can("update", $category);
    }
}