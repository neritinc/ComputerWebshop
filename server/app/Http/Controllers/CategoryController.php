<?php

namespace App\Http\Controllers;

// Aliasok a konzisztens struktúrához
use App\Models\Category as CurrentModel;
use App\Http\Requests\StoreCategoryRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCategoryRequest as UpdateCurrentModelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Összes kategória listázása.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::all();
        });
    }

    /**
     * Új kategória mentése.
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $this->authorize('create', CurrentModel::class);
            return CurrentModel::create($request->validated());
        });
    }

    /**
     * Egy konkrét kategória megjelenítése.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    /**
     * Kategória frissítése.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $category = CurrentModel::findOrFail($id);
            $this->authorize('update', $category);
            $category->update($request->validated());
            return $category;
        });
    }

    /**
     * Kategória törlése.
     */
    public function destroy(int $id)
    {
         return $this->apiResponse(function () use ($id) {
            $category = CurrentModel::findOrFail($id);
            $this->authorize('delete', $category);
            $category->delete();
            return ['id' => $id];
        });
    }
}
