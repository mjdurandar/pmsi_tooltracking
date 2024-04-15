<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    public function index()
    {
        return view('supplier.profile');
    }

    public function show(Request $request){
        
        $data = Auth::user();

        return response()->json([ 'data' => $data, ]);
    }
    public function store(Request $request)
    {
        // Retrieve the authenticated user
        $user = auth()->user();
        
        // Validate the request data (you can customize validation rules as needed)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'contact_address' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
        ]);
    
        // Update the user's profile data
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->contact_address = $request->input('contact_address');
        $user->contact_person = $request->input('contact_person');
        $user->location = $request->input('location');
        $user->company_description = $request->input('company_description');
        $user->save();
        
        // Optionally, you can return a response or redirect back with a success message
        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
    

}
