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
use App\Models\Sow;
use App\Models\Uploadanomalies;
use App\Models\Plugins;
use App\Models\Vm;
use clsTbsZip;
session_start();

class NassusController2 extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '2560M');
    }
    public static   $toBeCleanAndTranslatedArray = array(
        'name',
        'description',
        'solution',
        'synopsis',
        'see_also'
    );
    public static function PrepareForUploadToDB ($fieldName, $string)
    {



        if(in_array($fieldName , self::$toBeCleanAndTranslatedArray) )
        {
            $pattern1 = "/([[:punct:]]+ *)(\n)+/";
            $pattern2 = "/(\n)+( *-)/";
            $pattern3 = "/(\n)+/";
            $replacement = "{{1}}";

            $string = preg_replace($pattern1, '${1}'." ", $string);
            $string = preg_replace($pattern2, $replacement.'${2}', $string);
            $string = preg_replace($pattern3, $replacement.'${2}', $string);
            $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
        }
        return $string;
    }

    private function cleanCSVFiles($csvDirectory)
    {
        $csvs = glob($csvDirectory . '/*.csv'); // Get a list of all CSV files in the directory

        foreach ($csvs as $csv) {
            if (is_file($csv)) {
                unlink($csv); // Delete each CSV file
            }
        }
    }

    public function getPluginsFromAllServers(Request $request)
    {
        set_time_limit(50000);
        $allvm = VmController::index();
        $onlineVMs = [];
        $data = $allvm->getData();
        foreach ($data->Vm as $vm) {
            if ($vm->answer === 'Online') {
                $onlineVMs[] = $vm;
            }
        }

        $prj = $request->prj_id;


        foreach ($onlineVMs as $vm) {
            $needed = Vm::where('IP_Host', explode(":", $vm->ip))->first();
          $stats =  self::getPlugins($needed, $prj);
          AnnexesController::sendMessage($stats['name']."[Report] has ". $stats['nb_pl']."number of succesfull".$stats['nb_s']."number of problems".$stats['nb_p']);

        }

        return response()->json(['message' => 'done', 'status' => 200]);
    }

