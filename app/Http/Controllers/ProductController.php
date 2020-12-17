<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Http\Resources\Product as ProductResource;
use App\Rules\AllExists;

class ProductController extends Controller
{
    private CategoryRepository $categories;
    private ProductRepository $products;

    public function __construct(
        CategoryRepository $categories,
        ProductRepository $products
    ) {
        $this->categories = $categories;
        $this->products = $products;
    }

    public function index($category_id)
    {
        $category = $this->categories->getByIdWithProducts($category_id);

        if (!$category) {
            return abort(404);
        }

        return ProductResource::collection($category->products);
    }

    public function store(Request $request)
    {
        $fillable = $request->validate([
            "categories" => [
                "nullable",
                "array",
                new AllExists("categories", "id")
            ],
            "title" => ["required", "string", "max:255"],
            "content" => ["required", "string"]
        ]);

        $user = $request->user();

        $product = $this->products->createForUser($user, $fillable);

        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize("update", $product);

        $fillable = $request->validate([
            "categories" => [
                "nullable",
                "array",
                new AllExists("categories", "id")
            ],
            "title" => ["required_without:content", "string", "max:255"],
            "content" => ["required_without:title", "string"]
        ]);

        $product = $this->products->update($product, $fillable);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $this->authorize("forceDelete", $product);

        $this->products->delete($product);

        return "true";
    }
}