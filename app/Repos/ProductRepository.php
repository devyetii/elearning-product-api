<?php

namespace App\Repos;

use App\Models\Product;
use App\Models\ProductRating;
use App\Models\ProductReview;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    /**
     * Retrieve all products with optional filters, sorting, and pagination.
     *
     * @param array $filters
     * @param string $orderBy
     * @param string $direction
     * @param int $offset
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], $orderBy = 'rating', $direction = 'asc', $offset = 0, $limit = 10)
    {
        $query = Product::query();

        if (isset($filters['category_name'])) {
            $rel = $query->getRelation('category');
            $query->whereHas($rel, function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['category_name'] . '%');
            });
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['product_name'])) {
            $query->where('name', 'like', '%' . $filters['product_name'] . '%');
        }

        return $query->orderBy($orderBy, $direction)
                     ->skip($offset)
                     ->take($limit)
                     ->paginate($limit);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * Retrieve a product by its ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function findById($id)
    {
        return Product::find($id);
    }

    /**
     * Update a product.
     *
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function update($id, array $data)
    {
        $product = $this->findById($id);

        if ($product) {
            $product->update($data);
            return $product;
        }

        return null;
    }

    /**
     * Update product rating
     * 
     * @param int $productId
     * @param int $userId
     * @param float $rating
     * @param string $review
     */
    public function addReview($productId, $userId, $rating, $review)
    {
        ProductReview::updateOrCreate(
            ['product_id' => $productId, 'user_id' => $userId],
            ['rating' => $rating, 'review' => $review]
        );

        // Cache average rating to product table
        $product = $this->findById($productId);
        $product->rating = $product->reviews()->avg('rating');

        return $product->save();
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $product = $this->findById($id);

        if ($product) {
            return $product->delete();
        }

        return false;
    }
}
