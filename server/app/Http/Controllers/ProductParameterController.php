<?php

namespace App\Http\Controllers;

use App\Models\ProductParameter;
use Illuminate\Http\Request;

class ProductParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     * Lekéri az összes product_parameter-t a kapcsolódó termékkel és paraméterrel.
     */
    public function index()
    {
        $params = ProductParameter::with(['product', 'parameter'])->get();
        return response()->json($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'parameter_id' => 'required|exists:parameters,id',
            'value' => 'required|string|max:191',
        ]);

        $param = ProductParameter::create($validated);

        return response()->json($param, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductParameter $product_parameter)
    {
        $product_parameter->load(['product', 'parameter']); // Kapcsolatok betöltése
        return response()->json($product_parameter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductParameter $product_parameter)
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'parameter_id' => 'sometimes|required|exists:parameters,id',
            'value' => 'sometimes|required|string|max:191',
        ]);

        $product_parameter->update($validated);

        return response()->json($product_parameter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductParameter $product_parameter)
    {
        $product_parameter->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
