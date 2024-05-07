<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddBalance;
use App\Models\User;

class AddBalanceController extends Controller
{
    public function index() {
        return view('admin.add-balance');
    }

    public function show() {
        $user = User::find(auth()->user()->id);
        $data = AddBalance::where('user_id', $user->id)
                            ->leftjoin('users', 'users.id', 'add_balances.user_id')
                            ->select('add_balances.*', 'users.balance as balance')
                            ->get();

        $userBalance = User::where('id', $user->id)->first();
        
        return response()->json([ 'data' => $data,'userBalance' => $userBalance]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'balance' => 'required',
            'card_number' => 'required',
        ], [
            'balance.required' => 'The Balance field is required',
            'card_number.required' => 'The Card Number field is required',
        ]);
    
        // Retrieve user's balance
        $user = User::find(auth()->user()->id);
        $balance = $user->balance;
        $balance += $request->balance;
        $user->balance = $balance;

        $user->save();
    
        // Save transaction information
        $addbalance = new AddBalance();
        $addbalance->user_id = $user->id;
        $addbalance->card_number = $request->card_number;
        $addbalance->total_balance = $request->balance;
        $addbalance->card_type = $request->card_type; // Use payment method from request
        $addbalance->description = 'You Cash In ₱' . $request->balance . ' using your ' . $request->card_type . ' account';
        $addbalance->save();
    
        return response()->json(['message' => 'Success'], 200);
    }
}
