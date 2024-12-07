<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    // GET /api/get_size
    public function get_size()
    {
        return response()->json(Size::all());
    }

    // GET /api/get_size_detail/{id}
    public function get_size_detail($id)
    {
        $size = Size::find($id);
        return $size ? response()->json($size) : response()->json(['message' => 'Size not found'], 404);
    }

    // POST /api/post_size
    public function post_size(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $size = Size::create($request->only('name'));
        return response()->json($size, 201);
    }

    // PUT /api/edit_size/{id}
    public function edit_size(Request $request, $id)
    {
        $user = auth()->user();
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        $size->update($request->only('name'));
        return response()->json($size);
    }

    // DELETE /api/delete_size/{id}
    public function delete_size($id)
    {
        $user = auth()->user();
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        $size->delete();
        return response()->json(['message' => 'Size deleted successfully']);
    }
}