public static function getPlugins ($ip,$prj_id)
{
    AnnexesController::sendMessage("[Nessus_Plugins] Used Server: ". $ip);
    $s=0;
    $p=0;
    $Stats = [];
                $ApiKeys = $ip->Auth;
                // Get plugin IDs not present in the local database
                $statment = "SELECT DISTINCT `Plugin ID` AS PluginID FROM vuln WHERE `Plugin ID` NOT IN (SELECT DISTINCT id FROM plugins)";

                if (isset($prj_id)) {
                    $statment .= " AND `ID_Projet` = " . $prj_id;
                }
                $pluginIds =  DB::select($statment);

                $Stats['nb_pl']=count($pluginIds);

                foreach ($pluginIds as $plugin) {
                $pid = $plugin->PluginID;
                AnnexesController::sendMessage("$ip->Name.[Nessus_Plugins]IPD: ". $pid);
                // Get information about the plugin from Nessus
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'X-ApiKeys' => $ApiKeys,
                ])->get("https://{$ip->IP_Host}:{$ip->Port}/plugins/plugin/{$pid}");

                $responseData = json_decode($response->body(), true);
                if(isset($responseData['attributes']))
                {
                    $p++;
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
                    'exploit_framework_metasploit',
                    'exploit_framework_canvas',
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
                $item->save();
            }
            else
            {

                AnnexesController::sendMessage($ip->Name."[Nessus_Plugins_Problem] ". $pid ." Plugin was not found in nessus in the project with id ".$prj_id);
                $p++;
            }
                // Save the model


}
$Stats['nb_s']=$s;
$Stats['nb_p']=$p;
$Stats['name']=$ip->Name;
return $Stats;
}
    private function verifyRequiredFields($jsonData,$fields)
    {
        $verif=true;
        foreach($fields as $field){
            if(!isset($jsonData[$field]))$verif=false;
        }
        return $verif;
    }

    private function exportScan($jsonData)
    {
        $ipp = $jsonData['ip'];
        $ApiKeys = $jsonData['Auth'];
        $e = $jsonData["value"];
        $n = $jsonData["name"];
        $response = null;
        $iterations = 0;
        $maxIterations = 30;

        do {
            $response = Http::withOptions([
                'verify' => false // Disable SSL verification
            ])->withHeaders([
                'X-ApiKeys' => $ApiKeys
            ])->post("https://{$ipp}/scans/{$e}/export", [
                'format' => 'csv',
                'template_id' => 'false',
                'reportContents.hostSections.scan_information' => 'true',
                'reportContents.hostSections.host_information' => 'true',
                'reportContents.vulnerabilitySections.synopsis' => 'true',
                'reportContents.vulnerabilitySections.description' => 'true',
                'reportContents.vulnerabilitySections.see_also' => 'true',
                'reportContents.vulnerabilitySections.solution' => 'true',
                'reportContents.vulnerabilitySections.risk_factor' => 'true',
                'reportContents.vulnerabilitySections.cvss3_base_score' => 'true',
                'reportContents.vulnerabilitySections.stig_severity' => 'true',
                'reportContents.vulnerabilitySections.references' => 'true',
                'reportContents.vulnerabilitySections.exploitable_with' => 'true',
                'reportContents.vulnerabilitySections.plugin_information' => 'true',
                'reportContents.vulnerabilitySections.plugin_output' => 'true'
            ]);

            // Check if the response is successful
            if ($response->successful()) {
                // Handle the successful response
                break;
            }

            // Increment the iteration count
            $iterations++;
            sleep($iterations);
        } while ($iterations < $maxIterations );

        // Check if the maximum iterations are reached without a successful response
        if ($iterations === $maxIterations) {
            AnnexesController::sendMessage(" [Scan Ready to start response] for " . $e);
        }

        return json_decode($response->body(), true);
    }

    private function updateIPHostInformation($jsonData, $responseData)
    {
        
        $ipp = $jsonData['ip'];
        $ApiKeys = $jsonData['Auth'];
        $e = $jsonData['value'];
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-ApiKeys' => $ApiKeys,
        ])->get("https://{$ipp}/scans/{$e}");

        $responseData = json_decode($response->body(), true);

        foreach ($responseData['hosts'] as $host) {
            $one = $host['host_id'];

            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'X-ApiKeys' => $ApiKeys,
            ])->get("https://{$ipp}/scans/{$e}/hosts/{$one}");

            $responseData2 = json_decode($response->body(), true);
            //print_r($one);
            
            $host_ip = $responseData2['info']['host-ip'];
            $ip = Sow::where('IP_Host', $host_ip)
                ->get();
                
            if (!$ip->isEmpty()) {
                
        foreach ($ip as $ip_n) {
            
        // Check if 'host-fqdn' key exists in $responseData2
        if (isset($responseData2['info']['host-fqdn']) && !empty($responseData2['info']['host-fqdn'])) {
            $ip_n->Nom = $responseData2['info']['host-fqdn'];
        } elseif (isset($responseData2['info']['netbios-name']) && !empty($responseData2['info']['netbios-name']) && $ip_n->Nom === null) {
            $ip_n->Nom = $responseData2['info']['netbios-name'];
        }

        // Check if 'operating-system' key exists in $responseData2
        if (isset($responseData2['info']['operating-system'])) {
            $ip_n->field4 = $responseData2['info']['operating-system'];
        }

        $ip_n->update();
        
    }
    }

    }
    }



    private function createUploadAnomalies($lab, $des, $prj_id)
    {
        $createdId = null;

        while ($createdId === null) {
            $upload = new Uploadanomalies();
            $upload->Upload_Date = date('Y-m-d');
            $upload->Source = 'Nessus';
            $upload->Label = $lab;
            $upload->Description = $des;
            $upload->ID_Projet = $prj_id;
            $upload->save();
            $createdId = $upload->id;
        }

        return $createdId;
    }
    private function checkScanStatus($ip, $e, $i, $ApiKeys, $prj_id, $scanName, $maxIterations)
    {
        $iteration = 1;
        $verif = 'false';

        do {
            // Check the status of the exported file
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'X-ApiKeys' => $ApiKeys,
            ])->get("https://{$ip}/scans/{$e}/export/{$i}/status");

            $responseData = json_decode($response->body(), true);

            if ($response->successful() && $responseData["status"]!="loading") {
                $verif = 'true';
                AnnexesController::sendMessage($prj_id . " [Scan Ready to start response] for " . $scanName);
            } else {
                if ($iteration > $maxIterations) {
                    AnnexesController::sendMessage($prj_id . " [Scan Timeout in check status] " . $scanName);
                    $verif = '';
                    break;
                }

                $iteration++;
                sleep($iteration);
                AnnexesController::sendMessage($prj_id . " [Scan still not ready to start] for " . $scanName);
            }
        } while ($verif === 'false');

        return $verif;
    }
    private function downloadAndSaveCSV($ip, $e, $i, $ApiKeys, $customPath,$Label)
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'X-ApiKeys' => $ApiKeys,
        ])->get("https://{$ip}/scans/{$e}/export/{$i}/download");

        if ($response->successful()) {
            $csvContent = $response->body();

            if ($csvContent) {
                $csvPath = "{$customPath}/{$e}.csv";
                file_put_contents($csvPath, $csvContent);

                return [
                    'name' => $e . '.csv',
                    'scan' => $e,
                    'file' => $i,
                    'scan_name'=>$Label,
                ];
            }
        }

        return null;
    }

    private function loadDataToDatabase($csvPaths, $createdId, $prj_id, $scanName)
    {
    $path = $csvPaths[0]['name'];
        $sc = $csvPaths[0]['scan'];
        $fi = $csvPaths[0]['file'];
        $sn = $csvPaths[0]['scan_name'];

        $loadDataSQL = "LOAD DATA INFILE '{$path}' IGNORE
                        INTO TABLE vuln
                        FIELDS TERMINATED BY ','
                        ENCLOSED BY '\"'
                        LINES TERMINATED BY '\r\n'
                        IGNORE 1 LINES
                        (`Plugin ID`, CVE, `CVSS v2.0 Base Score`, Risk, Host, Protocol, Port, Name, Synopsis, Description, Solution, `See Also`, `Plugin Output`)
                        SET upload_id = {$createdId}, scan = {$sc}, file = {$fi}, scan_name = '{$sn}', ID_Projet = {$prj_id};";

        $sqlinjected = false;
        $iteration = 1;

        while (!$sqlinjected && $iteration < 50) {
            AnnexesController::sendMessage($prj_id . " [working in SQL for scan:] " . $scanName . " ID: " . $sc ."   Iteration: ".$iteration  ."\n  ". $loadDataSQL );

            try {
                DB::statement($loadDataSQL);
                $sqlinjected = true;
                AnnexesController::sendMessage($prj_id . " [done for SQL for scan:] " . $scanName . " ID: " . $sc."   Iteration: ".$iteration );
            } catch (Exception $e) {
                AnnexesController::sendMessage($prj_id . " [Fail for SQL for scan:] " .$e."In Scan =". $scanName . " ID: " . $sc ."   Iteration: ".$iteration );
                $iteration++;
                sleep($iterations);
            }
        }
    }

