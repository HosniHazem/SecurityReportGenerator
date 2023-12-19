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

        $item = Vm::where('Type', '!=', 'Acunetix')->get();
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
                        "answer" => "Online",
                        "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey
                    ];
                } else {
                    $resp[$it->IP_Host] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Offline",
                        "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey
                    ];
                }
            } catch (ConnectionException $e) {
                // Handle connection timeout or other connection-related issues
                $resp[$it->IP_Host] = [
                    "ip" => $it->IP_Host . ":" . $it->Port,
                    "answer" => "Offline",
                    "Auth" => "accessKey=" . $it->accessKey . ";secretKey=" . $it->secretKey
                ];
            }
        }

        return response()->json(['Vm' => $resp, 'status' => 200]);
    }


    public function getVulnWithType()
{
    $resp = [];

    $response = null;
    $item = Vm::where('Type', 'Acunetix')->get();
    foreach ($item as $it) {
        try {
            if ($it->accessKey != "") {
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification
                ])->withHeaders([
                    'X-ApiKeys' => "accessKey=" . $it->accessKey
                ])->timeout(1) // Set a longer timeout for the request (in seconds)
                    ->retry(2, 100) // Retry up to 2 times with a delay of 100 milliseconds between retries
                    ->get("https://" . $it->IP_Host . ":" . $it->Port);

                if ($response->successful()) {
                    $resp[] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Online",
                        "Auth" => "accessKey=" . $it->accessKey,
                        "Type"=>$it->Type,

                    ];
                } else {
                    $resp[] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Offline",
                        "Auth" => "accessKey=" . $it->accessKey,
                        "Type"=>$it->Type,
                    ];
                }
            } else {
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification
                ])->timeout(1) // Set a longer timeout for the request (in seconds)
                    ->retry(2, 100) // Retry up to 2 times with a delay of 100 milliseconds between retries
                    ->get("https://" . $it->IP_Host . ":" . $it->Port);
                if ($response->successful()) {
                    $resp[] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Online",
                        "Type"=>$it->Type,

                    ];
                } else {
                    $resp[] = [
                        "ip" => $it->IP_Host . ":" . $it->Port,
                        "answer" => "Offline",
                        "Type"=>$it->Type,

                    ];
                }
            }
        } catch (\Exception $e) {
            return response()->json('something went wrong');
        }
    }

    return response()->json(['Vm' => $resp]);
}

}
