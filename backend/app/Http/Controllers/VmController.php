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
    public function index()
    {
        $resp = [];

        $item = Vm::all();

        foreach ($item as $it) {
            try {
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification
                ])->withHeaders([
                    'X-ApiKeys' => "accessKey=a507ad2749850e9acc8543f56284896b8792e9cf9b0dd54f8c35802e47c3fed5; secretKey=663fd55660cbdd8ccf1d603d4adf3d4b8c2d6394122c77ddea66ad311e77decb",
                ])->timeout(1) // Set a longer timeout for the request (in seconds)
                    ->retry(2, 100) // Retry up to 2 times with a delay of 100 milliseconds between retries
                    ->get("https://" . $it->IP_Port . "/scans");

                if ($response->successful()) {
                    $resp[$it->IP_Host] = [
                        "ip" => $it->IP_Port,
                        "answer" => "Online"
                    ];
                } else {
                    $resp[$it->IP_Host] = [
                        "ip" => $it->IP_Port,
                        "answer" => "Offline"
                    ];
                }
            } catch (ConnectionException $e) {
                // Handle connection timeout or other connection-related issues
                $resp[$it->IP_Host] = [
                    "ip" => $it->IP_Port,
                    "answer" => "Offline"
                ];
            }
        }

        return response()->json(['Vm' => $resp, 'status' => 200]);
    }
}
