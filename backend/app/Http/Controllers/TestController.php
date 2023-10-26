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

class TestController extends Controller
{

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

    public function get()
    {

 // HTML content to be inserted (replace this with your HTML)
 $htmlContent = '<p>Hello <strong>World</strong>!</p> <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII" width="714" height="293" />';

 $templatePath1 = public_path('1.docx');
 $templateProcessor1 = new TemplateProcessor($templatePath1);

 // Get XML content from HTML
 $xmlContent = CustomPhpWord::getXmlFromHtml($htmlContent);

 // Optionally, you can do something with $xmlContent

 return response()->json(['xml_content' => $xmlContent]);

}

}
