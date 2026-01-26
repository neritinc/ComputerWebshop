<?php

namespace App\Http\Controllers;

use App\Models\ProductParameter;
use App\Http\Requests\StoreProduct_parameterRequest;
use App\Http\Requests\UpdateProduct_parameterRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductParameterController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $params = ProductParameter::all();
        return response()->json($params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProduct_parameterRequest $request)
    {
        $this->authorize('create', ProductParameter::class);
        
        $validated = $request->validated();
        $param = ProductParameter::create($validated);
        return response()->json($param, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductParameter $product_parameter)
    {
        return response()->json($product_parameter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProduct_parameterRequest $request, ProductParameter $product_parameter)
    {
        $this->authorize('update', $product_parameter);
        
        $validated = $request->validated();
        $product_parameter->update($validated);
        return response()->json($product_parameter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductParameter $product_parameter)
    {
        $this->authorize('delete', $product_parameter);
        
        $product_parameter->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
