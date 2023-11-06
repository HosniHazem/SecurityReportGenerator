<?php

namespace App\Http\Controllers;

use App\Models\AuditPreviousAudit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class AuditPreviousAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $item=AuditPreviousAudit::all();
        return response()->json(['auditPrev'=>$item,'status'=>200]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'ProjetNumero' => 'required|integer',
        'Project_name' => 'required|string',
        'ActionNumero' => 'required|integer',
        'Action' => 'required|string|max:600',
        'Criticite' => 'required|string',
        'Chargee_action' => 'required|string',
        'ChargeHJ' => 'required|string',
        'TauxRealisation' => 'required|string',
        'Evaluation' => 'required|string',
        'ID_Projet' => 'required|integer', // Ensure ID_Projet exists in the projects table
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'validate_err' => $validator->getMessageBag(),
        ]);
    }

    $existingAuditPrevious = AuditPreviousAudit::where('ProjetNumero', $request->input('ProjetNumero'))
        ->orWhere('Project_name', $request->input('Project_name'))
        ->first();

    if ($existingAuditPrevious) {
        return response()->json(['message' => 'A project with number or name already exists!', 'success' => false]);
    }

    // Find the associated project by its ID
    $project = Project::find($request->input('ID_Projet'));

    if (!$project) {
        return response()->json(['message' => 'Project not found', 'success' => false]);
    }
    return response()->json($project);
    $auditPreviousAudit = new AuditPreviousAudit();
    $auditPreviousAudit->ProjetNumero = $request->input('ProjetNumero');
    $auditPreviousAudit->Project_name = $request->input('Project_name');
    $auditPreviousAudit->ActionNumero = $request->input('ActionNumero');
    $auditPreviousAudit->Action = $request->input('Action');
    $auditPreviousAudit->Criticite = $request->input('Criticite');
    $auditPreviousAudit->Chargee_action = $request->input('Chargee_action');
    $auditPreviousAudit->ChargeHJ = $request->input('ChargeHJ');
    $auditPreviousAudit->TauxRealisation = $request->input('TauxRealisation');
    $auditPreviousAudit->Evaluation = $request->input('Evaluation');
    $auditPreviousAudit->ID_Projet = $project->id; // Associate the project

    $auditPreviousAudit->save();

    return response()->json(['success' => true, 'message' => 'Un audit prév a été créé avec succès', 'data' => $auditPreviousAudit]);
}



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditPreviousAudit  $auditPreviousAudit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item=AuditPreviousAudit::find($id);
        if($item){
                return response()->json(['audit_prev'=>$item,'status'=>200]);
        }
        return response()->json(["message"=>'not found','status'=>404]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AuditPreviousAudit  $auditPreviousAudit
     * @return \Illuminate\Http\Response
     */
    public function edit(AuditPreviousAudit $auditPreviousAudit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditPreviousAudit  $auditPreviousAudit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ProjetNumero' => 'required|integer',
            'Project_name' => 'required|string',
            'ActionNumero' => 'required|integer',
            'Action' => 'required|string|max:600',
            'Criticite' => 'required|string',
            'Chargee_action' => 'required|string',
            'ChargeHJ' => 'required|string',
            'TauxRealisation' => 'required|string',
            'Evaluation' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        }
    
        $auditPreviousAudit = AuditPreviousAudit::find($id);
    
        if ($auditPreviousAudit) {
            $auditPreviousAudit->ProjetNumero = $request->input('ProjetNumero');
            $auditPreviousAudit->Project_name = $request->input('Project_name');
            $auditPreviousAudit->ActionNumero = $request->input('ActionNumero');
            $auditPreviousAudit->Action = $request->input('Action');
            $auditPreviousAudit->Criticite = $request->input('Criticite');
            $auditPreviousAudit->Chargee_action = $request->input('Chargee_action');
            $auditPreviousAudit->ChargeHJ = $request->input('ChargeHJ');
            $auditPreviousAudit->TauxRealisation = $request->input('TauxRealisation');
            $auditPreviousAudit->Evaluation = $request->input('Evaluation');
            $auditPreviousAudit->update(); // Save the changes to the database
    
            return response()->json(['message' => 'Audit prev is updated', 'data' => $auditPreviousAudit ,'success'=>true]);
        } else {
            return response()->json(['message' => 'Record not found', 'status' => 404,'success'=>false]);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditPreviousAudit  $auditPreviousAudit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auditPreviousAudit=AuditPreviousAudit::find($id);
        if( !$auditPreviousAudit){
            return response()->json(['message' => 'Record not found', 'status' => 404,'success'=>false]);

        }
        $auditPreviousAudit->delete();
        return response()->json(['message' => 'Record deleted', 'status' => 200,'success'=>true]);

    }
}
