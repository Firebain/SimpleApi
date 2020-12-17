<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Http\Resources\Category as CategoryResource;

class CategoryController extends Controller
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        return CategoryResource::collection($this->categories->all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:255", "unique:categories"]
        ]);

        $category = $this->categories->create($validated);

        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:255", "unique:categories"]
        ]);

        $category = $this->categories->update($category, $validated);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $this->categories->delete($category);

        return "true";
    }
}