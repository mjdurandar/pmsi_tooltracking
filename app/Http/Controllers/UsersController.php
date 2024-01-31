<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BuyTools;
use App\Models\BorrowTools;

class UsersController extends Controller
{
    public function index() {
        return view('admin.users');
    }

    public function show() {
        $data = User::where('role', '!=', 1)->get(); 
        return response()->json([ 'data' => $data ]);
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

    public function showBuyingHistory($id) {

        $data = BuyTools::where('user_id', $id)
            ->leftJoin('power_tools', 'power_tools.id', '=', 'buy_tools.power_tools_id')
            ->leftjoin('users', 'users.id', '=', 'buy_tools.user_id')
            ->select('buy_tools.*', 'power_tools.name as power_tools_name', 'power_tools.price as price' ,'users.name as users_name')
            ->get();

        $dataBorrow = BorrowTools::where('user_id', $id)
            ->leftJoin('scaffoldings', 'scaffoldings.id', '=', 'borrow_tools.scaffoldings_id')
            ->leftjoin('users', 'users.id', '=', 'borrow_tools.user_id')
            ->select('borrow_tools.*', 'scaffoldings.name as scaffoldings_name', 'scaffoldings.price as price' ,'users.name as users_name')
            ->get();

        return response()->json([ 'data' => $data, 'dataBorrow' => $dataBorrow]);
        
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
