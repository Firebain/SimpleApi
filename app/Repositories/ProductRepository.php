<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductRepository extends BaseRepository
{
    protected function getInstance()
    {
        return Product::class;
    }

    public function createForUser(User $user, array $fillable): Product
    {
        $data = Arr::except($fillable, "categories");

        $product = $user->products()->create($data);

        if (isset($fillable["categories"])) {
            $product->categories()->sync($fillable["categories"]);
        }

        return $product;
    }

    public function update($product, $fillable)
    {
        $data = Arr::except($fillable, "categories");

        $product->fill($fillable);
        $product->save();

        if (isset($fillable["categories"])) {
            $product->categories()->sync($fillable["categories"]);
        }

        return $product;
    }
}
