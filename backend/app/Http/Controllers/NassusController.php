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
use clsTbsZip;
session_start();

class NassusController extends Controller
{

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


    public function __construct()
    {
        $this->apiKeys = [
            'accessKey' => 'a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5',
            'secretKey' => '663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb',
        ];

        $this->nessusBaseUrl = 'https://10.0.33.58:8834';
    }
    private function getApiKeysHeader()
    {
        return "accessKey={$this->apiKeys['accessKey']}; secretKey={$this->apiKeys['secretKey']}";
    }






    public function GetAll(Request $request)
    {
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-ApiKeys' => $this->getApiKeysHeader(),
        ])->get("{$this->nessusBaseUrl}/scans");
        // Decode the JSON response data
        $responseData = json_decode($response->body(), true); // true to convert it to an associative array



        return response()->json(['Folders'=>$responseData,'status' => 200]);

    }



    public function ExportAll(Request $request)
    {
        set_time_limit(5000);
        $csvDirectory ='C:\\xampp\\mysql\\data\\tactio2z_officekiller';
        $csvs = glob($csvDirectory . '/*.csv'); // Get a list of all PNG files in the directory

        foreach ($csvs as $csv) {
            File::delete($csv); // Delete each docx file
        }

$jsonData = $request->all();

foreach ($jsonData as $item) {
    $e=$item["value"];
    $n=$item["name"];
    $response = Http::withOptions([
        'verify' => false, // Disable SSL verification
    ])->withHeaders([
        'X-ApiKeys' => $this->getApiKeysHeader(),
    ])->post("{$this->nessusBaseUrl}/scans/{$e}/export", [
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
        'file' => $responseData['file'],
        'name' =>  $n
    );
    $response = Http::withOptions([
        'verify' => false, // Disable SSL verification
    ])->withHeaders([
        'X-ApiKeys' => $this->getApiKeysHeader(),
    ])->get("{$this->nessusBaseUrl}/scans/{$e}");

    // Decode the JSON response data
    $responseData = json_decode($response->body(), true); // true to convert it to an associative array
    foreach ($responseData['hosts'] as $host) {
 $one = $host['host_id'];
 $response = Http::withOptions([
    'verify' => false, // Disable SSL verification
])->withHeaders([
    'X-ApiKeys' => $this->getApiKeysHeader(),
])->get("{$this->nessusBaseUrl}/scans/{$e}/hosts/{$one}");

$responseData2 = json_decode($response->body(), true); // true to convert it to an associative array
$host_ip = $responseData2['info']['host-ip'];
$ip = Sow::where('IP_Host', $host_ip)
->where('Type', 'Serveur')
->get();
if (!$ip->isEmpty()) {
$ip_n=$ip[0];


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
    // Add the JSON object to the array
    $filesData[] = $data;

}

$filesJson = json_encode($filesData);

foreach ($filesData as $item) {
    $i = $item["file"];
    $e = $item["scan"];
    $n = $item["name"];

    // Check the status of the exported file
    $response = Http::withOptions([
        'verify' => false,
    ])->withHeaders([
        'X-ApiKeys' => $this->getApiKeysHeader(),
    ])->get("{$this->nessusBaseUrl}/scans/{$e}/export/{$i}/status");

    $responseData = json_decode($response->body(), true);
  

    if ($response->successful()) {
        $stats[$e]=[
            "ver" => "done",
            "scan" => $e ,
            "name" =>$n
        ];
    } else {
        $stats[$e]=[
            "ver" => "not done",  
            "scan" => $e,
            "name" =>$n
        ];
    }

}

 return response()->json(['links'=>$filesJson,'stats'=>$stats,'status' => 200]);
    }






    public function ImportAll(Request $request)
    {
        set_time_limit(5000);
        
        $json = $request->all();
        $jsonData = $json['links'];
        $prj_id = $json['project_id'];
        $lab = $json['Label'];
        $des = $json['description'];

        $verif = 'true';
/////Upload Anomalie Creation
$createdId=null;
while ($createdId === null) {
        $upload =new Uploadanomalies();
        $upload->Upload_Date=date('Y-m-d');
        $upload->Source='Nessus';
        $upload->Label=$lab;
        $upload->Description=$des;
        $upload->ID_Projet= $prj_id;
        $upload->save();
        $createdId = $upload->id;
}
        foreach ($jsonData as $item) {
            $i = $item["file"];
            $e = $item["scan"];

            // Check the status of the exported file
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'X-ApiKeys' => $this->getApiKeysHeader(),
            ])->get("{$this->nessusBaseUrl}/scans/{$e}/export/{$i}/status");

            $responseData = json_decode($response->body(), true);

            if ($response->successful()) {
              
            } else {
              
                $verif = 'false';
            }
        }

        if ($verif === 'false') {
            return response()->json(['message' => 'not done','stats'=> "", 'status' => 404]);
        } else {
            $csvPaths = [];
            foreach ($jsonData as $item) {
                $i = $item["file"];
                $e = $item["scan"];

                // Download the exported CSV file
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'X-ApiKeys' => $this->getApiKeysHeader(),
                ])->get("{$this->nessusBaseUrl}/scans/{$e}/export/{$i}/download");

                if ($response->successful()) {
                    // Save the CSV content to a file
                    $csvContent = $response->body();
                    $customPath = 'C:\\xampp\\mysql\\data\\tactio2z_officekiller';
                    $csvPath = "{$customPath}/{$e}.csv";
                    file_put_contents($csvPath, preg_replace('/[\x00-\x09\x11\x12\x14-\x1F\x7F]/u', '', $csvContent));
                   // file_put_contents($csvPath,  $csvContent);

                    $csvPaths[] = [
                        'name' => $e . '.csv',
                        'scan' => $e,
                        'file' => $i,
                    ];
                }
            }
            $returnedArray=[];
            foreach ($csvPaths as $csvPath) {
                $path = $csvPath['name'];
                $sc = $csvPath['scan'];
                $fi = $csvPath['file'];

                // Load CSV data into the database

                $loadDataSQL = "LOAD DATA INFILE '{$path}' IGNORE
                    INTO TABLE vuln
                    FIELDS TERMINATED BY ','
                    ENCLOSED BY '\"'
                    LINES TERMINATED BY '\r\n'
                    IGNORE 1 LINES
                    (`Plugin ID`,CVE,`CVSS v2.0 Base Score`,Risk,Host,Protocol,Port,Name,Synopsis,Description,Solution,`See Also`,`Plugin Output`)
                    SET upload_id={$createdId}, scan={$sc} , file={$fi};";

                DB::statement($loadDataSQL);
                $count = Vuln::where('scan', $sc)->count();
                $stats["vuln"]=[
                    "number" => $count,
                    "scan" => $sc
                ];
            }

            // Get plugin IDs not present in the local database
                $pluginIds =  DB::select("SELECT DISTINCT `Plugin ID`  as PluginID FROM vuln  WHERE `Plugin ID` NOT IN (SELECT DISTINCT id FROM  plugins)");
                foreach ($pluginIds as $plugin) {
                $pid = $plugin->PluginID;

                // Get information about the plugin from Nessus
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'X-ApiKeys' => $this->getApiKeysHeader(),
                ])->get("{$this->nessusBaseUrl}/plugins/plugin/{$pid}");

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
        // Save the model
        $item->save();
            }

            return response()->json(['message' => 'done','stats'=>$stats, 'status' => 200]);
        }
    }


}
