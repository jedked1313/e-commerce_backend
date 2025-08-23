<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class CategoriesController extends Controller
{
    public function index()
    {
        try {
            $categories = Categories::select('id', 'name_ar', 'name', 'image')->get();
            $response = ['status' => 'success', 'data' => $categories];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'name_ar'        => 'required|max:255',
                'name'           => 'required|max:255',
                'description_ar' => 'nullable',
                'description'    => 'nullable',
                'image'          => 'nullable|image|max:1024',
            ], [
                'name_ar.required' => 'The Arabic name is required.',
                'name.required'    => 'The name is required.',
                'name_ar.max'      => 'The Arabic name cannot be more than 255 characters.',
                'name.max'         => 'The name cannot be more than 255 characters.',
            ]);

            $data = Categories::create($fields);
            $response = ['status' => 'success', 'data' => $data];
            return response()->json($response, 201);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Categories::findOrFail($id);
            $response = ['status' => 'success', 'data' => $category];
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the category is not found
            return response()->json([
                'status' => 'failure',
                'message' => 'Category not found.'
            ], 404);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, Categories $category)
    {
        try {
            $fields = $request->validate([
                'name_ar'        => 'required|max:255',
                'name'           => 'required|max:255',
                'description_ar' => 'nullable',
                'description'    => 'nullable',
                'image'          => 'nullable|image|max:1024',
            ]);

            // Update the category with the validated data
            $category->update($fields);

            // Reload the category to reflect the changes after update
            $category->refresh();
            $response = ['status' => 'success', 'data' => $category];
            return response()->json($response, 200);
        } catch (QueryException $e) {
            // Handle query errors (e.g., database issues)
            return response()->json([
                'status' => 'failure',
                'message' => 'Database error occurred while updating the category.'
            ], 500);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category  = Categories::findOrFail($id);
            $category->delete();
            $response = ['status' => 'success', 'message' => 'The category has been deleted'];
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            $response = ['status' => 'failure', 'message' => 'Category not found.'];
            return response()->json($response, 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }
}
