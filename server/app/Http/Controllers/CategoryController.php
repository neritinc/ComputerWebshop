<?php

namespace App\Http\Controllers;

// Aliasok a konzisztens struktúrához
use App\Models\Category as CurrentModel;
use App\Http\Requests\StoreCategoryRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCategoryRequest as UpdateCurrentModelRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
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
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];
        });
    }
}