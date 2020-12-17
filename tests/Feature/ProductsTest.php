<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class ProductsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAuthenticationRequired()
    {
        $this->assertAuthenticationRequired("/products", "post");
        $this->assertAuthenticationRequired("/products/1", "patch");
        $this->assertAuthenticationRequired("/products/1", "delete");
    }

    public function testIndexReturnsCorrectResult()
    {
        $category = Category::factory()
            ->hasProducts(8)
            ->create();

        $this
            ->getJson("/categories/{$category->id}/products")
            ->assertOk()
            ->assertJsonStructure([[
                "id",
                "categories",
                "title",
                "content"
            ]])
            ->assertJsonCount(8);
    }

    public function testUserCantStoreWithoutData()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this
            ->postJson("/products")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title",
                "content"
            ]);
    }

    public function testUserCantStoreWithUnexistedCategory()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $data = [
            "categories" => [$category->id + 1, $category->id],
            "title" => $this->faker->sentence(3),
            "content" => $this->faker->text
        ];

        $this
            ->postJson("/products", $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "categories"
            ]);
    }

    public function testUserCanStoreNewProduct()
    {
        $category = Category::factory()->create();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $checkCreated = function ($data, $categories_ids) use ($user) {
            $response = $this->postJson("/products", $data);

            $response
                ->assertCreated()
                ->assertJsonStructure([
                    "id",
                    "categories",
                    "title",
                    "content"
                ]);

            $this->assertDatabaseHas(
                "products",
                ["user_id" => $user->id] + Arr::except($data, "categories")
            );

            $actual_categories_ids = $response->original->categories()
                ->pluck("id")
                ->toArray();

            $this->assertEquals($categories_ids, $actual_categories_ids);
        };

        $checkCreated([
            "categories" => [$category->id],
            "title" => $this->faker->sentence(3),
            "content" => $this->faker->text
        ], [$category->id]);

        $checkCreated([
            "title" => $this->faker->sentence(3),
            "content" => $this->faker->text
        ], []);
    }

    public function testUserCantUpdateWithoutData()
    {
        $product = Product::factory()->create();

        Sanctum::actingAs($product->user);

        $this
            ->patchJson("/products/{$product->id}")
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "title",
                "content"
            ]);
    }

    public function testUserCantUpdateNotHisProduct()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $product = Product::factory()->create();

        $this
            ->patchJson("/products/{$product->id}")
            ->assertForbidden();
    }

    public function testUserCanUpdateProduct()
    {
        [$firstCategory, $secondCategory] = Category::factory()
            ->count(2)
            ->create();

        $product = Product::factory()->create();

        Sanctum::actingAs($product->user);

        $data = [
            "categories" => [$firstCategory->id, $secondCategory->id],
            "title" => $this->faker->sentence(3),
            "content" => $this->faker->text
        ];

        $response = $this->patchJson("/products/{$product->id}", $data);

        $response
            ->assertOk()
            ->assertJsonStructure([
                "id",
                "categories",
                "title",
                "content"
            ]);

        $this->assertDatabaseHas("products", Arr::except($data, "categories"));

        $categories_ids = $response->original->categories()
                ->pluck("id")
                ->toArray();

        $this->assertEquals([$firstCategory->id, $secondCategory->id], $categories_ids);
    }

    public function testUserCantDeleteNotHisProduct()
    {
        $product = Product::factory()->create();

        Sanctum::actingAs(
            User::factory()->create()
        );

        $this
            ->deleteJson("/products/{$product->id}")
            ->assertForbidden();
    }

    public function testUserCanDeleteProduct()
    {
        $product = Product::factory()->create();

        Sanctum::actingAs($product->user);

        $this
            ->deleteJson("/products/{$product->id}")
            ->assertOk()
            ->assertSeeText("true");

        $this->assertDeleted($product);
    }
}
