<?php

namespace App\Http\Controllers;

use App\Models\AuditPreviousAudit;
use App\Models\CustomerSites;
use App\Models\GlbPip;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RmIteration;
use App\Models\MehariVersion;
use App\Models\Sow;
use App\Models\Vuln;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    public function show($id)
    {

        $item = Project::find($id);
        if ($item) {

            return response()->json(['Project' => $item, 'success' => true]);
        } else {
            return response()->json(['message' => 'not found', 'status' => 404]);
        }
    }
    static function CheckQualityAll($AllProjects)
    {
        $i = 0;
        foreach ($AllProjects as $project) {
            $project->quality = self::checkQuality($project->id);
        }

        return $AllProjects;
    }
    static function checkQuality($project)
    {
        return 0;
    }
    public function index()
    {

        $item = Project::all();
        $item2 = self::CheckQualityAll($item);

        return response()->json(['Project' => $item, 'status' => 200]);
    }
    public function default()
    {
        // Get the latest project record
        $latestProject = Project::latest()->first();

        if ($latestProject) {
            $lastProjectId = $latestProject;
        } else {
            $lastProjectId = null; // Handle the case where no projects exist
        }

        return response()->json(['lastProjectId' => $lastProjectId, 'status' => 200]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'Nom' => 'required',
            'URL' => 'required',
            'Description' => 'required',
            'iterationKey' => 'required',
            'methodVersion' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        }

        $methodVersion = $req->input('methodVersion');

        $existingMethodVersion = MehariVersion::where('Version', $methodVersion)->first();

        if ($existingMethodVersion) {
            // Check if iterationKey already exists
            $existingRmIteration = RmIteration::where('ID', $req->iterationKey)->first();

            if ($existingRmIteration) {
                // Delete the existing RmIteration entry
                $existingRmIteration->delete();
            }

            // Create the project
            $item = new Project();
            $item->Nom = $req->Nom;
            $item->URL = $req->URL;
            $item->Description = $req->Description;
            $item->year = $req->year;
            $item->customer_id = $req->customer_id;
            $item->iterationKey = $req->iterationKey;
            $item->save();

            // Create new RmIteration
            $rmIteration = new RmIteration;
            $rmIteration->ID = $req->iterationKey;
            $rmIteration->MehariVersion = $methodVersion;
            $rmIteration->customerID = $req->customer_id;
            $rmIteration['Date création'] = date("Y-m-d");
            $rmIteration->save();

            return response()->json([
                'message' => 'Project and RmIteration created successfully',
                'project' => $item,
                'rmIteration' => $rmIteration,
                'success' => true,
                'status' => 200
            ]);
        } else {
            // Handle the case where MehariVersion is not found
            return response()->json([
                'message' => 'MehariVersion not found',
                'success' => false,
            ]);
        }
    }


    public function update(Request $req, $id)
    {
        $item = Project::find($id);

        if ($item) {
            $previousIterationKey = $item->iterationKey;
            // print_r("gdim".$previousIterationKey);
            if (isset($req->Nom)) $item->Nom = $req->Nom;
            if (isset($req->URL))  $item->URL = $req->URL;
            if (isset($req->Description)) $item->Description = $req->Description;
            if (isset($req->QualityChecked)) $item->QualityChecked = $req->QualityChecked;
            if (isset($req->QualityCheckedDateTime)) $item->QualityCheckedDateTime = $req->QualityCheckedDateTime;
            if (isset($req->QualityCheckedMessage)) $item->QualityCheckedMessage = $req->QualityCheckedMessage;
            //year now
            if(isset($req->year)) $item->year=$req->year;
            $methodVersion = $req->methodVersion;
            if (isset($req->Preuve)) $item->Preuve = $req->Preuve;
            if (isset($req->iterationKey)) {
                $item->iterationKey = $req->iterationKey;

                // Fetch the existing RmIteration record using SQL
                $rmIteration = DB::table('rm_iteration')->where('ID', $previousIterationKey)->first();

                if ($rmIteration) {
                    // If RmIteration exists, update its fields using SQL
                    DB::table('rm_iteration')
                        ->where('ID', $previousIterationKey)
                        ->update([
                            'ID' => $req->iterationKey,
                            'MehariVersion' => isset($methodVersion) ? $methodVersion : $rmIteration->MehariVersion
                        ]);
                } else {
                    // If RmIteration doesn't exist, create a new one
                    DB::table('rm_iteration')->insert([
                        'ID' => $req->iterationKey,
                        'MehariVersion' => isset($methodVersion) ? $methodVersion : 2,
                        'customerID' => $item->customer_id,
                        'Date création' => date("Y-m-d")
                    ]);
                }
            }
            $item->update();



            return response()->json(['message' => 'done', 'item' => $item, 'status' => 200]);
        } else {
            return response()->json(['message' => 'not done', 'status' => 404]);
        }
    }

    public function updateQuality(Request $req, $id)
    {




        $item = Project::find($id);

        if ($item) {
            $item->QualityChecked = $req->QualityChecked;
            $item->Nom = $item->Nom;
            $item->URL->$item->URL;
            $item->Description->$item->Description;
            $item->QualityCheckedDateTime = $req->QualityCheckedDateTime;
            $item->QualityCheckedMessage = $req->QualityCheckedMessage;
            $item->Preuve = $req->Preuve;
            $item->update();
            return response()->json(['message' => 'done', 'status' => 200]);
        } else {
            return response()->json(['message' => 'not done', 'status' => 404]);
        }
    }
    public function destroy($id)
    {

        $item = Project::find($id);
        if ($item) {
            $item->delete();
            return response()->json(['message' => 'deleted'], 200);
        } else {
            return response()->json(['message' => 'not deleted'], 404);
        }
    }
    public function ProjectDetails()
    {
        $projects = Project::all();
        $response = [];
        $answerCounts = [];
        $indicatorsCount=[];

        foreach ($projects as $project) {
            $glbPipCount = GlbPip::where('Cusotmer_ID', $project->customer_id)->count();
            $sqlAnswers = "SELECT COUNT(rm_answers.ID) AS answer_count FROM projects 
            JOIN rm_iteration ON projects.iterationKey = rm_iteration.ID 
            JOIN rm_answers ON rm_iteration.ID = rm_answers.ID_ITERATION
             WHERE projects.id = ?";
            $result = DB::select($sqlAnswers, [$project->id]);
            $sqlIndicators="SELECT COUNT(sec_indic.id) AS sec_indic_count
            FROM projects
            JOIN rm_iteration ON projects.iterationKey = rm_iteration.ID
            JOIN sec_indic ON rm_iteration.ID = sec_indic.client
            WHERE projects.id = ?";
            $indicatorsResult=DB::select($sqlIndicators,[$project->id]);

            $sqlRmProcessus="SELECT COUNT(rm_processus_domains.id) AS rm_processus_domains
            FROM projects
            JOIN rm_iteration ON projects.iterationKey = rm_iteration.ID
            JOIN rm_processus_domains ON rm_iteration.ID = rm_processus_domains.ID_ITERATION
            WHERE projects.id =?";
            $rmProcessusResult=DB::select($sqlRmProcessus,[$project->id]);
            


            // Extract the answer count from the result and store it in the array
            $answerCount = $result[0]->answer_count;
            $answerCounts[$project->id] = $answerCount;
            $indicatorCount=$indicatorsResult[0]->sec_indic_count;
            $indicatorsCounts[$project->id]=$indicatorCount;
            $rmProcessusCount=$rmProcessusResult[0]->rm_processus_domains;

            $sowCount = Sow::where('Projet', $project->id)->count();
            $sites = CustomerSites::where('Customer_ID', $project->customer_id)->count();
            $auditPrev = AuditPreviousAudit::where('projectID', $project->id)->count();
            $anomalie = Vuln::where('ID_Projet', $project->id)->count();


            $response['Project'][] = [
                'id' => $project->id,
                'Nom' => $project->Nom,
                'URL' => $project->URL,
                'Description' => $project->Description,
                'customer_id' => $project->customer_id,
                'year' => $project->year,
                'QualityChecked' => $project->QualityChecked,
                'QualityCheckedDateTime' => $project->QualityCheckedDateTime,
                'QualityCheckedMessage' => $project->QualityCheckedMessage,
                'Preuve' => $project->Preuve,
                'iterationKey' => $project->iterationKey,
                'glbpip' => $glbPipCount,
                'sow' => $sowCount,
                'customerSites' => $sites,
                'auditPrev' => $auditPrev,
                'anomalie' => $anomalie,
                'answers' => $answerCount,
                'indicators' => $indicatorCount,
                'rm_processus' => $rmProcessusCount,
            ];
        }

        // Return the response array
        return response()->json($response);
    }
}
