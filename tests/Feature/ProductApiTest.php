<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_requires_authentication()
    {
        $this->json('GET', '/api/products')
            ->assertStatus(401);
    }

    public function test_it_allows_authenticated_user_to_get_products()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $this->json('GET', '/api/products')
            ->assertStatus(200);
    }

    public function test_it_allows_admin_to_create_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
        ];

        $this->json('POST', '/api/products', $data)
            ->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_it_prevents_non_admin_from_creating_product()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => 1,
        ];

        $this->json('POST', '/api/products', $data)
            ->assertStatus(403);
    }

    public function test_it_validates_product_creation_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $data = [
            'name' => '',
            'price' => 'invalid_price',
            'category_id' => null,
        ];

        $this->json('POST', '/api/products', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    public function test_it_allows_authenticated_user_to_get_product_reviews()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Passport::actingAs($user);

        $this->json('GET', "/api/products/{$product->id}/reviews")
            ->assertStatus(200);
    }

    public function test_it_allows_authenticated_user_to_add_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Passport::actingAs($user);

        $data = [
            'rating' => 4.5,
            'review' => 'Great product!'
        ];

        $this->json('POST', "/api/products/{$product->id}/reviews", $data)
            ->assertStatus(200)
            ->assertJsonFragment(['review' => 'Great product!']);
    }

    public function test_it_validates_review_data()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Passport::actingAs($user);

        $data = [
            'rating' => 10, // Invalid rating
            'review' => ''
        ];

        $this->json('POST', "/api/products/{$product->id}/reviews", $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_it_allows_admin_to_update_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        Passport::actingAs($admin);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Product',
            'price' => 150.00,
            'category_id' => $category->id,
        ];

        $this->json('PUT', "/api/products/{$product->id}", $data)
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Product']);
    }

    public function test_it_prevents_non_admin_from_updating_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Updated Product',
            'price' => 150.00,
            'category_id' => 1,
        ];

        $this->json('PUT', "/api/products/{$product->id}", $data)
            ->assertStatus(403);
    }

    public function test_it_allows_admin_to_delete_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        Passport::actingAs($admin);

        $this->json('DELETE', "/api/products/{$product->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'Product deleted successfully']);
    }

    public function test_it_prevents_non_admin_from_deleting_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Passport::actingAs($user);

        $this->json('DELETE', "/api/products/{$product->id}")
            ->assertStatus(403);
    }
}
