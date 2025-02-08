<?php

namespace App\Http\Controllers;

use App\Models\Favorites;
use App\Models\Items;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $favorites = Favorites::where('user_id', $request->user_id)->get(['user_id', 'item_id']);

            // Get all items with their images in one query
            $itemIds = $favorites->pluck('item_id'); // Get all item_ids from the favorites

            $items = Items::with('images:item_id,image')
                ->whereIn('id', $itemIds)
                ->get(['id', 'name', 'name_ar', 'description_ar', 'description', 'price', 'discount']); // Select only the necessary fields
            $response = ['status' => 'success', 'data' => $items];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($th, 400);
        }
    }

    public function addOrRemove(Request $request)
    {
        try {
            $favorite = Favorites::where('item_id', $request->item_id)->where('user_id', $request->user_id)->first();
            // If the item doesn't added to fevorites
            if (is_null($favorite)) {
                // If the item is not in the favorites, add it
                $favorite = Favorites::create([
                    'user_id' => $request->user_id,
                    'item_id' => $request->item_id,
                ]);
                $response = [
                    'status' => 'success',
                    'data' => 'The item has been successfully added to favorites.'
                ];
            } else {
                // If the item is already added to fevorites
                $favorite->delete();
                $response = [
                    'status' => 'success',
                    'message' => 'The item has been successfully removed from favorites.'
                ];
            }
            return response()->json($response, 200);
        }catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Database error occurred. Please try again.'
            ], 500);
            
        }catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }
}
