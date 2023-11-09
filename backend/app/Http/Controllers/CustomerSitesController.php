<?php

namespace App\Http\Controllers;

use App\Models\CustomerSites;
use Illuminate\Http\Request;

class CustomerSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'Cusotmer_ID' => 'required|integer',
            'Numero_site' => 'required|string',
            'Structure' => 'required|string',
            'Lieu' => 'required|string',
            // Add validation rules for other attributes
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        }
        
        
        $customerSite = CustomerSites::create($data);
    
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
