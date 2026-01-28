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
            // Mivel itt nincs Policy, a manuális ellenőrzést bent tartjuk a closure-ben
            if (auth()->user()->role !== 1) {
                // Itt érdemesebb lehet egy kivételt dobni, amit az apiResponse lekezel, 
                // de a te logikádat követve marad a manuális hibaüzenet:
                abort(403, 'Unauthorized. Admin role required.');
            }

            $category = CurrentModel::findOrFail($id);
            $category->delete();
            
            return ['id' => $id, 'message' => 'Deleted successfully'];
        });
    }
}