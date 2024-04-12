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
        // $data = User::where('role', '!=', 1)->get(); 
        $data = User::leftjoin('regions', 'users.region_id', '=', 'regions.id')
                    ->leftjoin('provinces', 'users.province_id', '=', 'provinces.id')
                    ->select('users.*','regions.name as region_name', 'provinces.name as province_name')
                    ->get();
                    
        $regions = Region::get();
        $provinces = Province::get();
        
        return response()->json([ 'data' => $data, 'regions' => $regions, 'provinces' => $provinces ]);
    }

    public function store(Request $request) {
                
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'region_id' => 'required',
            'province_id' => 'required',
            'city' => 'required',
            'barangay' => 'required',
            'contact_address' => 'required',
            'house_number' => 'required',
            'password' => 'required',
        ], [
            'name.required' => "The Name field is required",
            'email.required' => "The Email field is required",
            'region_id.required' => "The Region field is required",
            'province_id.required' => "The Province field is required",
            'city.required' => "The City field is required",
            'barangay.required' => "The Barangay field is required",
            'house_number.required' => "The House Number field is required",
            'contact_address.required' => "The Contact Address field is required",
            'password.required' => "The Password field is required",
        ]);

        $data = isset($request->id) ? User::where('id', $request->id)->first() : new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->balance = $request->balance;
        $data->region_id = $request->region_id;
        $data->province_id = $request->province_id;
        $data->city = $request->city;
        $data->barangay = $request->barangay;
        $data->house_number = $request->house_number;
        $data->contact_address = $request->contact_address;
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
