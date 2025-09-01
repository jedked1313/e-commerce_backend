<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get the logged-in user from token
            $user = $request->user();

            return response()->json([
                "status" => "success",
                "data" => $user->address
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failure', 'message' => 'An unexpected error occurred.'], 500);
        }
    }

    // Add new address
    public function store(Request $request)
    {
        // try {
        $validated = $request->validate([
            'address_name'   => 'nullable|string|max:255',
            'contact_phone'  => 'required|string|max:20',
            'city'           => 'required|string|max:255',
            'neighborhood'   => 'required|string|max:255',
            'street'         => 'nullable|string|max:255',
            'building'       => 'nullable|string|max:255',
            'apartment'      => 'nullable|string|max:255',
            'latitude'       => 'nullable',
            'longitude'      => 'nullable',
            'postal_code'    => 'nullable|integer',
        ]);

        $address = $request->user()->address()->create($validated);

        return response()->json($address, 201);
        // } catch (\Throwable $th) {
        //     return response()->json(['status' => 'failure', 'message' => 'An unexpected error occurred.'], 500);
        // }
    }

    // Update an existing address
    public function update(Request $request, $id)
    {
        try {
            $address = Address::where('user_id', $request->user()->user_id)->findOrFail($id);

            $validated = $request->validate([
                'address_name'   => 'nullable|string|max:255',
                'contact_phone'  => 'required|string|max:20',
                'city'           => 'required|string|max:255',
                'neighborhood'   => 'required|string|max:255',
                'street'         => 'nullable|string|max:255',
                'building'       => 'nullable|string|max:255',
                'apartment'      => 'nullable|string|max:255',
                'latitude'       => 'nullable',
                'longitude'      => 'nullable',
                'postal_code'    => 'nullable|integer',
            ]);

            $address->update($validated);

            return response()->json($address, 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failure', 'message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $address = Address::findOrFail($id);
            $response = ['status' => 'success', 'data' => $address];

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the address is not found
            return response()->json([
                'status' => 'failure',
                'message' => 'Address not found.'
            ], 404);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure', 'message' => 'An unexpected error occurred.'];
            return response()->json($response, 500);
        }
    }

    // Delete address
    public function destroy(Request $request, $id)
    {
        try {
            $address = Address::where('user_id', $request->user()->user_id)->findOrFail($id);
            $address->delete();

            return response()->json(['message' => 'Address deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failure', 'message' => 'An unexpected error occurred.'], 500);
        }
    }
}
