<?php

namespace App\Http\Controllers;

use App\Models\CustomerSites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CustomerSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $item =CustomerSites::all();
        if($item){

        return response()->json(['Customer'=>$item,'status' => 200]);
    
    }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCustomerSite(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'Numero_site' => 'required',
            'Structure' => 'required',
            'Lieu' => 'required',
            'Customer_ID' => 'required|integer', 
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        }
        else {
              // // Create a new CustomerSite instance and fill it with the request data
            $customerSite = new CustomerSites();
            $customerSite->Numero_site = $request->input('Numero_site');
            $customerSite->Structure = $request->input('Structure');
            $customerSite->Lieu = $request->input('Lieu');
            $customerSite->Customer_ID = $request->input('Customer_ID');
            
            $customerSite->save();
    
            // Return a response or redirect as needed
            return response()->json(['message' => 'Customer site created successfully' ,'success'=>true]);
        }
        

          
        
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        
          
       

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerSites  $customerSites
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerSites $customerSites)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerSites  $customerSites
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerSites $customerSites)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerSites  $customerSites
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerSites $customerSites)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerSites  $customerSites
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerSites $customerSites)
    {
        //
    }
}
