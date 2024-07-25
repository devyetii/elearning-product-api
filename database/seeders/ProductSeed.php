<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 test products for a new category
        $category = \App\Models\Category::factory()->create([
            'name' => 'Category 1',
        ]);

        \App\Models\Product::factory(10)->create([
            'category_id' => $category->id,
        ]);

        // Create 5 test products for another category
        $category = \App\Models\Category::factory()->create([
            'name' => 'Category 2',
        ]);

        \App\Models\Product::factory(5)->create([
            'category_id' => $category->id,
        ]);
    }
}
