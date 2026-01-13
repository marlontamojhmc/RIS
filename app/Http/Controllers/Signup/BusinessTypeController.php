<?php

namespace App\Http\Controllers\Signup;

use App\Http\Controllers\Controller;
use App\Models\Signup\BusinessType;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the business types.
     */
    public function index()
    {
        $types = BusinessType::select('id', 'name', 'description')->get();
        return response()->json($types);
    }

    /**
     * Store a newly created business type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $businessType = BusinessType::create($validated);

        return response()->json([
            'message' => 'Business type created successfully!',
            'data' => $businessType,
        ], 201);
    }

    /**
     * Display the specified business type.
     */
    public function show($id)
    {
        $businessType = BusinessType::findOrFail($id);
        return response()->json($businessType);
    }

    /**
     * Update the specified business type in storage.
     */
    public function update(Request $request, $id)
    {
        $businessType = BusinessType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $businessType->update($validated);

        return response()->json([
            'message' => 'Business type updated successfully!',
            'data' => $businessType,
        ]);
    }

    /**
     * Remove the specified business type from storage.
     */
    public function destroy($id)
    {
        $businessType = BusinessType::findOrFail($id);
        $businessType->delete();

        return response()->json([
            'message' => 'Business type deleted successfully!'
        ]);
    }
    public function categories($id)
    {
        $type = BusinessType::with('ceoc')->find($id);

        if (!$type) {
            return response()->json(['message' => 'Business type not found'], 404);
        }

        // Return CEOC entries as categories
        return response()->json($type->ceoc);
    }
}
