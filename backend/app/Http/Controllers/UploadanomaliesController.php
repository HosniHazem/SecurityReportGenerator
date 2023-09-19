<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Models\Uploadanomalies;
use App\Models\Project as Projet;

class UploadanomaliesController extends Controller
{
    public function get()
    {
        $item =Projet::all();



        return response()->json(['Project'=>$item,'status' => 200]);
    }
    public function index()
    {

        $item =Uploadanomalies::all();



        return response()->json(['Uploadanomalies'=>$item,'status' => 200]);
    }
    public function store(Request $req)
    {



        $item =new Uploadanomalies();
        $item->User=$req->User;
        $item->Upload_Date=date('Y-m-d');;
        $item->Source=$req->Source;
        $item->Label=$req->Label;
        $item->Description=$req->Description;
        $item->ID_Projet=$req->ID_Projet;


        $item->save();

        $createdId = $item->id;
        return response()->json(['createdId'=>$createdId,'message'=>'done','status' => 200]);


    }



}
