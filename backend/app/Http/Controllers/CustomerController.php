<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Project;

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
        'Logo' => 'required|image|mimes:jpeg,png,jpg,gif',
        'Organigramme' => 'required|image|mimes:jpeg,png,jpg,gif',
        'Network_Design' => 'required|image|mimes:jpeg,png,jpg,gif',
        'Description' => 'required|string',
        'SecteurActivité' => 'required|string',
        'Categorie' => 'required|string',
        'Site_Web' => 'required|string',
        'Addresse_mail' => 'required|email',
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
    $item->Description = $req->input('Description');
    $item->SecteurActivité = $req->input('SecteurActivité');
    $item->Categorie = $req->input('Categorie');
    $item['Site Web'] = $req->input('Site_Web');
    $item['Addresse mail' ]= $req->input('Addresse_mail');

    // Handle Logo file upload
    if ($req->hasFile('Logo')) {
        $logoFile = $req->file('Logo');
        $logoFileName = $item->SN . '_Logo.' . $logoFile->getClientOriginalExtension();
        $logoFile->move(public_path('images/uploads'), $logoFileName);
        $item->Logo = $logoFileName;
    }

    // Handle Organigramme file upload
    if ($req->hasFile('Organigramme')) {
        $orgFile = $req->file('Organigramme');
        $orgFileName = $item->SN . '_Organigramme.' . $orgFile->getClientOriginalExtension();
        $orgFile->move(public_path('images/uploads'), $orgFileName);
        $item->Organigramme = $orgFileName;
    }

    // Handle Network Design file upload
    if ($req->hasFile('Network_Design')) {
        $networkFile = $req->file('Network_Design');
        $networkFileName = $item->SN . '_NetworkDesign.' . $networkFile->getClientOriginalExtension();
        $networkFile->move(public_path('images/uploads'), $networkFileName);
        $item->Network_Design = $networkFileName;
    }

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


    public function update(Request $req, $customerId)
{
    $validator = Validator::make($req->all(), [
        'SN'=>'string',
        'Logo' => 'image|mimes:jpeg,png,jpg,gif',
        'Organigramme' => 'image|mimes:jpeg,png,jpg,gif',
        'Network_Design' => 'image|mimes:jpeg,png,jpg,gif',
       
        'Addresse_mail' => 'email',
    ]);
    // print_r("wa");exit;
    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'validate_err' => $validator->getMessageBag(),
        ]);
    }
// return $req->all();
    $customer = Customer::find($customerId);

    if (!$customer) {
        return response()->json([
            'status' => 404,
            'message' => 'Customer not found',
        ]);
    }
// return $customer;
    // Update fields if they are present in the request
    $fillableFields = ['SN', 'LN', 'Logo', 'Organigramme', 'Description', 'SecteurActivité', 'Categorie', 'Site_Web', 'Addresse_mail'];
    print_r($req->SN);
    foreach ($fillableFields as $field) {
        if (isset($req->$field)) {
            $customer->$field = $req->input($field);
            

        }
    }
    $customer->update();
    // Handle file uploads if they are present in the request
    $fileFields = ['Logo', 'Organigramme', 'Network_Design'];
    foreach ($fileFields as $fileField) {
        if ($req->hasFile($fileField)) {
            $file = $req->file($fileField);
            $fileName = $customer->SN . '_' . $fileField . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/uploads'), $fileName);
            $customer->{$fileField} = $fileName;
        }
    }

    $customer->update();

    return response()->json(['message' => 'Customer updated successfully', 'status' => 200 ,'Customer'=>$customer]);
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
    public function getProjectByCustomerId($projectId){
        $item =Customer::find($projectId);

        return response()->json(['Customer'=>$item,'status' => 200]);
    }
}

