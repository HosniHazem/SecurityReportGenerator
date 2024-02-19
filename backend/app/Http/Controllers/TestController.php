<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;


use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\HtmlString;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\Element\Image as PhpWordImage; // Alias for PhpWord Image class
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Chart;
use Illuminate\Support\Facades\DB;
use App\Models\Vm;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\CustomPhpWord\CustomPhpWord;
use Dompdf\Dompdf;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Vuln;
use App\Models\Sow;
use App\Models\Uploadanomalies;
use App\Models\Plugins;
use clsTbsZip;


class TestController extends Controller
{


public static function updateIPHostInformation()
   
 {
        
        $ipp = "srv.g6.ssk.lc:8834";
        $ApiKeys = "accessKey=0ad4ef73966ac93d4a8c10f854e665008d7a07fc540f17942501535ce7077dd3;secretKey=39cb3b8050857af6cfa39640a16204d68bd493337fe3340b1cbf59dc2b6ed7e9";
        $e = 53;
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






public static function translate($q)
{
    if(strlen($q) <10)  return $q;
    //$q= preg_replace('/[\x00-\x1F\x7F]/u', '', $q);
    $q=htmlspecialchars($q);
    //echo $q;
    $positionHttp = strpos($q, "http");
    $secondPart="";
    if($positionHttp >0)
    {
        $secondPart = substr($q,$positionHttp , strlen($q)-$positionHttp);
        $q = substr($q,0,$positionHttp);
    }
   // echo $q;
    $res= file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=en&tl=fr&hl=hl&q=".urlencode($q), $_SERVER['DOCUMENT_ROOT']."/transes.html");
    if(isset(json_decode($res)[0][0][0]))
    return json_decode($res)[0][0][0];
    return $q.$secondPart;
}
public static function translateAllVulnsCompliance()
{

   $allVuns =  DB::select("SELECT  `id`, `name`, `description`, `solution`,`synopsis` FROM  vuln WHERE Risk in ('FAILED', 'PASSED') and BID <> 'yes'");
   $i=0;
   foreach($allVuns as $vuln)
   {
    echo $allVuns[$i]->id."\n";
    $re = DB::table('vuln')
    ->where('id', $allVuns[$i]->id)
    ->update(['BID' => 'yes', 'name' => self::translate($allVuns[$i]->name),'description' => self::translate($allVuns[$i]->description),'solution' => self::translate($allVuns[$i]->solution),'synopsis' => self::translate($allVuns[$i]->synopsis)]);
    $i++;
}

}

public static function translateAllPlugins()
{

   $allPlugins =  DB::select("SELECT  `id`, `name`, `description`, `solution`,`synopsis` FROM  plugins WHERE translated <> 'yes'");
   $i=0;
   foreach($allPlugins as $plugin)
   {


       $re = DB::table('plugins')
    ->where('id', $allPlugins[$i]->id)
    ->update(['translated' => 'yes', 'name' => self::translate($allPlugins[$i]->name),'description' => self::translate($allPlugins[$i]->description),'solution' => self::translate($allPlugins[$i]->solution),'synopsis' => self::translate($allPlugins[$i]->synopsis)]);
    $i++;
}

}
public static function convertDocxToPdf($docxFilePath, $pdfFilePath)
{
    // Load the DOCX file
    $phpWord = IOFactory::load($docxFilePath);

    // Save the DOCX content as HTML
    $tempHtmlFile = tempnam(sys_get_temp_dir(), 'docx_to_pdf');
    $phpWord->save($tempHtmlFile, 'HTML');

    // Convert HTML to PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml(file_get_contents($tempHtmlFile));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save the PDF file
    file_put_contents($pdfFilePath, $dompdf->output());

    // Clean up temporary files
    unlink($tempHtmlFile);
}
    public function get()
    {
        $docxFilePath = public_path("0.docx");
        $pdfFilePath = public_path("output.pdf");
   // Load the DOCX file
   $phpWord = IOFactory::load($docxFilePath);

   // Save the DOCX content as HTML
   $tempHtmlFile = tempnam(sys_get_temp_dir(), 'docx_to_pdf');
   $phpWord->save($tempHtmlFile, 'HTML');

   // Convert HTML to PDF
   $dompdf = new Dompdf();
   $dompdf->loadHtml(file_get_contents($tempHtmlFile));
   $dompdf->setPaper('A4', 'portrait');
   $dompdf->render();

   // Save the PDF file
   file_put_contents($pdfFilePath, $dompdf->output());

   // Clean up temporary files
   unlink($tempHtmlFile);


}

}
