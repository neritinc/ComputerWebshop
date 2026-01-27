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
     * Termékek listázása kapcsolatokkal
     */
    public function index()
    {
        try {
            // JAVÍTVA: A parameters kapcsolaton keresztül töltjük be a unit-ot.
            // Feltételezve, hogy a Parameter modellben van 'unit' kapcsolat.
            $products = Product::with(['category', 'company', 'parameters.unit'])->get();
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error("Hiba a termékek lekérdezésekor: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Hiba a kapcsolatok betöltésekor.',
                'message' => $e->getMessage(),
                'data' => Product::all() // Vészmegoldás: adatok kapcsolatok nélkül
            ], 500);
        }
    }

    /**
     * Új termék mentése
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $this->authorize('create', Product::class);
            
            $validated = $request->validated();
            $product = Product::create($validated);
            
            return response()->json([
                'message' => 'Termék sikeresen létrehozva',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Mentési hiba',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Egy termék részletei
     */
    public function show($id)
    {
        try {
            // JAVÍTVA: Itt is a helyes kapcsolati láncot használjuk
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
     * Termék és paraméterek frissítése
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);
            
            // 1. Alapadatok frissítése
            $product->update($request->validated());

            // 2. Extra paraméterek frissítése Many-to-Many esetén a sync() vagy attach() ajánlott,
            // de ha maradunk a ProductParameter modellnél:
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