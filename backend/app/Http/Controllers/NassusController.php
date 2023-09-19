<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Vuln;
use App\Models\Plugins;
use clsTbsZip;
session_start();

class NassusController extends Controller
{

    public function GetAll(Request $request)
    {
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-ApiKeys' => 'accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
        ])->get('https://10.0.33.58:8834/scans');

        // Decode the JSON response data
        $responseData = json_decode($response->body(), true); // true to convert it to an associative array



        return response()->json(['Folders'=>$responseData,'status' => 200]);

    }



    public function ExportAll(Request $request)
    {
        $csvDirectory ='C:\\xampp\\mysql\\data\\tactio2z_officekiller';
        $csvs = glob($csvDirectory . '/*.csv'); // Get a list of all PNG files in the directory

        foreach ($csvs as $csv) {
            File::delete($csv); // Delete each docx file
        }

$jsonData = $request->all();

foreach ($jsonData as $item) {
    $e=$item["value"];
    $response = Http::withOptions([
        'verify' => false, // Disable SSL verification
    ])->withHeaders([
        'X-ApiKeys' => 'accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
    ])->post("https://10.0.33.58:8834/scans/{$e}/export", [
        'format' => 'csv',
        'template_id' => 'false',
        'reportContents.hostSections.scan_information'=> 'true',
        'reportContents.hostSections.host_information'=> 'true',
        'reportContents.hostSections.scan_information'=> 'true',
        'reportContents.vulnerabilitySections.synopsis'=> 'true',
        'reportContents.vulnerabilitySections.description'=> 'true',
        'reportContents.vulnerabilitySections.see_also'=> 'true',
        'reportContents.vulnerabilitySections.solution'=> 'true',
        'reportContents.vulnerabilitySections.risk_factor'=> 'true',
        'reportContents.vulnerabilitySections.cvss3_base_score'=> 'true',
        'reportContents.vulnerabilitySections.stig_severity'=> 'true',
        'reportContents.vulnerabilitySections.references'=> 'true',
        'reportContents.vulnerabilitySections.exploitable_with'=> 'true',
        'reportContents.vulnerabilitySections.plugin_information'=> 'true',
        'reportContents.vulnerabilitySections.plugin_output'=> 'true',

    ]);
    $responseData = json_decode($response->body(), true);
    $data = array(
        'scan' => $e,
        'file' => $responseData['file']
    );

    // Add the JSON object to the array
    $filesData[] = $data;

}

$filesJson = json_encode($filesData);

 return response()->json(['links'=>$filesJson,'status' => 200]);
    }







    public function ImportAll(Request $request)
    {
        $json = $request->all();

        $jsonData = $json['links'];
        $project_id = $json['project_id'];
        $verif = 'true';

         // Create an array to store the readiness status for each item

        foreach ($jsonData as $item) {

            $i=$item["file"];
            $e=$item["scan"];

            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'X-ApiKeys' => 'accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
            ])->get("https://10.0.33.58:8834/scans/{$e}/export/{$i}/status");

 $responseData = json_decode($response->body(), true);

if($response->successful())
{

    } else {
        $verif = 'false';
    }


        }

 if ($verif === 'false') {
        return response()->json(['message' => 'not done', 'status' => 404]);
    }
else {
    $csvPaths = [];
        foreach ($jsonData as $item) {

            $i=$item["file"];
            $e=$item["scan"];

            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'X-ApiKeys' => 'accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
            ])->get("https://10.0.33.58:8834/scans/{$e}/export/{$i}/download");

            if ($response->successful()) {
                // Save the CSV content to a file in the storage/csv folder
                $csvContent = $response->body();
                $customPath = 'C:\\xampp\\mysql\\data\\tactio2z_officekiller';

            // Build the full path to save the file
            $csvPath = "{$customPath}/{$e}.csv";

            // Save the CSV content to the custom path
            file_put_contents($csvPath, $csvContent);
            $csvPaths[] = [
                'name' => $e . '.csv',
                'scan' => $e,
                'file' => $i,
            ];



            }
        }
         foreach ($csvPaths as $csvPath) {
            $path = $csvPath['name'];
            $sc = $csvPath['scan'];
            $fi = $csvPath['file'];

             $loadDataSQL = "LOAD DATA INFILE '{$path}' IGNORE
             INTO TABLE vuln
             FIELDS TERMINATED BY ','
             ENCLOSED BY '\"'
             LINES TERMINATED BY '\r\n'
             IGNORE 1 LINES
              (`Plugin ID`,CVE,`CVSS v2.0 Base Score`,Risk,Host,Protocol,Port,Name,Synopsis,Description,Solution,`See Also`,`Plugin Output`)
              SET upload_id={$project_id}, scan={$sc} , file={$fi}
              ;";

        // Execute query
        DB::statement($loadDataSQL);
          }


          $pluginIds = DB::table('vuln as v')
          ->select('v.Plugin ID as PluginID')
          ->distinct()
          ->whereNotIn('v.Plugin ID', function($query) {
            $query->select('id')
                  ->from('plugins');
          })
          ->get();
          foreach ($pluginIds as $plugin) {

           $pid =  $plugin->PluginID;

           $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-ApiKeys' => 'accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
        ])->get("https://10.0.33.58:8834/plugins/plugin/{$pid}");


        $responseData = json_decode($response->body(), true);
        $attributes = $responseData['attributes'];

        // making a one json file
        $Finale_data = [];
        $Finale_data["id"] = $responseData['id'];
        $Finale_data["name"] = $responseData['name'];
        $Finale_data["family_name"] = $responseData['family_name'];

        foreach ($attributes as $attribute) {
            $Finale_data[$attribute['attribute_name']] = $attribute['attribute_value'];
        }


        $item = new Plugins();

        // Define a list of attributes to map
        $attributesToMap = [
            'id',
            'fname',
            'name',
            'plugin_name',
            'description',
            'solution',
            'script_version',
            'script_copyright',
            'cvss3_vector',
            'cvss_score_source',
            'cvss_temporal_vector',
            'exploit_framework_core',
            'risk_factor',
            'cvss_temporal_score',
            'plugin_publication_date',
            'metasploit_name',
            'exploited_by_malware',
            'cvss3_base_score',
            'cvss_vector',
            'plugin_type',
            'synopsis',
            'see_also',
            'exploit_available',
            'cvss_base_score',
            'stig_severity',
            'age_of_vuln',
            'cvssV3_impactScore',
            'exploit_code_maturity',
            'family_name',
        ];

        foreach ($attributesToMap as $attribute) {
            if (isset($Finale_data[$attribute])) {
                $item->{$attribute} = $Finale_data[$attribute];
            }
        }
        // Save the model
        $item->save();


        }
        return response()->json(['message'=>'done','status' => 200]);
    }

    }

}
