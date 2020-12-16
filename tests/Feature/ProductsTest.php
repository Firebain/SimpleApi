<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;

class ProductsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAuthenticationRequired()
    {
        $this->assertAuthenticationRequired("/categories/1/products", "post");
        $this->assertAuthenticationRequired("/categories/1/products/1", "patch");
        $this->assertAuthenticationRequired("/categories/1/products/1", "delete");
    }

    public function testIndexReturnsCorrectResult()
    {
        $user = User::factory()
            ->has(
                Category::factory()
                    ->hasProducts(8)
            )
            ->create();

        $category = $user->categories->first();

        $this
            ->getJson("/categories/{$category->id}/products")
            ->assertOk()
            ->assertJsonStructure([[
                "id",
                "title",
                "content"
            ]])
            ->assertJsonCount(8);
    }

    public function testUserCantStoreNotInHisCategory()
    {
        [$firstUser, $secondUser] = User::factory()
            ->count(2)
            ->hasCategories()
            ->create();

        Sanctum::actingAs($secondUser);

        $category = $firstUser->categories->first();

        $this
            ->postJson("/categories/{$category->id}/products")
            ->assertForbidden();
    }

    public function testUserCantStoreWithoutData()
    {
        $user = User::factory()
            ->hasCategories()
            ->create();

        $category = $user->categories->first();

        Sanctum::actingAs($user);

        $this
            ->postJson("/categories/{$category->id}/products")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title",
                "content"
            ]);
    }

    public function testUserCanStoreNewProduct()
    {
        $user = User::factory()
            ->hasCategories()
            ->create();

        $category = $user->categories->first();

        Sanctum::actingAs($user);

        $productTitle = $this->faker->sentence(3);
        $productContent = $this->faker->text;

        $response = $this
            ->postJson("/categories/{$category->id}/products", [
                "title" => $productTitle,
                "content" => $productContent
            ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                "id",
                "title",
                "content"
            ]);

        $this->assertEquals($response->original->title, $productTitle);
        $this->assertEquals($response->original->content, $productContent);
    }

    public function testUserCantUpdateNotInHisCategory()
    {
        [$firstUser, $secondUser] = User::factory()
            ->count(2)
            ->has(
                Category::factory()
                    ->hasProducts()
            )
            ->create();

        Sanctum::actingAs($secondUser);

        $category = $firstUser->categories->first();
        $product = $category->products->first();

        $this
            ->patchJson("/categories/{$category->id}/products/{$product->id}")
            ->assertForbidden();
    }

    public function testUserCantUpdateWithoutData()
    {
        $user = User::factory()
            ->has(
                Category::factory()
                    ->hasProducts()
            )
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();
        $product = $category->products->first();

        $this
            ->patchJson("/categories/{$category->id}/products/{$product->id}")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title",
                "content"
            ]);
    }

    public function testUserCanUpdateProduct()
    {
        $user = User::factory()
            ->has(
                Category::factory()
                    ->hasProducts()
            )
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();
        $product = $category->products->first();

        $productTitle = $this->faker->sentence(3);
        $productContent = $this->faker->text;

        $response = $this->patchJson("/categories/{$category->id}/products/{$product->id}", [
            "title" => $productTitle,
            "content" => $productContent
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                "id",
                "title",
                "content"
            ]);

        $this->assertEquals($response->original->title, $productTitle);
        $this->assertEquals($response->original->content, $productContent);
    }

    public function testUserCantDeleteNotInHisCategory()
    {
        [$firstUser, $secondUser] = User::factory()
            ->count(2)
            ->has(
                Category::factory()
                    ->hasProducts()
            )
            ->create();

        Sanctum::actingAs($secondUser);

        $category = $firstUser->categories->first();
        $product = $category->products->first();

        $this
            ->deleteJson("/categories/{$category->id}/products/{$product->id}")
            ->assertForbidden();
    }

    public function testUserCanDeleteProduct()
    {
        $user = User::factory()
            ->has(
                Category::factory()
                    ->hasProducts()
            )
            ->create();

        Sanctum::actingAs($user);

        $category = $user->categories->first();
        $product = $category->products->first();

        $this
            ->deleteJson("/categories/{$category->id}/products/{$product->id}")
            ->assertOk()
            ->assertSeeText("true");

        $this->assertDeleted($product);
    }
}