/////////////////////////////////////////////////////// API'S
    public function GetAll(Request $request)
    {
        $jsonData = $request->all();
        $ApiKeys = $request->Auth;
        if($ApiKeys===0) return 0;
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-ApiKeys' => $ApiKeys,
        ])->get("https://{$request->selectedIp}/scans");
        // Decode the JSON response data
        $responseData = json_decode($response->body(), true); // true to convert it to an associative array
        return response()->json(['Folders'=>$responseData,'status' => 200]);
    }



    public function ExportOne(Request $request)
{
    set_time_limit(5000);

    try {
        $csvDirectory = 'C:\\xampp\\mysql\\data\\tactio2z_officekiller';

        // Clean CSV files
        $this->cleanCSVFiles($csvDirectory);

        // Verify required fields
        $jsonData = $request->all();
        $fields = ["value","name","ip","Auth"];
        if ($this->verifyRequiredFields($jsonData,$fields)===false) {
            return response()->json(['error' => 'Required fields are missing', 'status' => 404]);
            AnnexesController::sendMessage("Required fields are missing");
        }

        // Export scan and get response data
        $responseData = $this->exportScan($jsonData);
        if ($responseData) {
            // Update IP host information

            $this->updateIPHostInformation($jsonData, $responseData);

            // Check the status of the exported file


            return response()->json(['links' => $responseData, 'scan'=>$jsonData["value"],'status' => 200]);
        } else {
            return response()->json(['error' => "Error no file generated by nessus", 'status' => 404]);
        }
    } catch (\Exception $e) {
        return response()->json(['error2' => $e->getMessage()]);
    }
}







    public function ImportOne(Request $request)
    {
       // try {
            set_time_limit(50000);

            $json = $request->all();
            $fields = ["project_id","Label","Auth","description","selectedIp","scan","scan_name"];
            if ($this->verifyRequiredFields($json,$fields)===false ||
            !isset($json['links']['file']))
            {
                return response()->json(['error' => 'Required fields are missing', 'status' => 404]);
                AnnexesController::sendMessage("Required fields are missing");
            }
/// Initialisation
            $jsonData = $json['links'];
            $prj_id = $json['project_id'];
            $lab = $json['Label'];
            $des = $json['description'];
            $ip = $json['selectedIp'];
            $ApiKeys = $json['Auth'];;
            $i = $jsonData["file"];
            $e = $json["scan"];
            $sn = $json["scan_name"];
            $verif = 'false';
            $maxIterations = 30;
            $csvPaths = [];

///////////////////
            /////Upload Anomalie Creation
            if($i){
                ///// Create UploadAnomalie
            $createdId = $this->createUploadAnomalies($lab, $des, $prj_id);
                ////
                ///// Check Export file Status
            $verif = $this->checkScanStatus($ip, $e, $i, $ApiKeys, $prj_id, $lab, $maxIterations);
                /////


if ($verif==='true'){
                    // Download the exported CSV file
                    $customPath = 'C:\\xampp\\mysql\\data\\tactio2z_officekiller';
                    $csvPathInfo = $this->downloadAndSaveCSV($ip, $e, $i, $ApiKeys, $customPath,$sn);

                    if ($csvPathInfo) {
                        $csvPaths[0] = $csvPathInfo;
                    }else{
                        AnnexesController::sendMessage($prj_id . " [CSV File error in download] " . $lab . " ID: " . $e);
                    }

                    $this->loadDataToDatabase($csvPaths, $createdId, $prj_id, $lab);

            try {
                $count = Vuln::where('scan', $e)->count();
            } catch (\Exception $e) {
                AnnexesController::sendMessage($prj_id . " [Fail to count vuln for scan:] " . $lab . " ID: " . $e);
                $count = 999999; // Assign a default value in case of failure
            }

            $stats = [
                "number" => $count,
                "scan" => $e
            ];

            AnnexesController::sendMessage($prj_id . " [stats ]  ".$count. " For:".$lab. " ID: ".$e);
                //self::getPlugins ($ip,$prj_id);

            return response()->json(['message' => 'done','stats'=>$stats, 'status' => 200],200);
        }else {
            return response()->json(['error' => 'No csv file body', 'status' => 404]);
            AnnexesController::sendMessage($prj_id ." [No csv file body No file found !!!! ]  ".$lab. " ID: ".$e);
        }

}else {
    return response()->json(['error' => 'Response having problem file is expired', 'status' => 404]);
    AnnexesController::sendMessage($prj_id ." [Response having problem file is expired] ".$lab. " ID: ".$e);
}
    }

}
