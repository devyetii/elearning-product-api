<?php

namespace Tests\Unit\Repos;

use App\Models\Product;
use App\Models\Category;
use App\Repos\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = new ProductRepository();
    }

    public function test_get_all_products()
    {
        Category::factory()->create(['id' => 1, 'name' => 'Electronics']);
        Product::factory()->create(['name' => 'Product 1', 'category_id' => 1, 'rating' => 4.5]);
        Product::factory()->create(['name' => 'Product 2', 'category_id' => 1, 'rating' => 3.0]);

        $products = $this->productRepository->getAll();

        $this->assertCount(2, $products);
    }

    public function test_get_all_products_with_filters()
    {
        $cat1 = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->create(['name' => 'Product 1', 'category_id' => $cat1->id, 'rating' => 4.5]);
        Product::factory()->create(['name' => 'Product 2', 'category_id' => $cat1->id, 'rating' => 3.0]);

        $cat2 = Category::factory()->create(['name' => 'Clothing']);
        Product::factory()->create(['name' => 'Product 3', 'category_id' => $cat2->id, 'rating' => 4.0]);
        Product::factory()->create(['name' => 'Product 4', 'category_id' => $cat2->id, 'rating' => 2.5]);

        $filters = ['category_name' => 'Electronics'];
        $products = $this->productRepository->getAll($filters);

        $names = $products->pluck('name')->toArray();
        $this->assertCount(2, $names);
        $this->assertContains('Product 1', $names);
        $this->assertContains('Product 2', $names);

        // Filter by category ID
        $filters = ['category_id' => $cat2->id];
        $products = $this->productRepository->getAll($filters);

        $names = $products->pluck('name')->toArray();
        $this->assertCount(2, $names);
        $this->assertContains('Product 3', $names);
        $this->assertContains('Product 4', $names);

        // Filter by product name
        $filters = ['product_name' => 'Product 3'];
        $products = $this->productRepository->getAll($filters);

        $this->assertCount(1, $products);
        $this->assertEquals('Product 3', $products->first()->name);

        // Create other 5 projects for each category
        Product::factory()->count(5)->create(['category_id' => $cat1->id]);
        Product::factory()->count(5)->create(['category_id' => $cat2->id]);

        // Test pagination
        $products = $this->productRepository->getAll([], 'rating', 'desc', 0, 5);
        $this->assertCount(5, $products);
        $ratings = $products->pluck('rating')->toArray();
        $this->assertArrayDescending($ratings);
    }

    public function test_create_product()
    {
        $category = Category::factory()->create();
        $data = [
            'name' => 'New Product',
            'description' => 'Product Description',
            'price' => 99.99,
            'category_id' => $category->id,
            'rating' => 5.0
        ];

        $product = $this->productRepository->create($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('New Product', $product->name);
    }

    public function test_find_by_id()
    {
        $product = Product::factory()->create();

        $foundProduct = $this->productRepository->findById($product->id);

        $this->assertInstanceOf(Product::class, $foundProduct);
        $this->assertEquals($product->id, $foundProduct->id);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create();
        $data = ['name' => 'Updated Product'];

        $updatedProduct = $this->productRepository->update($product->id, $data);

        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Updated Product', $updatedProduct->name);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $result = $this->productRepository->delete($product->id);

        $this->assertTrue($result);
        $this->assertNull(Product::find($product->id));
    }

    private function assertArrayDescending(array $array)
    {
        $sorted = $array;
        rsort($sorted);
        $this->assertEquals($sorted, $array);
    }
}
