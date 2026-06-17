<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    // Mostrar listado completo de productos NoSQL
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json(ProductResource::collection($products));
    }

    // Alta de producto con código autogenerado
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json(new ProductResource($product), 201);
    }

    // Visualización de un producto individual por ID
    public function show(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        return response()->json(new ProductResource($product));
    }

    // Edición de producto
    public function update(StoreProductRequest $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        return response()->json(new ProductResource($product));
    }

    // Eliminación de producto
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }
}