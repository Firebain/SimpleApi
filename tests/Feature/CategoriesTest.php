<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;

class CategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAuthenticationRequired()
    {
        $this->assertAuthenticationRequired("/categories", "post");
        $this->assertAuthenticationRequired("/categories/1", "patch");
        $this->assertAuthenticationRequired("/categories/1", "delete");
    }

    public function testIndexReturnsCorrectResult()
    {
        Category::factory()
            ->count(8)
            ->create();

        $this
            ->getJson('/categories')
            ->assertOk()
            ->assertJsonStructure([[
                "id",
                "title",
            ]])
            ->assertJsonCount(8);
    }

    public function testUserCantStoreCategoryWithoutData()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this
            ->postJson("/categories")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCantStoreCategoryWithExistingTitle()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $data = [
            "title" => $this->faker->sentence(3)
        ];

        Category::factory()->create($data);

        $this
            ->postJson("/categories", $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCanStoreNewCategory()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $data = [
            "title" => $this->faker->sentence(3)
        ];

        $this
            ->postJson("/categories", $data)
            ->assertCreated()
            ->assertJsonStructure([
                "id",
                "title",
            ]);

        $this->assertDatabaseHas("categories", $data);
    }

    public function testUserCantUpdateCategoryWithoutData()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $this
            ->patchJson("/categories/{$category->id}")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCantUpdateCategoryWithExistingTitle()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $data = [
            "title" => $this->faker->sentence(3)
        ];

        Category::factory()->create($data);

        $this
            ->patchJson("/categories/{$category->id}", $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCanUpdateCategory()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $data = [
            "title" => $this->faker->sentence(3)
        ];

        $this
            ->patchJson("/categories/{$category->id}", $data)
            ->assertOk()
            ->assertJsonStructure([
                "id",
                "title",
            ]);

        $this->assertDatabaseHas("categories", $data);
    }

    public function testUserCanDeleteCategory()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $this
            ->deleteJson("/categories/{$category->id}")
            ->assertOk()
            ->assertSeeText("true");

        $this->assertDeleted($category);
    }
}