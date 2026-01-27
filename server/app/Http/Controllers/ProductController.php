<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductParameter;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Termékek listázása szűréssel és kereséssel
     */
   public function index(Request $request) // 1. Itt átvesszük a kérést
{
    try {
        $query = Product::with(['category', 'company', 'parameters.unit']);

        // 2. Szűrés kategóriára, ha van az URL-ben
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Keresés névben vagy leírásban, ha van az URL-ben
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        return response()->json($query->get());
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Új termék mentése
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $this->authorize('create', Product::class);
            $product = Product::create($request->validated());
            
            return response()->json([
                'message' => 'Termék sikeresen létrehozva',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Mentési hiba', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Egy termék részletei
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'company', 'parameters.unit'])->find($id);
            
            if (!$product) {
                return response()->json(['message' => 'Termék nem található'], 404);
            }
            
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Termék frissítése
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);
            
            $product->update($request->validated());

            // Paraméterek frissítése (ha érkezik 'params' tömb)
            if ($request->has('params')) {
                foreach ($request->params as $paramId => $value) {
                    ProductParameter::updateOrCreate(
                        ['product_id' => $product->id, 'parameter_id' => $paramId],
                        ['value' => $value]
                    );
                }
            }

            return response()->json([
                'message' => 'Sikeres frissítés',
                'data' => $product->load(['category', 'company', 'parameters.unit'])
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Termék törlése
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);
            $product->delete();
            
            return response()->json(['message' => 'Termék sikeresen törölve']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}