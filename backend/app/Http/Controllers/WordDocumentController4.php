<?php

namespace App\Http\Controllers;

use Illuminate\Support\HtmlString;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Element\Image as PhpWordImage; // Alias for PhpWord Image class
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Vuln; // Replace with your actual model
use App\Models\Sow; // Replace with your actual model

use App\Models\Customer; // Replace with your actual model
use App\Models\Uploadanomalies; // Replace with your actual model
use Knp\Snappy\Image as SnappyImage; // Alias for Knp\Snappy Image class
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use vendor\seblucas\tbszip;
use clsTbsZip;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\Models\Project; // Replace with your actual model
use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class WordDocumentController4 extends Controller
{
    public static   $AnnexesTitles = array("","Serveurs","Solution Réseau", "Bases de donnees", "Poste de travail",  "Actifs externe", "Applications", "Solution VOIP", "Solution MAILS");
    public static   $AnnexesLetters = array("","B","C", "D", "E",  "F", "G", "H", "I");


    private static function generateGlobalTableOfRowsWithTwoLevels( $templateProcessor,$query, $prjID, $KeyToDuplicateRows, $ColoredRowsArrays,$ColoredField, $prefixStats)
    {
        $AllRows=  DB::select($query,[$prjID,$prjID]);
//print_r( $AllRows);exit;
           $TwoLevelsTablesAllRows = [];
           for ($i=0;$i<count($AllRows);$i++)
           {

               foreach($AllRows[$i] as $key=>$value)
                   {

                       if(is_string($value) && str_contains($key, "ToBeClean"))   $AllRows[$i]->$key= self::cleanNewLineProblem($AllRows[$i]->$key, str_contains($key,"ref"));
                   }

                   $TwoLevelsTablesAllRows[$AllRows[$i]->$KeyToDuplicateRows] [$AllRows[$i]->$ColoredField][]=$AllRows[$i];
           }
           if($i>0)

           {
            $singleRow=$AllRows[0];

            $templateProcessor->cloneRow($KeyToDuplicateRows,  count($TwoLevelsTablesAllRows));
            $hostNumber=0;
            foreach ($TwoLevelsTablesAllRows as $HostSection)
            {
                $hostNumber++;


                foreach($ColoredRowsArrays as $colorRow)
                {

                    if(isset($HostSection[$colorRow]))
                    {
                                $templateProcessor->cloneRow($colorRow."_".$ColoredField."#".$hostNumber,  count($HostSection[$colorRow]));
                                $templateProcessor->setValue($KeyToDuplicateRows."_ip#".$hostNumber, $HostSection[$colorRow][0]->$KeyToDuplicateRows );


                            $order=1;
                            foreach($HostSection[$colorRow] as $HostOneVuln)
                            {

                                foreach ($HostOneVuln as $key => $value)
                                {
                               //     var_dump($HostOneVuln); exit;

                                    $templateProcessor->setValue($colorRow."_".$key."#".$hostNumber."#".$order,  $value);
                                    $templateProcessor->setValue($key."#".$hostNumber."#".$order,  $value);
                                }
                                $order++;
                            }
                    }
                    else $templateProcessor->cloneRowAndSetValues($colorRow."_".$ColoredField."#".$hostNumber, []);

                }

            }
           }


           return count($AllRows);
    }





    public function generateWordDocument(Request $request)
    {
        set_time_limit(5000);
        //ini_set('memory_limit', '1G');





       return;


    }
static function preparePagesDeGarde($templateProcessor, $annex_id,$customer, $project )
{

    $templateProcessor->setValue('SRV_TITLE', self::$AnnexesTitles[$annex_id]);
    $templateProcessor->setValue('SRV_LETTER', self::$AnnexesLetters[$annex_id]);
/*      $imageData = file_get_contents($customer->Logo);
    $localImagePath = public_path('images/'.basename($customer->Logo)); // Specify the local path to save the image
    file_put_contents($localImagePath, $imageData);
    $templateProcessor->setImageValue('icon', $localImagePath); */
    $templateProcessor->setValue('SN',  $customer->SN);
    $templateProcessor->setValue('LN',  $customer->LN);
    $templateProcessor->setValue('PRJ',  $project->Nom);
    $templateProcessor->setValue('Y',  $project->year);
    $templateProcessor->setValue('URL',  $project->URL);
    $templateProcessor->setValue('DESC',  $project->description);

   }
   public static function send_whatsapp($message="Test"){
    $url='https://api.callmebot.com/whatsapp.php?phone=21629961666&apikey=2415178&text='.urlencode($message);
    if($ch = curl_init($url))
    {
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $html = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // echo "Output:".$html;  // you can print the output for troubleshooting
        curl_close($ch);
        return (int) $status;
    }
    else return ;

}

}
