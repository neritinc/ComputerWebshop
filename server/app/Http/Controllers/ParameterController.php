<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Http\Requests\StoreParameterRequest;
use App\Http\Requests\UpdateParameterRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ParameterController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parameters = Parameter::all();
        return response()->json($parameters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParameterRequest $request)
    {
        $this->authorize('create', Parameter::class);
        
        $validated = $request->validated();
        $parameter = Parameter::create($validated);
        return response()->json($parameter, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Parameter $parameter)
    {
        return response()->json($parameter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParameterRequest $request, Parameter $parameter)
    {
        $this->authorize('update', $parameter);
        
        $validated = $request->validated();
        $parameter->update($validated);
        return response()->json($parameter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parameter $parameter)
    {
        $this->authorize('delete', $parameter);
        
        $parameter->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
