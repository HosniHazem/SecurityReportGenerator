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



//the function to fill ansi  repot
    public function generateWordDocument(Request $request)
    {
        set_time_limit(5000);

        //query for  customers table 
        $sqlCustomers = 'SELECT 
        c.id AS ID,
        c.SN AS SN,
        c.LN AS LN,
        c.type AS Type,
        c.Logo AS Logo,
        c.Description AS Description,
        c.SecteurActivite AS SecteurActivite,
        c.Categorie AS Categorie,
        c.`Site web` AS `Site_Web`,
        c.`Addresse mail` AS `Adresse_mail`,
        c.Organigramme AS Organigramme,
        c.Network_Design AS Network_Design,
        t.leType AS leType
    FROM glb_customers AS c
    LEFT JOIN glb_type AS t ON c.type = t.id
    WHERE c.id = ?;';
    
        //query for process  table
        $sqlProcess = 'SELECT  RM_Processus_domains.ID,Processus_domaine AS  process , MAX(D) AS  Process_D , MAX(I) AS Process_I, MAX(C) AS Process_C
        FROM RM_Processus_Actifs_Valeurs
        LEFT JOIN RM_Processus_domains ON RM_Processus_Actifs_Valeurs.ID_Processus = RM_Processus_domains.ID
        WHERE RM_Processus_domains.ID_ITERATION = 1
        GROUP BY Processus_domaine';        
        


        $sql =  <<<HERE10
        SELECT `standards_controls`.`Clause`, `standards_controls`.`controle`, rm_answers.Answer, `rm_questions`.`Bonne pratique` as 'bp', `rm_questions`.`Vulnérabilité` as 'vuln'
        FROM `standards_controls` LEFT JOIN rm_questions on standards_controls.ID=`rm_questions`.`Standard_Control_id`
        LEFT Join rm_answers on rm_answers.ID_Question=rm_questions.ID WHERE LENGTH(`rm_questions`.`Vulnérabilité`) > 5
        order by `Clause`, `controle`,`rm_questions`.`Question_numero` ASC;
        HERE10;

        $templatePath = public_path("0.docx");
        $templateProcessor = new TemplateProcessor($templatePath);


        $outputFileName = 'ansi2023.docx';
        $outputPath = public_path('' . $outputFileName);

         //Process Table  
        // return response()->json($Process);
        $Process = DB::select($sqlProcess);
        $rowCount = count($Process);
        $modifiedProcessArray = [];

        // Loop through each item in the $Process array
        foreach ($Process as $item) {
            // Apply htmlspecialchars to the "process" property of the object
            $modifiedProcess = htmlspecialchars($item->process, ENT_XML1);
            $item->process = $modifiedProcess;
        
            // Add the modified object to the new array
            $modifiedProcessArray[] = $item;
        }

      return response()->json($modifiedProcessArray); 
         $templateProcessor->cloneRowAndSetValues('process',$modifiedProcessArray);
    

       
        //Customer Table 
        $Customers=  DB::select($sqlCustomers,[$request->customer]);
        if (!empty($Customers)) {
            $firstRow = $Customers[0];
            $SN = $firstRow->SN;
            $LN = $firstRow->LN;
            $typeCompany = $firstRow->leType;

            $ActivitySector=$firstRow->SecteurActivite;
            $Categorie =$firstRow->Categorie;
            $siteWeb=$firstRow->Site_Web;
            $mailAddress=$firstRow->Adresse_mail;

            
            $templateProcessor->setValue('SN', $SN);
            $templateProcessor->setValue('LN', $LN);
            $templateProcessor->setValue('typeCompany', $typeCompany);
            $templateProcessor->setValue('SecteurActivite', $ActivitySector);
            $templateProcessor->setValue('Categorie', $Categorie);
            $templateProcessor->setValue('siteweb', $siteWeb);
            $templateProcessor->setValue('mailadress', $mailAddress);
        } else {
            return response()->json("no customer with this id exists ");
        }

        $AllRows=  DB::select($sql);

        $allRowsAsArray=[];

        foreach($AllRows as $row){

            if($row->Answer>0)
            {
                 $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][]=$row->bp;
            }
            else
            {
                $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][]=$row->vuln;
          //  echo $row->Answer."aaaaaaaaaaaaaaaa";
            }

        }
//return $allRowsAsArray;
        foreach ($allRowsAsArray as $ClauseId => $rowData) {


                foreach ($rowData as $ControlID => $cellData) {

                    self::setOneRowControl($templateProcessor,$ClauseId, $ControlID, $cellData, 1, "_BestPractice#" );

                    self::setOneRowControl($templateProcessor,$ClauseId, $ControlID, $cellData, 0, "_Vuln#" );


                }

        }


        $templateProcessor->saveAs($outputPath);


      //  return response()->download($filepath,$filename)->deleteFileAfterSend(true);



    }

static function setOneRowControl($templateProcessor,$ClauseId, $ControlID, $cellData, $type, $typeTag )
{
 //   echo  $ClauseId."_". $ControlID."\n";

 if(!isset($cellData[$type])) {
        echo $ClauseId.$typeTag.$ControlID;
        $templateProcessor->setValue($ClauseId.$typeTag.$ControlID, "");return;
    };

    $templateProcessor->cloneRow($ClauseId.$typeTag.$ControlID, count($cellData[$type]));
               //        var_dump("\${".$ClauseId.$typeTag.$ControlID."#$i}", $value);
    $i=1;

    foreach($cellData[$type] as $attr=>$value)
    {

        $templateProcessor->setValue($ClauseId.$typeTag.$ControlID."#$i", $value);
        $templateProcessor->setValue($ClauseId.$typeTag.$ControlID, $value);
        $i++;
    }
    if($i==1)
    {   echo $ClauseId.$typeTag.$ControlID;
        $templateProcessor->cloneRowAndSetValues($ClauseId.$typeTag.$ControlID, []);
    }
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
