<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

use App\Models\User;
use App\Models\Category;

class CategoryRepository
{
    public function all(): Collection
    {
        return Category::all();
    }

    public function update(Category $category, $fillable)
    {
        $category->fill($fillable);
        $category->save();

        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
    }

    public function createForUser(User $user, array $fillable): Category
    {
        return $user->categories()->create($fillable);
    }
}