<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use App\Models\User;

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
        User::factory()
            ->count(2)
            ->hasCategories(4)
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
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this
            ->postJson("/categories")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCanStoreNewCategory()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $categoryTitle = $this->faker->sentence(3);

        $response = $this
            ->postJson("/categories", [
                "title" => $categoryTitle
            ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                "id",
                "title",
            ]);

        $this->assertDatabaseHas("categories", [
            "title" => $categoryTitle
        ]);

        $this->assertEquals($response->original->user_id, $user->id);
        $this->assertEquals($response->original->title, $categoryTitle);
    }

    public function testUserCantUpdateNotHisCategory()
    {
        [$firstUser, $secondUser] = User::factory()
            ->count(2)
            ->hasCategories(4)
            ->create();

        Sanctum::actingAs($secondUser);

        $category = $firstUser->categories->first();

        $this
            ->patchJson("/categories/{$category->id}")
            ->assertForbidden();
    }

    public function testUserCantUpdateCategoryWithoutData()
    {
        $user = User::factory()
            ->hasCategories(4)
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();

        $this
            ->patchJson("/categories/{$category->id}")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title"
            ]);
    }

    public function testUserCanUpdateCategory()
    {
        $user = User::factory()
            ->hasCategories(4)
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();

        $categoryTitle = $this->faker->sentence(3);

        $response = $this->patchJson("/categories/{$category->id}", [
            "title" => $categoryTitle
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                "id",
                "title",
            ]);

        $this->assertEquals($response->original->title, $categoryTitle);
    }

    public function testUserCantDeleteNotHisCategory()
    {
        [$firstUser, $secondUser] = User::factory()
            ->count(2)
            ->hasCategories(4)
            ->create();

        Sanctum::actingAs($secondUser);

        $category = $firstUser->categories->first();

        $this
            ->deleteJson("/categories/{$category->id}")
            ->assertForbidden();
    }

    public function testUserCanDeleteCategory()
    {
        $user = User::factory()
            ->hasCategories(4)
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();

        $this
            ->deleteJson("/categories/{$category->id}")
            ->assertOk()
            ->assertSeeText("true");

        $this->assertDeleted($category);
    }
}