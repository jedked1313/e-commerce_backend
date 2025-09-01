<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Favorites;
use App\Models\ItemImages;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Show only discounted items (no pagination)
            $discountedItems = Items::with([
                'category:id,name',
                'images:item_id,image',
            ])
                ->where('discount', '>=', 10)
                ->get(); // Use get() instead of paginate()

            $favoriteItemIds = collect(
                Favorites::where("user_id", $request->user_id)
                    ->pluck('item_id')
            )->flip()->all(); // Using a set for efficient lookup

            // Map over items and add `isFavorite` flag
            $discountedItems->transform(function ($item) use ($favoriteItemIds) {
                $item->isFavorite = isset($favoriteItemIds[$item->id]) ? 1 : 0;
                return $item;
            });
            return response()->json(['status' => 'success', 'data' => $discountedItems], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failure',
                'message' => 'An error occurred while fetching data. Please try again later.',
            ], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation and create item
            $fields = $request->validate([
                'category_id'    => 'required|integer|exists:categories,id',
                'name'           => 'required|max:255',
                'name_ar'        => 'required|max:255',
                'description'    => 'nullable',
                'description_ar' => 'nullable',
                'image'          => 'nullable',
                'price'          => 'required|integer',
                'discount'       => 'nullable',
                'quantity'       => 'required|integer',
                'is_active'      => 'required|boolean',
            ]);
            $item = Items::create($fields);

            // Add Images to created item
            $images = ItemImages::create([
                'item_id' => $item->id,
                'image' => $request->images
            ]);
            $item->images = $images->id;
            $item->save();
            $response = ['status' => 'success', 'data' => $item];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $item = Items::with(['images' => function ($query) {
                $query->select('item_id', 'image');
            }])
                ->findOrFail($id, ['id', 'category_id', 'name_ar', 'name', 'description_ar', 'description', 'price', 'discount']);
            $response = ['status' => 'success', 'data' => $item];
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the item is not found
            return response()->json([
                'status' => 'failure',
                'message' => 'Item not found.',
            ], 404);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    public function categoryItems(Categories $id, Request $request)
    {
        // Brings items that belong to specifice category
        try {
            $itemsCategory = Items::with(['images' => function ($query) {
                $query->select('item_id', 'image');
            }])->where('category_id', $id->id)->get();

            $favoriteItems = Favorites::where('user_id', $request->user_id)
                ->pluck('item_id') // Return only item_ids
                ->toArray();

            // Add 'isFavorite' flag to each item by checking if it's in the favorites list
            $itemsCategory->each(function ($item) use ($favoriteItems) {
                $item->isFavorite = in_array($item->id, $favoriteItems) ? 1 : 0;
            });

            return response()->json([
                'status' => 'success',
                'data' => $itemsCategory
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Category not found.',
            ], 404);
        } catch (\Throwable $th) {
            $response = [
                'status' => 'failure',
                'message' => 'An unexpected error occurred.'
            ];
            return response()->json($response, 500);
        }
    }

    // Search for items by english or arabic name
    public function searchItems(Request $request)
    {
        try {
            $searchTerm = $request->input('query');

            $items = Items::with(['images:item_id,image'])
                ->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('name_ar', 'LIKE', '%' . $searchTerm . '%')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $items
            ], 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => 'failure',
                'message' => 'An unexpected error occurred.'
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items $item)
    {
        try {
            $fialds = $request->validate([
                'category_id'    => 'required|integer|exists:categories,id',
                'name'           => 'required|max:255',
                'name_ar'        => 'required|max:255',
                'description'    => 'nullable',
                'description_ar' => 'nullable',
                'image'          => 'nullable|image|max:1024',
                'price'          => 'required|integer',
                'discount'       => 'nullable',
                'quantity'       => 'integer',
                'is_active'      => 'required|tinyint',
            ]);
            $item->update($fialds);
            $response = ['status' => 'success', 'data' => $item];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Items $item)
    {
        try {
            $item = Items::findOrFail($item);
            $item->delete();
            $response = ['status' => 'success', 'message' => 'The category has been successfully deleted.'];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }
}
