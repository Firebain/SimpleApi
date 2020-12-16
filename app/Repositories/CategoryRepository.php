<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

use App\Models\User;
use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    protected function getInstance()
    {
        return Category::class;
    }

    public function getByIdWithProducts(int $id): ?Category
    {
        return Category::with("products")->find($id);
    }

    public function createForUser(User $user, array $fillable): Category
    {
        return $user->categories()->create($fillable);
    }
}