<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

use App\Models\Product;
use App\Models\Category;

class ProductRepository extends BaseRepository
{
    protected function getInstance()
    {
        return Product::class;
    }

    public function createForCategory(Category $category, array $fillable): Product
    {
        return $category->products()->create($fillable);
    }
}