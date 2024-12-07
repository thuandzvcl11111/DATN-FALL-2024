<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    // GET /api/get_color
    public function get_color()
    {
        return response()->json(Color::all());
    }

    // GET /api/get_color_detail/{id}
    public function get_color_detail($id)
    {
        $color = Color::find($id);
        return $color ? response()->json($color) : response()->json(['message' => 'Color not found'], 404);
    }

    // POST /api/post_color
    public function post_color(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $color = Color::create($request->only('name'));
        return response()->json($color, 201);
    }

    // PUT /api/put_color/{id}
    public function put_color(Request $request, $id)
    {
        $user = auth()->user();
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->update($request->only('name'));
        return response()->json($color);
    }

    // DELETE /api/delete_color/{id}
    public function delete_color($id)
    {
        $user = auth()->user();
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->delete();
        return response()->json(['message' => 'Color deleted successfully']);
    }
}
