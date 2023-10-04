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

class WordDocumentController3 extends Controller
{
    public static   $AnnexesTitles = array("","Serveurs","Solution Réseau", "Bases de donnees", "Poste de travail",  "Actifs externe", "Applications", "Solution VOIP", "Solution MAILS");
    public static   $AnnexesLetters = array("","B","C", "D", "E",  "F", "G", "H", "I");

   public static function cleanNewLineProblem ($string, $seeAlso)
    {

        $pattern1 = "/([[:punct:]]+ *)(\n)+/";
		$pattern11 = "/([[:punct:]]+ *)(\{\{1\}\})+/";

        $pattern2 = "/(\n)+( *-)/";
		$pattern21 = "/(\{\{1\}\})+( *-)/";

        $pattern3 = "/(\n)+/";
		$pattern31 = "/(\{\{1\}\})+/";

        $replacement = "</w:t></w:r><w:r><w:br/><w:t>";
        $string = htmlspecialchars($string);
        $string = preg_replace($pattern1, '${1}'.$replacement, $string);
		$string = preg_replace($pattern11, '${1}'.$replacement, $string);
        $string = preg_replace($pattern2, $replacement.'${2}', $string);
		$string = preg_replace($pattern21, $replacement.'${2}', $string);

        if ($seeAlso==1)  $string = preg_replace( $pattern3 , $replacement, $string);
        $string = preg_replace( $pattern31 , " ", $string);


        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
        return $string;
    }

    private static function setTotalValues($prefix, $arraykeys,$templateProcessor,$AllRows )
    {
        foreach ($arraykeys as $key=>$value)
        {
            //print_r($key);exit(0);
            $templateProcessor->setValue($prefix.$key,  array_sum(array_column($AllRows, $key)));
        }
    }

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











    private static function generateGlobalTableOfRows( $templateProcessor,$query, $prjID, $KeyToDuplicateRows, $ColoredRowsArrays,$ColoredField, $prefixStats)
    {

//var_dump($query);exit;
       $AllRows=  DB::select($query,[$prjID,$prjID]);
    //   var_dump($AllRows);exit;
       $AllRowsPerColor=[];

        for ($i=0;$i<count($AllRows);$i++)
        {
            foreach($AllRows[$i] as $key=>$value)
                {

                    if(is_string($value) && str_contains($key, "ToBeClean"))   $AllRows[$i]->$key= self::cleanNewLineProblem($AllRows[$i]->$key, str_contains($key,"ref"));
                }


            if(isset($ColoredField))
            {

                        $newField=  $AllRows[$i]->$ColoredField."_" . $ColoredField ;
                        $AllRows[$i]->$newField=$AllRows[$i]->$ColoredField;
                        $AllRowsPerColor[$AllRows[$i]->$ColoredField][]=$AllRows[$i];

             }
        }

     //   var_dump($TwoLevelsTablesAllRows); exit;
        if($i>0) $singleRow=$AllRows[0];


              //  var_dump("_".$ColoredField, $AllRowsPerColor); exit;
       if(isset($ColoredField))
        {
            foreach($ColoredRowsArrays as $colorRow)
            {
              if (isset( $AllRowsPerColor[$colorRow]))
              {
                $templateProcessor->cloneRowAndSetValues($colorRow."_".$ColoredField,  $AllRowsPerColor[$colorRow]);
           }

            }
        }
        else $templateProcessor->cloneRowAndSetValues($KeyToDuplicateRows,  $AllRows);

         if(isset($prefixStats))
         {
            $templateProcessor->SetValue($prefixStats,  count($AllRows));
          //  var_dump($prefixStats,$singleRow,$templateProcessor,$AllRows );exit;
            if(isset($singleRow)) self::setTotalValues($prefixStats,$singleRow,$templateProcessor,$AllRows );
       }
       return count($AllRows);
    }


    public function generateWordDocument(Request $request)
    {
        set_time_limit(5000);
        //ini_set('memory_limit', '1G');

        $annex_id =  $request->annex_id;
    //    var_dump(get_object_vars($request)); exit;
        include ("sqlRequests.php");
        $listOfFile=[];
        $returnedArray = [];
        foreach($request->project_id as $prj_id)
        {
            $returnedArray [] = $prj_id;
            $project =Project::find($prj_id);
            $customer =Customer::find($project->customer_id);
            $arrayConfig=array(
                "3.docx" => array(0,1,2),
                "4.docx" => array(3),
            );

            foreach($annex_id as $Annex)
            {
                $iteration=0;
                $returnedArray[$prj_id][]=self::$AnnexesLetters[$Annex];

                foreach($arrayConfig as $tmplate => $listOfDocParts)
                {
                    $iteration++;

                    $nbrOfRowsAddedToFile=0;
                    $templatePath = public_path($tmplate);
                    $templateProcessor = new TemplateProcessor($templatePath);
                    self::preparePagesDeGarde($templateProcessor, $Annex,$customer, $project );
                    foreach($listOfDocParts as $i)
                        {
                            $isitComplex=null;
                            if ($i==2)  $nbrOfRowsAddedToFile+= self::generateGlobalTableOfRowsWithTwoLevels($templateProcessor,str_replace($SqlQueriesMarks[0], $SqlQueriesMarks[$Annex], $DefaultQuery[$i]), $prj_id,$keyToDuplicateRows[$i], $ColoredRowsArrays[$i],$RowOfColoring[$i], $prefixTLT[$i]);
                           else $nbrOfRowsAddedToFile+= self::generateGlobalTableOfRows($templateProcessor,str_replace($SqlQueriesMarks[0], $SqlQueriesMarks[$Annex], $DefaultQuery[$i]), $prj_id,$keyToDuplicateRows[$i], $ColoredRowsArrays[$i],$RowOfColoring[$i], $prefixTLT[$i]);
                        }
                $outputFileName = $prj_id .'_tchRpt_Annx_' . self::$AnnexesLetters[$Annex] .$iteration."_".self::$AnnexesTitles[$Annex]."_".$customer->SN. '.docx';
                $outputPath = public_path('storage/' . $outputFileName);
                $returnedArray[$prj_id][self::$AnnexesLetters[$Annex]][] = $nbrOfRowsAddedToFile;
                if($nbrOfRowsAddedToFile>0)
                    {
                        $templateProcessor->saveAs($outputPath);
                        $listOfFile[]=$outputPath;
                    self::send_whatsapp("[App2_TechReport] ". $outputFileName ." was created with sucess");
                    }
                }
            }

        }

        print_r($returnedArray);
        print_r($listOfFile);

       return $returnedArray;

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
   static function send_whatsapp($message="Test"){
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
