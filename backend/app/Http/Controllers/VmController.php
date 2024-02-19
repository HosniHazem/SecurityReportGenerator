<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vm;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class VmController extends Controller
{
    public static function index()
    {
        $resp = [];

        $item = Vm::where('Type', 'Nessus')->get();
        //   print_r($item);
        foreach ($item as $it) {
            try {

                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification
                ])->withHeaders([
                    'X-ApiKeys' => "accessKey=" . $it->accessKey . "; secretKey=" . $it->secretKey,
                ])->timeout(1) // Set a longer timeout for the request (in seconds)
                    ->retry(2, 100) // Retry up to 2 times with a delay of 100 milliseconds between retries
                    ->get("https://" . $it->IP_Host . ":" . $it->Port . "/scans");

                if ($response->successful()) {
                    $resp[$it->IP_Host] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "Name" => $it->Name,
                        "answer" => "Online",
                        "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey,
                        "Type" =>  $it->Type
                    ];
                } else {
                    $resp[$it->IP_Host] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Offline",
                        "Name" => $it->Name,
                        "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey,
                        "Type" =>  $it->Type
                    ];
                }
            } catch (ConnectionException $e) {
                // Handle connection timeout or other connection-related issues
                $resp[$it->IP_Host] = [
                    "ip" => $it->IP_Host . ":" . $it->Port,
                    "answer" => "Offline",
                    "Name" => $it->Name,
                    "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey,
                    "Type" =>  $it->Type
                ];
            }
        }

        return response()->json(['Vm' => $resp, 'status' => 200]);
    }



    public function getAccunetixAndOwaszap()
    {
        $resp = [];
    
        $items = Vm::whereIn('Type', ['Acunetix', 'Owaszap'])
            ->get();
        
        foreach ($items as $it) {
            try {
                $headers = [];
                if (!empty($it->accessKey)) {
                    $headers['X-ApiKeys'] = "accessKey=" . $it->accessKey;
                }
    
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification
                ])->withHeaders($headers)
                    ->timeout(6) // Set a longer timeout for the request (in seconds)
                    ->retry(2, 100) // Retry up to 2 times with a delay of 100 milliseconds between retries
                    ->get("https://" . $it->IP_Host . ":" . $it->Port);
    
                $status = $response->successful() ? 'Online' : 'Offline';
    
                $resp[] = [
                    "ip" => $it->IP_Host . ":" . $it->Port,
                    "answer" => $status,
                    "Type" => $it->Type,
                    "accessKey" => $status === 'Online' ? $it->accessKey : null
                ];
    
            } catch (\Exception $e) {
                // Log the exception if necessary
                // Log::error("Error accessing {$it->IP_Host}:{$it->Port}", ["exception" => $e->getMessage()]);
    
                $resp[] = [
                    "ip" => $it->IP_Host . ":" . $it->Port,
                    "answer" => "Offline",
                    "Type" => $it->Type,
                    "accessKey" => null
                ];
            }
        }
    
        return response()->json(['Vm' => $resp]);
    }
    

}
