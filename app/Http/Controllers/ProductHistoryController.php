<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use Illuminate\Http\Request;
use PDO;

class ProductHistoryController extends Controller
{
    public function index(){
        return view('admin.product-history');
    }

    public function show(){

        $data = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
                            ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                            ->whereNot('admin_histories.status', 'Cancelled') 
                            ->orderBy('admin_histories.created_at', 'desc')
                            ->get()
                            ->map(function ($item) {
                                $item->created_at = $item->created_at->format('Y-m-d H:i:s');
                                return $item;
                            });

        $dataCancelled = AdminHistory::leftJoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
                        ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                        ->where('admin_histories.status', 'Cancelled') // Filter by status "cancel"
                        ->orderBy('admin_histories.created_at', 'desc')
                        ->get()
                        ->map(function ($item) {
                            $item->created_at = $item->created_at->format('Y-m-d H:i:s');
                            return $item;
                        });

        return response()->json([ 'data' => $data, 'dataCancelled' => $dataCancelled]);
    }

    public function releasedSearch(Request $request){
        $releasedData = $request->releasedData;

        $data = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
                            ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                            ->orderBy('admin_histories.created_at', 'desc')
                            ->where(function($query) use ($releasedData) {
                                $query->where('tools_and_equipment.name', 'like', "%$releasedData%")
                                      ->orWhere('tools_and_equipment.product_code', 'like', "%$releasedData%")
                                      ->orWhere('admin_histories.status', 'like', "%$releasedData%")
                                      ->orWhere('admin_histories.created_at', 'like', "%$releasedData%");
                            })
                            ->whereNot('admin_histories.status', 'Cancelled')
                            ->get()
                            ->map(function ($item) {
                                $item->created_at = $item->created_at->format('Y-m-d H:i:s');
                                return $item;
                            });

        return response()->json([ 'data' => $data ]);
    }

    public function cancelledSearch(Request $request){
        $cancelledData = $request->cancelledData;

        $dataCancelled = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
                            ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                            ->orderBy('admin_histories.created_at', 'desc')
                            ->where(function($query) use ($cancelledData) {
                                $query->where('tools_and_equipment.name', 'like', "%$cancelledData%")
                                      ->orWhere('tools_and_equipment.product_code', 'like', "%$cancelledData%")
                                      ->orWhere('admin_histories.status', 'like', "%$cancelledData%")
                                      ->orWhere('admin_histories.created_at', 'like', "%$cancelledData%");
                            })
                            ->whereNot('admin_histories.status', 'Borrowing')
                            ->whereNot('admin_histories.status', 'Selling')
                            ->get()
                            ->map(function ($item) {
                                $item->created_at = $item->created_at->format('Y-m-d H:i:s');
                                return $item;
                            });

        return response()->json([ 'dataCancelled' => $dataCancelled ]);
    }
}
