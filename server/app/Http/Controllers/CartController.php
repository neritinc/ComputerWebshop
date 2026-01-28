<?php

namespace App\Http\Controllers;

// Aliasing a modelhez és a requestekhez
use App\Models\Cart as CurrentModel;
use App\Http\Requests\StoreCartRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCartRequest as UpdateCurrentModelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            // A with('user:id,name') csak az ID-t és a nevet kéri le a felhasználók táblából
            return CurrentModel::with('user:id,name')->get();
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
            // Itt láncoljuk: a kosárhoz a user-t, és az itemekhez a product-ot is betöltjük
            $cart = CurrentModel::with(['user:id,name', 'items.product'])->findOrFail($id);
            $this->authorize('view', $cart);
            return $cart;
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $cart = CurrentModel::findOrFail($id);
            $this->authorize('update', $cart);
            $cart->update($request->validated());
            return $cart;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $cart = CurrentModel::findOrFail($id);
            $this->authorize('delete', $cart);
            $cart->delete();
            return ['id' => $id, 'message' => 'Deleted successfully'];
        });
    }
}