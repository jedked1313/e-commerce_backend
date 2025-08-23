<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cartItems = Cart::with(['items.singleImage'])
            ->where('user_id', $request->user_id)
            ->get(['user_id', 'item_id', 'quantity']);

        // Check if the cart is empty
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'No items found in the cart.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cartItems
        ], 200);
    }

    // Increase the quantity of an item in the cart
    public function increase(Request $request)
    {
        $cart_item = Cart::where('item_id', $request->item_id)->where('user_id', $request->user_id)->first();
        if ($cart_item) {
            $cart_item->quantity += 1; // Increase the quantity by 1
            $cart_item->save();
            $response = [
                'status' => 'success',
                'message' => 'The item has been successfully increased in the cart.'
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 'failure',
                'message' => 'Item not found in the cart.'
            ];
            return response()->json($response, 404);
        }
    }

    // Decrease the quantity of an item in the cart
    public function decrease(Request $request)
    {
        $cart_item = Cart::where('item_id', $request->item_id)->where('user_id', $request->user_id)->first();
        if ($cart_item) {
            if ($cart_item->quantity > 1) {
                $cart_item->quantity -= 1; // Decrease the quantity by 1
                $cart_item->save();
                $response = [
                    'status' => 'success',
                    'message' => 'The item has been successfully decreased in the cart.',
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => 'failure',
                    'message' => 'Cannot decrease quantity below 1.'
                ];
                return response()->json($response, 400);
            }
        } else {
            $response = [
                'status' => 'failure',
                'message' => 'Item not found in the cart.'
            ];
            return response()->json($response, 404);
        }
    }

    // Add a new item to the cart
    public function addToCart(Request $request)
    {
        try {
            $cart_item = Cart::where('item_id', $request->item_id)->where('user_id', $request->user_id)->first();
            if ($cart_item) {
                $response = [
                    'status' => 'failure',
                    'message' => 'Item already exists in the cart.'
                ];
                return response()->json($response, 400);
            }

            $new_cart_item = new Cart();
            $new_cart_item->user_id = $request->user_id;
            $new_cart_item->item_id = $request->item_id;
            $new_cart_item->quantity = 1; // Default quantity is 1
            $new_cart_item->save();

            $response = [
                'status' => 'success',
                'message' => 'The item has been successfully added to the cart.',
                'data' => $new_cart_item
            ];
            return response()->json($response, 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'item_id' => 'required|integer',
        ]);

        try {
            $cartItem = Cart::where('user_id', $request->user_id)
                ->where('item_id', $request->item_id)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item successfully removed from the cart.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }
}
