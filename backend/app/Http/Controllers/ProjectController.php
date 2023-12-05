<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RmIteration;
use App\Models\MehariVersion;


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
            'Description' => 'required',
            'iterationKey'=>'required',
            'methodVersion'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } 
        $methodVersion = $req->input('methodVersion');

        $existingMethodVersion = MehariVersion::where('Version', $methodVersion)->first();

        if($existingMethodVersion){

        //we create the project firstly
        $item =new Project();
        $item->Nom=$req->Nom;
        $item->URL=$req->URL;
        $item->Description=$req->Description;
        $item->year=$req->year;
        $item->customer_id=$req->customer_id;
        $item->iterationKey=$req->iterationKey;
        $item->save();
        //let's create iteration

        $rmIteration=new RmIteration;
        $rmIteration->ID=$req->iterationKey;
        $rmIteration->MehariVersion=$methodVersion;
        $rmIteration->customerID=$req->customer_id;
        $rmIteration['Date création']=date("Y-m-d");
        $rmIteration->save();

        return response()->json([
            'message' => 'Project and RmIteration created successfully',
            'project' => $item,
            'rmIteration' => $rmIteration,
            'success'=>true,
        ]);



        } else {

            return response()->json([
                'message' => 'MehariVersion not found',
                'success' => false,
            ]);
        }



        

    
    }
    public function update(Request $req,$id)
    {




        $item =Project::find($id);

        if($item){
           if(isset($req->Nom)) $item->Nom=$req->Nom;
           if(isset($req->URL))  $item->URL=$req->URL;
           if(isset($req->Description)) $item->Description=$req->Description;
           if(isset($req->QualityChecked)) $item->QualityChecked=$req->QualityChecked;
           if(isset($req->QualityCheckedDateTime)) $item->QualityCheckedDateTime=$req->QualityCheckedDateTime;
           if(isset($req->QualityCheckedMessage)) $item->QualityCheckedMessage=$req->QualityCheckedMessage;
           if(isset($req->Preuve)) $item->Preuve=$req->Preuve;
        $item->update();
        return response()->json(['message'=>'done','status' => 200]);
                }
                else
                {
                return response()->json(['message'=>'not done','status' => 404]);
                }
            }
    public function updateQuality(Request $req,$id)
    {




        $item =Project::find($id);

        if($item){
            $item->QualityChecked=$req->QualityChecked;
            $item->Nom=$item->Nom;
            $item->URL->$item->URL;
            $item->Description->$item->Description;
            $item->QualityCheckedDateTime=$req->QualityCheckedDateTime;
            $item->QualityCheckedMessage=$req->QualityCheckedMessage;
            $item->Preuve=$req->Preuve;
        $item->update();
        return response()->json(['message'=>'done','status' => 200]);
                }
                else
                {
                return response()->json(['message'=>'not done','status' => 404]);
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

