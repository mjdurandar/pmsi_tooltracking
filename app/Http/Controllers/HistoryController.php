<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index() {
        return view('users.history');
    }

    public function show() {
        $user_id = Auth::id();
        $data = History::where('user_id', $user_id)
            ->leftJoin('power_tools', 'power_tools.id', '=', 'histories.power_tools_id')
            ->leftjoin('users', 'users.id', '=', 'histories.user_id')
            ->select('histories.*', 'power_tools.name as power_tools_name', 'users.name as users_name')
            ->get();

        return response()->json([ 'data' => $data]);
    }
}
