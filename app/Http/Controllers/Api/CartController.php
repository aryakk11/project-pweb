<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return new CartResource(true, 'Cart items retrieved successfully', $carts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id']
            ],
            [
                'quantity' => $validated['quantity']
            ]
        );

        return new CartResource(true, 'Product added to cart successfully', $cart->load('product'));
    }

    public function update(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->update($validated);

        return new CartResource(true, 'Cart quantity updated successfully', $cart->load('product'));
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return new CartResource(true, 'Product removed from cart successfully', null);
    }
}
