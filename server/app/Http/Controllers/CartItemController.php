<?php

namespace App\Http\Controllers;

use App\Models\Cart_item;
use App\Http\Requests\StoreCart_itemRequest;
use App\Http\Requests\UpdateCart_itemRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartItems = Cart_item::all();
        return response()->json($cartItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCart_itemRequest $request)
    {
        $this->authorize('create', Cart_item::class);
        
        $validated = $request->validated();
        $cartItem = Cart_item::create($validated);
        return response()->json($cartItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart_item $cart_item)
    {
        $this->authorize('view', $cart_item);
        
        return response()->json($cart_item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCart_itemRequest $request, Cart_item $cart_item)
    {
        $this->authorize('update', $cart_item);
        
        $validated = $request->validated();
        $cart_item->update($validated);
        return response()->json($cart_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart_item $cart_item)
    {
        $this->authorize('delete', $cart_item);
        
        $cart_item->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
