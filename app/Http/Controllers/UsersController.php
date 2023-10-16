<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index() {
        return view('admin.users');
    }

    public function show() {
        $data = User::get();
        return response()->json([ 'data' => $data, ]);        
    }

    public function store(Request $request) {
                
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'password' => 'required',
        ], [
            'name.required' => "The Name field is required",
            'email.required' => "The Email field is required",
            'address.required' => "The Address field is required",
            'password.required' => "The Password field is required",
        ]);

        $data = isset($request->id) ? User::where('id', $request->id)->first() : new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->password = $request->password;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(User $users) {
        return response()->json(['data' => $users]);
    }

    public function destroy(User $users)
    {
        $users->delete();
        return response()->json(['data' => $users]);
    }
}
