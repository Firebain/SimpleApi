<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\CategoryCollection as CategoriesResource;

class CategoryController extends Controller
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        return new CategoriesResource($this->categories->all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:255"]
        ]);

        $user = $request->user();

        $category = $this->categories->createForUser($user, $validated);

        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize("update", $category);

        $validated = $request->validate([
            "title" => ["required", "string", "max:255"]
        ]);

        $category = $this->categories->update($category, $validated);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $this->authorize("forceDelete", $category);

        $this->categories->destroy($category);

        return "true";
    }
}