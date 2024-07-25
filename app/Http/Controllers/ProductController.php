<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Repos\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category_name', 'category_id', 'product_name']);
        $orderBy = $request->get('order_by', 'rating');
        $direction = $request->get('direction', 'asc');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        $products = $this->productRepository->getAll($filters, $orderBy, $direction, $offset, $limit);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        $data = $request->validated();
        $product = $this->productRepository->create($data);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->productRepository->findById($id);

        if ($product) {
            return new ProductResource($product);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateProductRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $data = $request->validated();
        $product = $this->productRepository->update($id, $data);

        if ($product) {
            return new ProductResource($product);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->productRepository->delete($id);

        if ($result) {
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }
}
