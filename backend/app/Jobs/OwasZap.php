<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Vuln;

class OwasZap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id=0;
        $baseUrl = "http://acu.g6.ssk.lc:8081/JSON/alert/view/alert/?apikey=d31c2oo5sn998vpk0cpouf0i0h&id={$id}";
   
       do{
   
           $response = Http::get($baseUrl);
           $data = [
               [
                   'Name' => $response['alert']['name'],
                   'Risk' => $response['alert']['risk'],
                   'Description' => $response['alert']['description'],
                   'Solution' => $response['alert']['solution'],
                   'Host' => self::parseBaseUrl($response['alert']['url']),
                   'See Also' => json_encode(self::parseBaseUrl($response['alert']['reference'])),
               ],
           ];
           Vuln::insertOrIgnore($data);
   
           $id++;
       }while ($response->status() != 400);
       return response()->json(['message' => 'done', 'status' => 200]);
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
