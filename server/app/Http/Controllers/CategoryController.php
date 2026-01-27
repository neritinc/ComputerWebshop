<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Összes kategória listázása.
     */
    public function index()
    {
        return response()->json(Category::all());
    }

    /**
     * Új kategória mentése.
     * A jogosultságot és validációt a StoreCategoryRequest kezeli.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, 201);
    }

    /**
     * Egy konkrét kategória megjelenítése.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Kategória frissítése.
     * A jogosultságot és validációt az UpdateCategoryRequest kezeli.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return response()->json($category);
    }

    /**
     * Kategória törlése.
     */
    public function destroy(Category $category)
    {
        // Mivel itt nincs külön FormRequest, kézzel ellenőrizzük az admint
        if (auth()->user()->role !== 1) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $category->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}