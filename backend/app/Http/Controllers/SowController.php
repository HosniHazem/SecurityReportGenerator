<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sow;
use Illuminate\Support\Facades\Validator;

class SowController extends Controller
{




    public function show($id)
    {

        $item =Sow::find($id);
        if($item){

        return response()->json(['Sow'=>$item,'status' => 200]);
        }
    else
    {
    return response()->json(['message'=>'not found','status' => 404]);
    }
    }

    public function index()
    {

        $item =Sow::all();

        return response()->json(['Sow'=>$item,'status' => 200]);
    }
    public function default()
    {
        // Get the latest Sow record
        $latestSow = Sow::latest()->first();

        if ($latestSow) {
            $lastSowId = $latestSow;
        } else {
            $lastSowId = null; // Handle the case where no Sows exist
        }

        return response()->json(['lastSowId' => $lastSowId, 'status' => 200]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'SN' => 'required',
            'LN' => 'required',
            'Logo' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {
        $item =new Sow();
        $item->SN=$req->SN;
        $item->LN=$req->LN;
        $item->Logo=$req->Logo;
        $item->save();
        return response()->json(['message'=>'done','status' => 200]);

    }
    }
    public function multiple(Request $req)
    {
        $srv = $req->serveur;
        $apps = $req->apps;
        $rs = $req->rs;
        $pc = $req->pc;
        $project_id = $req->project_id;
     $all = [$srv,$apps,$rs,$pc];
     $fields = ["IP_Host","Type","Nom","field3","field4","field5","URL","dev_by","Number_users"];

     foreach ($all as $it) {
        foreach ($it as $i){
            $item =new Sow();
        foreach ($fields as $field) {

            if (isset($i[$field]))
            {

        $item[$field]=$i[$field];
    }
}
        $item->Projet=$project_id;
        $existingItem = Sow::where('IP_Host', $item->IP_Host)->first();
            if ($existingItem) {
                continue; // Skip the save operation and continue to the next iteration
            }

            $item->save();
    }
}
        return response()->json(['message'=>'done','status' => 200]);

    }


    public function update(Request $req,$id)
    {
        $validator = Validator::make($req->all(), [
            'SN' => 'required',
            'LN' => 'required',
            'Logo' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {

        $item =Sow::find($id);

        if($item){
            $item->SN=$req->SN;
            $item->LN=$req->LN;
            $item->Logo=$req->Logo;
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

        $item =Sow::find($id);
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

