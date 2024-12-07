<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Thêm mới contact
    public function add_contact(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Contact::create([
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Contact saved successfully']);
    }

    // Xem tất cả contacts
    public function show_contact()
    {
        return Contact::all();
    }

    // Xem chi tiết 1 contact
    public function show_contact_id($id)
    {
        return Contact::findOrFail($id);
    }

    // Xóa contact
    public function delete_concact($id)
    {
        $user = auth()->user();
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
