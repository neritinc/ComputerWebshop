<?php

namespace App\Http\Controllers;

use App\Models\ProductParameter as CurrentModel;
use Illuminate\Http\Request;

class ProductParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     * Lekéri az összes product_parameter-t a kapcsolódó termékkel és paraméterrel.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::with(['product', 'parameter'])->get();
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->apiResponse(function () use ($request) {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'parameter_id' => 'required|exists:parameters,id',
                'value' => 'required|string|max:191',
            ]);

            return CurrentModel::create($validated);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::with(['product', 'parameter'])->findOrFail($id);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);

            $validated = $request->validate([
                'product_id' => 'sometimes|required|exists:products,id',
                'parameter_id' => 'sometimes|required|exists:parameters,id',
                'value' => 'sometimes|required|string|max:191',
            ]);

            $row->update($validated);
            return $row;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $row = CurrentModel::findOrFail($id);
            $row->delete();
            return ['id' => $id];
        });
    }
}