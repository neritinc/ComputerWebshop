<?php

namespace App\Http\Controllers;

// Aliasok beállítása a könnyebb másolhatóság érdekében
use App\Models\Cart_item as CurrentModel;
use App\Http\Requests\StoreCart_itemRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCart_itemRequest as UpdateCurrentModelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            $user = request()->user();
            $query = CurrentModel::with(['product', 'cart']);

            if ((int) $user->role !== 1) {
                $query->whereHas('cart', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            return $query->get();
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
            $cartItem = CurrentModel::with('product')->findOrFail($id);
            $this->authorize('view', $cartItem);
            return $cartItem;
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $cartItem = CurrentModel::findOrFail($id);
            $this->authorize('update', $cartItem);
            $cartItem->update($request->validated());
            return $cartItem;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $cartItem = CurrentModel::findOrFail($id);
            $this->authorize('delete', $cartItem);
            $cartItem->delete();
            return ['id' => $id, 'message' => 'Deleted successfully'];
        });
    }
}
