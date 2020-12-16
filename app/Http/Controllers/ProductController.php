<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Http\Resources\Product as ProductResource;

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

    public function store(Request $request, Category $category)
    {
        $this->authorize("create", [Product::class, $category]);

        $fillable = $request->validate([
            "title" => ["required", "string", "max:255"],
            "content" => ["required", "string"]
        ]);

        $product = $this->products->createForCategory($category, $fillable);

        return new ProductResource($product);
    }

    public function update(Request $request, Category $category, Product $product)
    {
        $this->authorize("update", [$product, $category]);

        $fillable = $request->validate([
            "title" => ["required_without:content", "string", "max:255"],
            "content" => ["required_without:title", "string"]
        ]);

        $product = $this->products->update($product, $fillable);

        return new ProductResource($product);
    }

    public function destroy(Category $category, Product $product)
    {
        $this->authorize("forceDelete", [$product, $category]);

        $this->products->delete($product);

        return "true";
    }
}