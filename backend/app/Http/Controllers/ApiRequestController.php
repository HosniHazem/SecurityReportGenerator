<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Vuln;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApiRequestController extends Controller
{
    public function index(Request $request)
{

        $query = $request->q;
        $projectID=$request->id;

        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders([
            'X-Auth' => '1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a',
        ])->get("https://acu.g6.ssk.lc:3443/api/v1/targets?q={$query}");

       
        $responseData = json_decode($response->body(), true); // true to convert it to an associative array
        // Check if the 'targets' key exists in the response
        if (isset($responseData['targets'][0])) {
            $A = $responseData['targets'][0]['last_scan_id'];
            $B = $responseData['targets'][0]['last_scan_session_id'];

            $response2 = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'X-Auth' => '1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a',
            ])->get("https://acu.g6.ssk.lc:3443/api/v1/scans/{$A}/results/{$B}/vulnerabilities");
            // Decode the JSON response data for the second request

            $responseData2 = json_decode($response2->body(), true); // true to convert it to an associative array




            $responseData3 = [];
            if(isset($responseData2['vulnerabilities'])){
             foreach ($responseData2['vulnerabilities'] as $item) {

                
            $response3 = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'X-Auth' => '1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a',
            ])->get("https://acu.g6.ssk.lc:3443/api/v1/scans/{$A}/results/{$B}/vulnerabilities/{$item['vuln_id']}");


            $responseData3[] = json_decode($response3->body(), true); // true to convert it to an associative array


             }
            foreach ( $responseData3 as $item) {
                $it =new Vuln();
                $it->Name=$item['vt_name'];
                $it->Description=htmlspecialchars($item['description']);

                $it->Host=self::parseBaseUrl(htmlspecialchars($item['affects_url']));

                 $it->Synopsis=htmlspecialchars($item['impact']);
                 $it->CVSSv3BaseScore= $item['cvss3'];
                 // $it->score=$item['cvss_score'];
                $cvssInput = $item['cvss3'];

                $cvssItem = [];

                $it['See Also'] = htmlspecialchars(json_encode($item['references']));
                $it['Plugin Output']=htmlspecialchars($item['request']);

                $cvssScore = $item['cvss_score'];
                $riskLevel = "";

                if ($cvssScore >= 0.1 && $cvssScore <= 3.9) {
                    $riskLevel = "Low";
                } elseif ($cvssScore >= 4 && $cvssScore <= 6.9) {
                    $riskLevel = "Medium";
                } elseif ($cvssScore >= 7 && $cvssScore <= 8.9) {
                    $riskLevel = "High";
                } elseif ($cvssScore >= 9) {
                    $riskLevel = "Critical";
                }
                else {
                    $riskLevel="Information";
                }
                 $it->risk=$riskLevel;  //High,Medium,Low..
                $it->Solution=htmlspecialchars($item['recommendation']);
                $it->ID_Projet=$projectID;
                // $it->user_name=$request->user;
                // return response()->json($it);
                $it->save();

                $results[] = [
                    'Name' => $it->Name,
                    'Description' => $it->Description,
                    'Host' => $it->Host,
                    'Synopsis' => $it->Synopsis,
                    'CVSSv3BaseScore' => $it->CVSSv3BaseScore,
                    'See_Also' => $it['See Also'],
                    'Plugin_Output' => $it->Plugin_Output,
                    'risk' => $it->risk,
                    'Solution' => $it->Solution,
                ];
                

             }
             $resultLength=count($results);
             return response()->json(['message' => 'done', 'status' => 200,'success'=>true ,'data' => $resultLength]);





    }
}
    }
    public function getVulns(){
        $vulns=Vuln::All();
        return response()->json($vulns);
    }

    public function fillWithOWasZap(Request $request){
        set_time_limit(1000000);
        $query = $request->q;
        $projectID=$request->id;

        $id=1;
     
        $test=0;

     do {
        $baseUrl = "http://acu.g6.ssk.lc:8081/JSON/alert/view/alert/?apikey=d31c2oo5sn998vpk0cpouf0i0h&id={$id}";

            
         $response = Http::get($baseUrl)->json();
        
        
              
     
         $Name = $response['alert']['name'] ?? null;
         $Risk = $response['alert']['risk'] ?? null;
         $Description = $response['alert']['description'] ?? null;
         $Solution = $response['alert']['solution'] ?? null;
         $Host = isset($response['alert']['url']) ? self::parseBaseUrl($response['alert']['url']) : null;
         $SeeAlso = json_encode(self::parseBaseUrl($response['alert']['reference'] ?? null)) ?? null;
         if(strpos($Host,$query)!=false){
            $test++;
        
         }   
         $result = DB::table('vuln2')->insertOrIgnore([
            'Name' => $Name,
            'Risk' => $Risk,
            'Description' => $Description,
            'Solution' => $Solution,
            'Host' => $Host,
            'See Also' => $SeeAlso,
            'ID_Projet'=>$projectID,
        ]);
         $id++;

        
     } while (isset($Host));
     

     
     DB::table('vuln2')
         ->whereNotIn('id', function ($query) {
             $query->select(DB::raw('MIN(id)'))
                 ->from('vuln2')
                 ->groupBy('Name', 'Risk', 'Description', 'Solution', 'Host', 'See Also');
         })
         ->delete();
     
     
     
     return response()->json(['message' => 'done', 'status' => 200,'success'=>true ,'data'=>$test]);
     

    }

    public static function parseBaseUrl($url)
    {
        $parsedUrl = parse_url($url);
    
        if ($parsedUrl && isset($parsedUrl['host'])) {
            return $parsedUrl['host'];
        } else {
            // Handle invalid URL or missing host
            // You might want to log an error or set a default value
            return 'unknown';
        }
    }

    public static function writeReferences($references) {
        // Initialize an empty string to store the references
        $output = '';

        // Iterate through each reference
        foreach ($references as $index => $reference) {
            // Check if 'href' key exists in the reference
            if (isset($reference['href'])) {
                // Append the formatted reference to the output string
                $output .= "-href " . ($index + 1) . " " . $reference['href'] . PHP_EOL;
            }
        }

        // Output the final result
        echo $output;
    }

}
