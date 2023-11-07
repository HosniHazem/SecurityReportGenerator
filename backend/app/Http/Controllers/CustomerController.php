<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{


   

    public function show($id)
    {

        $item =Customer::find($id);
        if($item){

        return response()->json(['Customer'=>$item,'status' => 200]);
        }
    else
    {
    return response()->json(['message'=>'not found','status' => 404]);
    }
    }

    public function index()
    {

        $item =Customer::all();

        return response()->json(['Customer'=>$item,'status' => 200]);
    }
    public function default()
    {
        // Get the latest Customer record
        $latestCustomer = Customer::latest()->first();
    
        if ($latestCustomer) {
            $lastCustomerId = $latestCustomer;
        } else {
            $lastCustomerId = null; // Handle the case where no Customers exist
        }
    
        return response()->json(['lastCustomerId' => $lastCustomerId, 'status' => 200]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'SN' => 'required',
            'LN' => 'required',
            'Logo' => 'required',
            'Description' => 'required|string',
            'SecteurActivité' => 'required|string',
            'Categorie' => 'required|string',
            'Site Web' => 'required|string',
            'Addresse mail' => 'required|email',
            'Organigramme' => 'required|string',
            'Network_Design' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        }

        $item = new Customer();
        $item->SN = $req->input('SN');
        $item->LN = $req->input('LN');
        $item->Logo = $req->input('Logo');
        $item->Description = $req->input('Description');
        $item->SecteurActivité = $req->input('SecteurActivité');
        $item->Categorie = $req->input('Categorie');
        $item['Site Web'] = $req->input('Site Web');
        $item['Addresse mail']= $req->input('Addresse mail');
        $item->Organigramme = $req->input('Organigramme');
        $item->Network_Design = $req->input('Network_Design');
        
        $item->save();

        return response()->json(['message' => 'Customer created successfully', 'status' => 200]);
    }

    
    public function uploadimage(Request $request)
    {
        $file = $request->file('attach');
        $filename = $request->input('name') . '.' . $file->getClientOriginalExtension();
        $picture = $filename;
    
        // Move image to public/images/uploads folder
        $file->move(public_path('images/uploads'), $filename);
    
        return response()->json(["message" => "Image Uploaded Successfully", 'status' => 200]);
    }

    static function uploadfile(){

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

        $item =Customer::find($id);

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

        $item =Customer::find($id);
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

