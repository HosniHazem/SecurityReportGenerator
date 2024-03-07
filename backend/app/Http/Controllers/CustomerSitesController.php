<?php

namespace App\Http\Controllers;

use App\Models\CustomerSites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerSitesController extends Controller
{
    public function index()
    {
        $customerSites = CustomerSites::all();

        if ($customerSites->isEmpty()) {
            return response()->json(['message' => 'No customer sites found', 'success' => false]);
        }

        return response()->json(['customerSites' => $customerSites, 'success' => true]);
    }

    public function show($id)
    {
        $customerSite = CustomerSites::find($id);

        if (!$customerSite) {
            return response()->json(['message' => 'Customer site not found', 'success' => false]);
        }

        return response()->json(['customerSite' => $customerSite, 'success' => true]);
    }

    public function createCustomerSite(Request $request)
    {
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
        } else {
            $customerSite = new CustomerSites();
            $customerSite->Numero_site = $request->input('Numero_site');
            $customerSite->Structure = $request->input('Structure');
            $customerSite->Lieu = $request->input('Lieu');
            $customerSite->Customer_ID = $request->input('Customer_ID');

            $customerSite->save();

            return response()->json(['message' => 'Customer site created successfully', 'success' => true]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Numero_site' => 'required',
            'Structure' => 'required',
            'Lieu' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {
            $customerSite = CustomerSites::find($id);

            if (!$customerSite) {
                return response()->json(['message' => 'Customer site not found', 'success' => false]);
            }

            $customerSite->Numero_site = $request->input('Numero_site');
            $customerSite->Structure = $request->input('Structure');
            $customerSite->Lieu = $request->input('Lieu');
            // $customerSite->Customer_ID = $request->input('Customer_ID');

            $customerSite->save();

            return response()->json(['message' => 'Customer site updated successfully', 'success' => true]);
        }
    }

    public function destroy($id)
    {
        $customerSite = CustomerSites::find($id);

        if (!$customerSite) {
            return response()->json(['message' => 'Customer site not found', 'success' => false]);
        }

        $customerSite->delete();

        return response()->json(['message' => 'Customer site deleted successfully', 'success' => true]);
    }
    public function getCustomerSiteByCustomerId($id){
        $customerSite=CustomerSites::where('Customer_ID',$id)->get();
        return response()->json([
            'success' => 'true', // or 'error' based on your logic
            'data' => $customerSite,
        ]);
    }
    
}
