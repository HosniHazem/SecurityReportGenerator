<?php

namespace App\Http\Controllers;

use App\Models\AuditPreviousAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Project;

class AuditPreviousAuditController extends Controller
{
    public function index()
    {
        $items = AuditPreviousAudit::all();
        return response()->json(['auditPrev' => $items, 'status' => 200]);
    }

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
            'projectID' => 'required|integer|exists:projects,id', // Ensure projectID exists in the projects table
            'ID_Projet' => 'required|integer|exists:glb_projects,ID',
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
            return response()->json(['message' => 'A project with the same number or name already exists!', 'success' => false]);
        }

        $projectID = $request->input('projectID');
        $project = Project::find($projectID);

        if (!$project) {
            return response()->json(['message' => 'Project not found', 'success' => false]);
        }

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
        $auditPreviousAudit->projectID = $projectID; // Associate the project ID
        $auditPreviousAudit->ID_Projet = $request->input('ID_Projet'); // Set ID_Projet

        $auditPreviousAudit->save();

        return response()->json(['success' => true, 'message' => 'Un audit prév a été créé avec succès', 'data' => $auditPreviousAudit,$project]);
    }

    public function show($id)
    {
        $item = AuditPreviousAudit::find($id);
        if ($item) {
            return response()->json(['audit_prev' => $item, 'status' => 200]);
        }
        return response()->json(["message" => 'Not found', 'status' => 404]);
    }

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

            return response()->json(['message' => 'Audit prev is updated', 'data' => $auditPreviousAudit, 'success' => true]);
        } else {
            return response()->json(['message' => 'Record not found', 'status' => 404, 'success' => false]);
        }
    }

    public function destroy($id)
    {
        $auditPreviousAudit = AuditPreviousAudit::find($id);
        if (!$auditPreviousAudit) {
            return response()->json(['message' => 'Record not found', 'status' => 404, 'success' => false]);
        }
        $auditPreviousAudit->delete();
        return response()->json(['message' => 'Record deleted', 'status' => 200, 'success' => true]);
    }
}
