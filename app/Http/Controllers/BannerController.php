<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function get_banner(){

        return response()->json(BannerSlide::all());
    }
    public function post_banner(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'status' => 'required|boolean', // Validate status as a boolean (0 or 1)
    ]);

    $banner = new BannerSlide(); // Replace 'BannerSlide' with the actual model name if it's different

    // Handle image upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('image/banners/'), $filename);
        $banner->image = $filename;
    }

    // Set the status for visibility
    $banner->status = $request->status;

    // Save the banner to the database
    $banner->save();

    // Return a successful JSON response
    return response()->json($banner);
}

}
