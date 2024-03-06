<?php

namespace App\Http\Controllers;

use App\Models\GlbPip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class GlbPipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //done
    public function test()
    {

      
    

        return response()->json(['Customer'=>"slm"]);
        }
    public function index()
    {

        $item =GlbPip::all();

        return response()->json(['GlbPip'=>$item,'status' => 200]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //tested  with sucess 
    public function store(Request $request)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'Nom' => 'required',
        'Titre' => 'required',
        'adresse_mail_primaire' => 'required|email',
        'adresse_mail_secondaire' => 'email',
        'tel' => 'required',
        'customer_id' => 'required|exists:customers,id', // Ensure customer_id exists in the customers table
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => $validator->getMessageBag(),
        ]);
    }

    $existingGlbPip = GlbPip::where('Adresse mail primaire', $request->input('adresse_mail_primaire'))
        ->orWhere('Adresse mail secondaire', $request->input('adresse_mail_primaire'))
        ->first();

    if ($existingGlbPip) {
        return response()->json(['success' => false, 'message' => 'Email address already exists']);
    }

    // Retrieve the associated customer
    $customer = Customer::find($request->input('customer_id'));

    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found']);
    }

    // Create a new GlbPip instance associated with the customer
    $glbPip = new GlbPip();
    $glbPip->Nom = $request->input('Nom');
    $glbPip->Titre = $request->input('Titre');
    $glbPip["Adresse mail primaire"] = $request->input('adresse_mail_primaire');
    $glbPip["Adresse mail secondaire"] = $request->input('adresse_mail_secondaire');
    $glbPip["Tél"] = $request->input('tel');
    $glbPip->customer()->associate($customer); // Associate the GlbPip with the customer
    $glbPip->active = true;

    // Save the GlbPip record to the database
    $glbPip->save();

    // Respond with a success message and the created record
    return response()->json(['success' => true, 'message' => 'glb pip ajouté avec succès', 'data' => $glbPip], 201);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GlbPip  $glbPip
     * @return \Illuminate\Http\Response
     */

     //tested with sucess
    public function show($id)
    {
        $item=GlbPip::find($id);
        if($item){

            return response()->json(['GlbPip'=>$item,'status' => 200]);
            }
        else
        {
        return response()->json(['message'=>'not found','status' => 404]);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GlbPip  $glbPip
     * @return \Illuminate\Http\Response
     */
    public function edit(GlbPip $glbPip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GlbPip  $glbPip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request ->all(),[
            'Nom' => 'required',
            'Titre' => 'required',
            'adresse_mail_primaire' => 'required|email',
            'adresse_mail_secondaire' => 'email',
            'tel' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->getMessageBag(),
                'success'=>false,
                
            ]);
        }
        $existingGlbPip = GlbPip::where('id', '!=', $id)
        ->where(function ($query) use ($request) {
            $query->where('Adresse mail primaire', $request->input('adresse_mail_primaire'))
                ->orWhere('Adresse mail secondaire', $request->input('adresse_mail_primaire'));
        })
        ->first();
    

    if ($existingGlbPip) {
        return response()->json(['message' => 'Email address already exists','success'=>false,
    ]);
    }
        // Find the GlbPip record by its ID
        $glbPip = GlbPip::find($id);
    
        if (!$glbPip) {
            return response()->json(['message' => 'Record not found', 'status' => 404], 404);
        }
    
        // Update the GlbPip model with the new data
        $glbPip->Nom = $request->input('Nom');
        $glbPip->Titre = $request->input('Titre');
        $glbPip["Adresse mail primaire"] = $request->input('adresse_mail_primaire');
        $glbPip["Adresse mail secondaire"] = $request->input('adresse_mail_secondaire');
        $glbPip->Tél = $request->input('tel');
        $glbPip->update();
    
        // Respond with a success message and the updated record
        return response()->json(['success'=>true,'message' => 'un glbpip a été mis a jour', 'data' => $glbPip]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GlbPip  $glbPip
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the GlbPip record by its ID
        $glbPip = GlbPip::find($id);
    
        if (!$glbPip) {
            return response()->json(['success'=>false,'message' => 'Record not found', 'status' => 404]);
        }
    
        // Delete the GlbPip model
        $glbPip->delete();
    
        // Respond with a success message
        return response()->json(['success'=>true,'message' => 'un glb_pip a été supprimé', 'status' => 200]);
    }

    public function getGlbPipByProjectId($customerID){
        $glbPip = GlbPip::where('Cusotmer_ID', $customerID)->get();
        
        return response()->json([
            'success' => 'true', // or 'error' based on your logic
            'data' => $glbPip,
        ]);
    }
}
