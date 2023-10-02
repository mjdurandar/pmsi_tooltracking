<?php

namespace App\Http\Controllers;

use App\Models\ProjectSite;
use Illuminate\Http\Request;

class ProjectSiteController extends Controller
{
    public function index(){
        return view('admin.project-site');
    }
    
    public function show(){
        $data = ProjectSite::get();
        return response()->json([ 'data' => $data, ]);
    }

    public function store(Request $request)
    {       
        
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => "The Name field is required",
        ]);

        $data = isset($request->id) ? ProjectSite::where('id', $request->id)->first() : new ProjectSite();
        $data->name = $request->name;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(ProjectSite $projectsite)
    {
        return response()->json(['data' => $projectsite]);
    }

    public function destroy(ProjectSite $projectsite)
    {
        $projectsite->delete();
        return response()->json(['data' => $projectsite]);
    }
    
}
