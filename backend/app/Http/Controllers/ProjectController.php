<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    public function show($id)
    {

        $item =Project::find($id);
        if($item){

        return response()->json(['Project'=>$item,'status' => 200]);
        }
    else
    {
    return response()->json(['message'=>'not found','status' => 404]);
    }
    }
    static function CheckQualityAll($AllProjects)
    {
        $i=0;
        foreach ($AllProjects as $project)
        {
            $project->quality= self::checkQuality($project->id);
        }

     return $AllProjects;
    }
    static function checkQuality($project)
    {
        return 0;
    }
    public function index()
    {

        $item =Project::all();
        $item2 = self::CheckQualityAll($item);

        return response()->json(['Project'=>$item,'status' => 200]);
    }
    public function default()
    {
        // Get the latest project record
        $latestProject = Project::latest()->first();

        if ($latestProject) {
            $lastProjectId = $latestProject;
        } else {
            $lastProjectId = null; // Handle the case where no projects exist
        }

        return response()->json(['lastProjectId' => $lastProjectId, 'status' => 200]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'Nom' => 'required',
            'URL' => 'required',
            'Description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {
        $item =new Project();
        $item->Nom=$req->Nom;
        $item->URL=$req->URL;
        $item->Description=$req->Description;
        $item->year=$req->year;
        $item->customer_id=$req->customer_id;

        $item->save();
        return response()->json(['message'=>'done','status' => 200]);

    }
    }
    public function update(Request $req,$id)
    {
        $validator = Validator::make($req->all(), [
            'Nom' => 'required',
            'URL' => 'required',
            'Description' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {

        $item =Project::find($id);

        if($item){
            $item->Nom=$req->Nom;
            $item->URL=$req->URL;
            $item->Description=$req->Description;
        $item->update();
        return response()->json(['message'=>'done','status' => 200]);
                }


                else
                {
                return response()->json(['message'=>'not done','status' => 404]);
                }
            }
    }
    public function destroy($id)
    {

        $item =Project::find($id);
        if($item){
        $item->delete();
        return response()->json(['message'=>'deleted'], 200);
                }
                else
                {
                return response()->json(['message'=>'not deleted'], 404);
                }
    }
}

