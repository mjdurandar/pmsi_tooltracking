<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BuyTools;
use App\Models\BorrowTools;
use App\Models\Region;
use App\Models\Province;

class UsersController extends Controller
{
    public function index() {
        return view('admin.users');
    }

    public function show() {
        $data = User::where('role', '!=', 1)->get(); 

        return response()->json([ 'data' => $data]);
    }

    public function filterData(Request $request){
        $account_type = $request->account_type;
        
        $data = User::where('accounts', 'like', '%' . $account_type . '%')
                ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request) {
                
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'company_name' => 'required',
            'contact_address' => 'required',
            'location' => 'required',
            'password' => 'required',
        ], [
            'name.required' => "The Name field is required",
            'email.required' => "The Email field is required",
            'company_name.required' => "The Company Name field is required",
            'location.required' => "The Location field is required",
            'contact_address.required' => "The Contact Address field is required",
            'password.required' => "The Password field is required",
        ]);

        $data = isset($request->id) ? User::where('id', $request->id)->first() : new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->company_name = $request->company_name;
        $data->contact_address = $request->contact_address;
        $data->password = $request->password;
        $data->location = $request->location;
        $data->contact_person = $request->contact_person;
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
