<?php

namespace App\Http\Controllers;

use App\Models\Company as CurrentModel;
use App\Http\Requests\StoreCompanyRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCompanyRequest as UpdateCurrentModelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            // Kommenteld ki ezt a sort a teszthez:
            $this->authorize('viewAny', CurrentModel::class);
            return CurrentModel::all();
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $this->authorize('create', CurrentModel::class);
            return CurrentModel::create($request->validated());
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $row = CurrentModel::findOrFail($id);
            $this->authorize('view', $row);
            return $row;
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $this->authorize('update', $row);
            $row->update($request->validated());
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
            $this->authorize('delete', $row);
            $row->delete();
            return ['id' => $id];
        });
    }
}