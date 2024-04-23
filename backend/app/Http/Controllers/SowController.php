<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SowController extends Controller
{




    public function show($id)
    {

        $item = Sow::find($id);
        if ($item) {

            return response()->json(['Sow' => $item, 'status' => 200]);
        } else {
            return response()->json(['message' => 'not found', 'status' => 404]);
        }
    }

    public function index()
    {

        $item = Sow::all();

        return response()->json(['Sow' => $item, 'status' => 200]);
    }
    public function default()
    {
        // Get the latest Sow record
        $latestSow = Sow::latest()->first();

        if ($latestSow) {
            $lastSowId = $latestSow;
        } else {
            $lastSowId = null; // Handle the case where no Sows exist
        }

        return response()->json(['lastSowId' => $lastSowId, 'status' => 200]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'SN' => 'required',
            'LN' => 'required',
            'Logo' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'validate_err' => $validator->getMessageBag(),
            ]);
        } else {
            $item = new Sow();
            $item->SN = $req->SN;
            $item->LN = $req->LN;
            $item->Logo = $req->Logo;
            $item->save();
            return response()->json(['message' => 'done', 'status' => 200]);
        }
    }
    public function multiple(Request $req)
    {
        $srv = $req->serveur;
        $apps = $req->apps;
        $rs = $req->rs;
        $pc = $req->pc;
        $project_id = $req->project_id;
        $all = [$srv, $apps, $rs, $pc];
        $fields = ["Projet", "IP_Host", "Type", "Nom", "field3", "field4", "field5", "dev_by", "Number_users"];
        $sql = "INSERT IGNORE INTO sow (`" . implode("`,`", $fields) . "`) VALUES ";
        $values = [];

        foreach ($all as $it) {
            foreach ($it as $i) {
                $itemValues = [];
                foreach ($fields as $field) {
                    if (isset($i[$field])) {

                        $itemValues[] = isset($i[$field]) ? $i[$field] : null;
                    } else if ($field === "Projet") {
                        $itemValues[] = $project_id;
                    } else {
                        $itemValues[] = null;
                    }
                }
                $values[] = "('" . implode("','", $itemValues) . "')";
            }
        }

        $sql .= implode(",", $values);

        DB::statement($sql);

        return response()->json(['message' => 'done', 'status' => 200]);
    }

    // public function update(Request $req,$id)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'SN' => 'required',
    //         'LN' => 'required',
    //         'Logo' => 'required'
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'validate_err' => $validator->getMessageBag(),
    //         ]);
    //     } else {

    //     $item =Sow::find($id);

    //     if($item){
    //         $item->SN=$req->SN;
    //         $item->LN=$req->LN;
    //         $item->Logo=$req->Logo;
    //     $item->update();
    //     return response()->json(['message'=>'done','status' => 200]);
    //             }


    //             else
    //             {
    //             return response()->json(['message'=>'not done','status' => 404]);
    //             }
    //         }
    // }

    public function update(Request $request, $id)
    {
        $sow = Sow::find($id);

        if (!$sow) {
            return response()->json(['message' => 'Sow not found'], 404);
        }

        // Update only the fields that are provided in the request
        $sow->fill($request->all())->save();

        return response()->json(['message' => 'Sow updated successfully', 'sow' => $sow]);
    }
    public function destroy($id)
    {
        try {
            $item = Sow::findOrFail($id); // Use findOrFail to automatically throw an exception if the item is not found
            $item->delete();
            return response()->json(['message' => 'deleted', 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false]);
        }
    }

    public function fillTable(Request $request, $projectId)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt'
            ]);

            // Retrieve the uploaded CSV file
            $csvFile = $request->file('csv_file');
            $filePath = $csvFile->getRealPath();

            // Read the contents of the CSV file
            $fileContents = file_get_contents($filePath);

            // Insert the project ID as the first column in each row
            $modifiedContents = preg_replace('/^(.*)$/m', $projectId . ',$1', $fileContents);

            // Save the modified CSV file
            $modifiedFilePath = storage_path('app/csv_files/sow_modified.csv');
            file_put_contents($modifiedFilePath, $modifiedContents);

            // Use the modified CSV file in the LOAD DATA INFILE query
            $loadData = "LOAD DATA INFILE '" . str_replace('/', '\\\\', str_replace('\\', '\\\\', storage_path('app/csv_files/sow_modified.csv'))) . "' IGNORE
                INTO TABLE sow
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                (`Projet`, `Type`, `Nom`,`IP_Host`, `field3`, `field4`, `field5`, `dev_by`, `URL`, `Number_users`)
            ";

            DB::statement($loadData);

            return response()->json(['message' => 'CSV file stored with success', 'success' => true]);
        } catch (\Throwable $th) {
            // Handle exceptions here, e.g., log the error
            return response()->json(['message' => $th->getMessage(), 'success' => false]);
        }
    }

    public function getSowByProjectId($projectId)
    {
        try {
            $sow = Sow::where('Projet', $projectId)->get();
            if ($sow) {

                return response()->json($sow);
            } else {
                return response()->json(['msg' => 'sow not found']);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false]);
        }
    }
}
