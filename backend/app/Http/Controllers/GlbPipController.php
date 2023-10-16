<?php

namespace App\Http\Controllers;

use App\Models\GlbPip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

        return response()->json(['Customer'=>$item,'status' => 200]);
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
    //done 
    public function store(Request $request)
    {
       // Validate the incoming request data
         $validatedData = $request->validate([
        'Nom' => 'required',
        'Titre' => 'required',
        'adresse_mail_primaire' => 'required|email',
        'adresse_mail_secondaire' => 'email',
        'tel' => 'required',
        // Add more validation rules if needed
        ]);

    // Create a new GlbPip instance with the validated data
        $glbPip = new GlbPip();
        $glbPip->Nom = $request->input('Nom');
        $glbPip->Titre = $request->input('Titre');
        $glbPip["Adresse mail primaire"] = $request->input('adresse_mail_primaire');
        $glbPip["Adresse mail secondaire"] = $request->input('adresse_mail_secondaire');
        $glbPip->Tél = $request->input('tel');
        $glbPip->active = false;

    // Save the GlbPip record to the database
        $glbPip->save();

    // Respond with a success message and the created record
        return response()->json(['message' => 'Record created', 'data' => $glbPip], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GlbPip  $glbPip
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item=GlbPip::find($id);
        if($item){

            return response()->json(['Customer'=>$item,'status' => 200]);
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
        $validatedData = $request->validate([
            'Nom' => 'required',
            'Titre' => 'required',
            'adresse_mail_primaire' => 'required|email',
            'adresse_mail_secondaire' => 'email',
            'tel' => 'required',
            // Add more validation rules if needed
        ]);
    
        // Find the GlbPip record by its ID
        $glbPip = GlbPip::find($id);
    
        if (!$glbPip) {
            return response()->json(['message' => 'Record not found', 'status' => 404], 404);
        }
    
        // Update the GlbPip model with the new data
        $glbPip->update($request->all());
    
        // Respond with a success message and the updated record
        return response()->json(['message' => 'Record updated', 'data' => $glbPip], 200);
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
            return response()->json(['message' => 'Record not found', 'status' => 404], 404);
        }
    
        // Delete the GlbPip model
        $glbPip->delete();
    
        // Respond with a success message
        return response()->json(['message' => 'Record deleted', 'status' => 200], 200);
    }
}
