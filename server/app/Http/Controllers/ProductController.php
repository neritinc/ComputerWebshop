<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductParameter; // Ezt be kell importálni!
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Betöltjük a kategóriát és a paramétereket is, hogy lássuk a frontenden
        $products = Product::with(['category', 'parameters.parameter.unit'])->get();
        return response()->json($products);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);
        
        $validated = $request->validated();
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show($id) // Átírtam ID-ra a biztosabb működésért
    {
        $product = Product::with(['category', 'parameters.parameter.unit'])->find($id);
        if (!$product) return response()->json(['message' => 'Termék nem található'], 404);
        return response()->json($product);
    }

    /**
     * Termék és extra paraméterek frissítése
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
        
        // 1. Alapadatok frissítése (név, ár, leírás, stb.)
        $product->update($request->validated());

        // 2. Extra paraméterek (p1, p2, p3...) frissítése
        // Ha a request.rest-ben küldesz "params" tömböt
        if ($request->has('params')) {
            foreach ($request->params as $paramId => $value) {
                ProductParameter::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'parameter_id' => $paramId
                    ],
                    ['value' => $value]
                );
            }
        }

        // Visszaadjuk a frissített terméket a paramétereivel együtt
        return response()->json([
            'message' => 'Sikeres frissítés',
            'data' => $product->load('parameters.parameter.unit')
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);
        
        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}