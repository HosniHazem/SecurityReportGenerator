<?php

namespace App\Http\Controllers;

use App\Models\RmProcessusDomains;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RmIteration;


class RmProcessusDomainsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rmProcessusDomains = RmProcessusDomains::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'ID_ITERATION' => 'required',
            'Processus_domaine' => 'required',
            'Description' => 'required',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $rmProcessusDomain = new RmProcessusDomains();
        $rmProcessusDomain->fill($request->all());

        $rmProcessusDomain->save();

        return response()->json(['message' => 'Record created successfully'], 201);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RmProcessusDomains  $rmProcessusDomains
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    
    {
        $item=RmProcessusDomains::find($id);
        if($item){

            return response()->json(['RmProcessusDomain'=>$item,'status' => 200]);
            }
        else
        {
        return response()->json(['message'=>'not found','status' => 404]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RmProcessusDomains  $rmProcessusDomains
     * @return \Illuminate\Http\Response
     */
    public function edit(RmProcessusDomains $rmProcessusDomains)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RmProcessusDomains  $rmProcessusDomains
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the record by its ID
        $rmProcessusDomain = RmProcessusDomains::find($id);
    
        // Check if the record exists
        if (!$rmProcessusDomain) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'Processus_domaine' => 'string',
            'Description' => 'string',
        ]);
    
        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // If validation passes, proceed to update the record
        $rmProcessusDomain->Processus_domaine = $request->input('Processus_domaine');
        $rmProcessusDomain->Description = $request->input('Description');
    
        // Save the updated record
        $rmProcessusDomain->save();
    
        // Return a success response
        return response()->json(['message' => 'Record updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RmProcessusDomains  $rmProcessusDomains
     * @return \Illuminate\Http\Response
     */

        public function getRmProccessByIterationID($idIteration){
            $rmProcessusDomain=RmProcessusDomains::where('ID_ITERATION',$idIteration)->get();
            if($rmProcessusDomain){
                return response()->json($rmProcessusDomain);
            }
            



        }



    public function destroy($id)

    {
        $rmProcessusDomain=RmProcessusDomains::find($id);

        if(!$rmProcessusDomain){
            return response()->json(['success'=>false,'message' => 'Record not found', 'status' => 404]);

        }

        $rmProcessusDomain->delete();

        return response()->json(['success'=>true,'message' => 'un rm processus a été supprimé', 'status' => 200]);

    }
}
