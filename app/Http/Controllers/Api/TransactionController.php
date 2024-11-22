<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Get user's cart items
            $cartItems = Cart::with('product')
                ->where('user_id', Auth::id())
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate total amount
            $totalAmount = $cartItems->sum(function ($item) {
                return ($item->product->price - $item->product->discount) * $item->quantity;
            });

            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount
            ]);

            // Create transaction items
            foreach ($cartItems as $cartItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity
                ]);

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear user's cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction->load('items.product')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $transactions = Transaction::with('items.product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ]);
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction->load('items.product')
        ]);
    }
}
