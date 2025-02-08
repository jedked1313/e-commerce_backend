<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Categories;
use App\Models\Favorites;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Display only 6 categories and 6 discounted items
            $categories = Categories::select('id', 'name', 'name_ar', 'image')->take(6)->get();

            // Get items and related tables
            $discountedItems = Items::with([
                'category:id,name',
                'images:item_id,image',
            ])
                ->where('discount', '>', 10)
                ->select([
                    'id',
                    'category_id',
                    'name',
                    'name_ar',
                    'description',
                    'description_ar',
                    'price',
                    'discount'
                ])
                ->take(6)
                ->get();

            // Get user favorite items (only item_ids)
            $favoriteItemIds = Favorites::where("user_id", $request->user_id)
                ->pluck('item_id')
                ->toArray();

            // Check if item is added to favorite
            $discountedItems->each(function ($item) use ($favoriteItemIds) {
                $item['isFavorite'] = in_array($item->id, $favoriteItemIds) ? 1 : 0;
            });
            $response = [
                'status' => 'success',
                'data' => [
                    'categories' => $categories,
                    'items' => $discountedItems,
                ]
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => 'failure',
                'error' => 'An error occurred while fetching data. Please try again later.',
            ];

            return response()->json($response, 500);
        }
    }
}
