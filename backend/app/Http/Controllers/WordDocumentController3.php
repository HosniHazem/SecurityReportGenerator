<?php

namespace App\Http\Controllers;

use Illuminate\Support\HtmlString;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Element\Image as PhpWordImage; // Alias for PhpWord Image class
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Chart;
use PhpOffice\PhpWord\Shared\Converter;
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
    public static   $AnnexesTitles = array("","Serveurs","Solutions Réseaux et Infra", "Bases des données", "Postes de travail",  "Actifs externes", "Applications", "Solutions VOIP", "Solutions MAILS");
    public static   $AnnexesLetters = array("","B","C", "D", "E",  "F", "G", "H", "I");
    public static $currentAnnex=0;
    public static function QualityCheck(Request $req)
    {
        set_time_limit(50000);
        $actionlink = array("translatePlugins", "translateVulns", "getPluginsFromAllServers"," ", "", "", "", "", "", "");
        $sqls = array(
            <<< HERE0
            SELECT  'Nombre de Plugins non traduit', count(DISTINCT `Plugin ID`), 'translatePlugins'  FROM vuln where vuln.upload_id in  (select id from uploadanomalies where uploadanomalies.ID_Projet=?) and `Plugin ID` in (SELECT id FROM `plugins` WHERE `translated`<>'yes' )
            HERE0,
            <<< HERE1
            SELECT 'Nombre de Vulns non traduit', count(DISTINCT `id`),'translateVulns'  FROM vuln where vuln.upload_id in  (select id from uploadanomalies where uploadanomalies.ID_Projet=?) and Risk in ('PASSED', 'FAILED') AND  `BID`<>'yes'
            HERE1,
            <<< HERE2
            SELECT 'Nombre de Plugins manquants', count(DISTINCT `Plugin ID`) , 'getPluginsFromAllServers' FROM vuln where vuln.upload_id in  (select id from uploadanomalies where uploadanomalies.ID_Projet=?) and `Plugin ID` not in (SELECT id FROM `plugins` )
            HERE2,
            <<< HERE3
            SELECT sow.Type As "Type", count(*) ,'no Link' FROM `vuln` LEFT Join sow on sow.IP_Host=Host WHERE `upload_id` in (select id from uploadanomalies where uploadanomalies.ID_Projet = ?) GROUP BY sow.Type;
            HERE3,
            <<< HERE4
            SELECT Concat (sow.Type," non encore scannee") As "Type",  IP_Host ,'Danger !!!' FROM `sow` WHERE Type<>'PC' AND `Projet`= ?  AND IP_Host not in (SELECT DISTINCT Host FROM vuln WHERE ID_Projet=?)  order by sow.Type;
            HERE4,

    );
    /* */
        //$listOfCombinedItems()
        $qualityChecher=[];
        $qualityChecher[0] = array("Item" , "Valeur", "link");
        $i=0;
        foreach ($sqls as $index => $sql) {
            if ($index===4){
                $returnedRows=DB::select($sql,array($req->project_id, $req->project_id )) ;
            }else {
                $returnedRows=DB::select($sql,array($req->project_id)) ;
            }
 
            foreach ($returnedRows as $singleRow)
              {
                $i++;
                foreach ($singleRow as $key => $value)
                {

                    //$qualityChecher[$i] [] = $key;
                    $qualityChecher[$i] [] = $value;


                }
            }
        }
       // return $qualityChecher;
        //$qualityChecher = array ( array("A", "B", "C", "link"),  array("A", "B", "C", "link"),  array("A", "B", "C", "link"));
        return response()->json(['QC' => $qualityChecher, 'status' => 200]);
    }
    public static function getPourcentage ($source, $ttl_hosts)
    {
        $v_Global=0;
        if($ttl_hosts!=null)
        if($source[0] > 0)
        $v_Global = 75 + round(25 * ($source[0]/$ttl_hosts));
        elseif ($source[1] > 0) $v_Global = 50 + round(25 * ($source[1]/$ttl_hosts));
        elseif ($source[2] > 0) $v_Global = 25 + round(25 * ($source[2]/$ttl_hosts));
        else $v_Global = round(25 * ($source[3]/$ttl_hosts));

        return min(99, $v_Global);

    }

    public function generateExcelDocument(Request $req)
    {
        set_time_limit(50000);
//        '''SELECT ... FROM someTable WHERE etcINTO OUTFILE 'someTableExport.csv' CHARACTER SET utf8mb4FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' ESCAPED BY ''LINES TERMINATED BY '\r\n';'''
        $sqls=array(
            <<<HERE1
        ((select "Plugin ID" ,"Risk" ,"Type", "Host" ,"name" ,"synopsis" ,"exploited_by_malware" ,"exploit_available" ,"age_of_vuln" ,"description" ,"Plugin Output" ,"solution")
        UNION ALL
        (select `Plugin ID` ,`Risk` ,Type,`Host` ,`plugins`.`name` ,`plugins`.`synopsis` ,`plugins`.`exploited_by_malware` ,`plugins`.`exploit_available` ,`plugins`.`age_of_vuln` ,`plugins`.`description` ,`Plugin Output` ,`plugins`.`solution` from `vuln`
        left join `plugins` on `Plugin ID` = `plugins`.`id`
        LEFT JOIN sow on vuln.Host=sow.IP_Host
        where `upload_id` in (select `uploadanomalies`.`ID` from `uploadanomalies` where `uploadanomalies`.`ID_Projet` = ?)
        AND sow.Projet=?
        AND Risk in ('Critical', 'Medium', 'High', 'Low')
        group by `Host`,`plugins`.`name`))
        INTO OUTFILE 'PLACEHOLDER2'  CHARACTER SET utf8mb4
        FIELDS ENCLOSED BY '\"' TERMINATED BY ';' ESCAPED BY '\"' LINES TERMINATED BY '\r\n'
        HERE1,
        <<<HERE2
        ((select "Plugin ID" ,"Risk" ,"Type", "Host" ,"name" ,"synopsis" ,"exploited_by_malware" ,"exploit_available" ,"age_of_vuln" ,"description" ,"Plugin Output" ,"solution")
        UNION ALL
        (select `Plugin ID` ,`Risk` ,Type,`Host` ,`name` ,`synopsis` ,"" ,"" ,"" ,`description`  ,"" ,`solution` from `vuln`
        LEFT JOIN sow on vuln.Host=sow.IP_Host
        where `upload_id` in (select `uploadanomalies`.`ID` from `uploadanomalies` where `uploadanomalies`.`ID_Projet` = ?)
        AND sow.Projet=?
        AND Risk not in ('Critical', 'Medium', 'High', 'Low')
        group by `Host`,`description`))
        INTO OUTFILE 'PLACEHOLDER2'  CHARACTER SET utf8mb4
        FIELDS ENCLOSED BY '\"' TERMINATED BY ';' ESCAPED BY '\"' LINES TERMINATED BY '\r\n'
        HERE2
        );
        $fileNames = [];
        foreach($sqls as $sql)
        {
            $fileNames [] = $filename="c:/tmp/". $req->filename.time().".csv";
            $sql= str_replace("PLACEHOLDER2", $filename, $sql);
            if(isset($req->OnlyVuln)) $sql= str_replace("PLACEHOLDER1", "and Risk in ('Critical', 'Medium', 'High', 'Low')", $sql);
            else $sql = str_replace("PLACEHOLDER1", " ", $sql);
            DB::select($sql,array($req->project_id, $req->project_id ));
        }
//      return response()->download("c:/tmp/".$filename)->deleteFileAfterSend();
      return self::ZipAndDownload($req->project_id."_", "CSV", $fileNames);

    }


   public static function cleanNewLineProblem ($string, $seeAlso)
    {
        $pattern1 = "/([[:punct:]]+ *)(\n)+/";
//		$pattern11 = "/([[:punct:]]+ *)(\{\{1\}\})+/";

        $pattern2 = "/(\n)+( *-)/";
//		$pattern21 = "/(\{\{1\}\})+( *-)/";

        $pattern3 = "/(\n)+/";
//		$pattern31 = "/(\{\{1\}\})+/";

        $replacement = "</w:t></w:r><w:r><w:br/><w:t>";
        $string = htmlspecialchars($string);
        $string = preg_replace($pattern1, '${1}'.$replacement, $string);
	//	$string = preg_replace($pattern11, '${1}'.$replacement, $string);
        $string = preg_replace($pattern2, $replacement.'${2}', $string);
	//	$string = preg_replace($pattern21, $replacement.'${2}', $string);

        if ($seeAlso==1)  $string = preg_replace( $pattern3 , $replacement, $string);
   //     $string = preg_replace( $pattern31 , " ", $string);


        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
        return $string;
    }

        public static function setGlobalStats ($prjID, $templateProcessor, $isitAnnexeA )
        {
            $sql =<<<HERE0
            SELECT
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_MLW,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_ExC,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_ExH,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_ExM,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_ExL,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_CR,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_HI,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_MD,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_LW,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_NC,
             SUM(TLT_Hosts_MLW) AS TLT_Hosts_CF
            FROM
            (
            SELECT
    COUNT(IF(Exp_Malware>0,1,NULL))as TLT_Hosts_MLW,
    COUNT(IF(Critical_Ex>0,1,NULL))as TLT_Hosts_ExC,
    COUNT(IF(High_Ex>0,1,NULL))as TLT_Hosts_ExH,
    COUNT(IF(Medium_Ex>0,1,NULL))as TLT_Hosts_ExM,
    COUNT(IF(Low_Ex>0,1,NULL))as TLT_Hosts_ExL,
    COUNT(IF(Critical>0,1,NULL))as TLT_Hosts_CR,
    COUNT(IF(High>0,1,NULL))as TLT_Hosts_HI,
    COUNT(IF(Mediu>0,1,NULL))as TLT_Hosts_MD,
    COUNT(IF(Low>0,1,NULL))as TLT_Hosts_LW,
    max(FAILED2)as TLT_Hosts_NC, max(PASSED2)as TLT_Hosts_CF

    FROM (
    SELECT
    vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4,
        COUNT(IF( `exploited_by_malware` = 'true' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = 'Critical' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = 'High' AND ( `exploit_available` = 'true' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = 'Medium' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = 'Low' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Low_Ex,
        COUNT(IF(vuln.`Risk` = 'Critical', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = 'High', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = 'Medium', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = 'Low', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = 'FAILED', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = 'PASSED', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
        WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?)  and sow.IP_Host = vuln.Host and sow.Projet=?

    GROUP BY
    `Host` ,  vuln.Name
    ) t

    GROUP BY hostip
    )tt
    HERE0;
    $AllRows=  DB::select($sql,[$prjID,$prjID]);
    print_r(json_decode(json_encode($AllRows)));
    //$templateProcessor->setValues(json_decode(json_encode($AllRows)));

        }
        public static function setVulnPatchValues($prjID, $templateProcessor, $isitAnnexeA )
    {
        include("sqlRequests.php");

        $query = <<<HERE
            SELECT `t`.`Risk`,`t`.`age_of_vuln`,count(*) AS nombre FROM
                (
                    SELECT `vuln`.`Risk`,`plugins`.`age_of_vuln`,`vuln`.`Name`,count(*)  FROM vuln
                         LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
                        RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
                        WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?) CLAUSENUMBER1   and sow.IP_Host = vuln.Host and sow.Projet=?
                        CLAUSENUMBER2
                         AND     `vuln`.`Risk` in ('Critical','High','Medium','Low')
                        group by `vuln`.`Risk`,`plugins`.`age_of_vuln`,`vuln`.`Name`,`vuln`.`Host`
                ) `t`
              group by `t`.`Risk`,`t`.`age_of_vuln`;
        HERE;
        $listOfAgesOfVulns = ["", "0 - 7 days",        "7 - 30 days",        "30 - 60 days",        "60 - 180 days",        "180 - 365 days",        "365 - 730 days",        "730 days +"];
                    if($isitAnnexeA==1)
                    {
                        $query=str_replace($SqlQueriesMarks[0], " ", $query);
                    }
                    else
                    {
                        //var_dump($SqlQueriesMarks[0], " and sow.Type=\"".self::$currentAnnex."\"", $query);
                        $query=str_replace($SqlQueriesMarks[0], " and sow.Type=\"".$SqlQueriesMarks[self::$currentAnnex][0]."\"", $query);
                    }

        $AllRows=  DB::select($query,[$prjID,$prjID]);

        foreach ($AllRows as $row)
        {
            $templateProcessor->setValue($row->Risk."_".$row->age_of_vuln,  $row->nombre);
          //  var_dump($row->Risk."_".$row->age_of_vuln,  $row->nombre);
        }
        foreach($listOfAgesOfVulns as $age_of_vuln)
        {
            foreach ($arrayRisks as $risk)
            {
                $templateProcessor->setValue($risk."_".$age_of_vuln,  "-");
            }
        }
    }

    private static function setTotalValues($prefix, $arraykeys,$templateProcessor,$AllRows )
    {
     $totalStatsName= array(0=>"Hosts_CR",1=>"Hosts_HI",2=>"Hosts_MD",3=>"Hosts_LW");
     $totalStats=[];


        foreach ($arraykeys as $key=>$value)
        {
            //print_r($key);exit(0);
            $templateProcessor->setValue($prefix.$key,  array_sum(array_column($AllRows, $key)));
        }
        for($i=0;$i<4;$i++)
        {
            $totalStats[$i] = array_sum(array_column($AllRows, $totalStatsName[$i]));
        }

        $templateProcessor->setImageValue('V_Global', public_path('images/'. self::getPourcentage($totalStats, count($AllRows)).".png"));

    }

    private static function generateGlobalTableOfRowsWithTwoLevels( $templateProcessor,$query, $prjID, $KeyToDuplicateRows, $ColoredRowsArrays,$ColoredField, $prefixStats)
    {
        $AllRows=  DB::select($query,[$prjID,$prjID]);
//print_r( $query);
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
            echo $KeyToDuplicateRows;
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
                    else
                    {
                        $templateProcessor->cloneRowAndSetValues($colorRow."_".$ColoredField."#".$hostNumber, []);
                        //$templateProcessor->cloneRowAndSetValues($colorRow."_".$ColoredField, []);
                    }


                }

            }
           }


           return count($AllRows);
    }











    private static function generateGlobalTableOfRows( $templateProcessor,$query, $prjID, $KeyToDuplicateRows, $ColoredRowsArrays,$ColoredField, $prefixStats)
    {

//var_dump($query); return 0;
       $AllRows=  DB::select($query,[$prjID,$prjID]);

       $AllRowsPerColor=[];

        for ($i=0;$i<count($AllRows);$i++)
        {
            foreach($AllRows[$i] as $key=>$value)
                {

                    if(is_string($value) && str_contains($key, "ToBeClean"))   $AllRows[$i]->$key= self::cleanNewLineProblem($AllRows[$i]->$key, str_contains($key,"ref"));
                    if(is_string($value)) {
                        if(strlen($value)==0) $AllRows[$i]->$key ="-";
                        if($value==="true") $AllRows[$i]->$key ="Vrai";
                        if($value==="false") $AllRows[$i]->$key ="Faux";
                    }
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
           else  $templateProcessor->cloneRowAndSetValues($colorRow."_".$ColoredField, []);

            }
        }
        else $templateProcessor->cloneRowAndSetValues($KeyToDuplicateRows,  $AllRows);

         if(isset($prefixStats))
         {
            $templateProcessor->SetValue($prefixStats,  count($AllRows));
            self::setVulnPatchValues($prjID, $templateProcessor,0);
          //  var_dump($prefixStats,$singleRow,$templateProcessor,$AllRows );exit;
            if(isset($singleRow)) self::setTotalValues($prefixStats,$singleRow,$templateProcessor,$AllRows );
       }
       return count($AllRows);
    }


    public function generateWordDocument(Request $request)
    {
        set_time_limit(50000);

    //    print_r($request->project_id);exit;
        $annex_id =  $request->annex_id;
      //  var_dump(get_object_vars($request)); exit;
        include ("sqlRequests.php");
        $listOfFile=[];
        $returnedArray = [];
     //   $prj_id=7;
        foreach($request->project_id as $prj_id)
        {
            $returnedArray [] = $prj_id;
            $project =Project::find($prj_id);
            $customer =Customer::find($project->customer_id);
            $arrayConfig=array(
                "3.docx" => array(0,1,2),
                "4.docx" => array(3),
                "5.docx" => array(4),
            );
            $annex_id=[1,2,3,4,5,6,7,8] ;
            foreach($annex_id as $Annex)
            {
                $iteration=0;
                $returnedArray[$prj_id][]=self::$AnnexesLetters[$Annex];
                self::$currentAnnex=$Annex;

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
                    }// else print_r($outputFileName);
                }
            }

        }

        if(isset($request->ZipIt) or 1==1)
       return self::ZipAndDownload($project->Nom, "techAnnexes_", $listOfFile);
        else  print_r($listOfFile);

    }
static function preparePagesDeGarde($templateProcessor, $annex_id,$customer, $project )
{
    $templateProcessor->setValue('SRV_TITLE', self::$AnnexesTitles[$annex_id]);
    $templateProcessor->setValue('SRV_LETTER', self::$AnnexesLetters[$annex_id]);

    $localImagePath = public_path('images/uploads/'.$customer->Logo); // Specify the local path to save the image
    $templateProcessor->setImageValue('icon', $localImagePath);
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
public static function ZipAndDownload($project, $prefix, $filePaths)
{
    // Create a zip archive
 $zip = new ZipArchive;
 $tempDirectory = storage_path('app/temp');
 $zipFileName = $prefix. $project . '.zip';
 $zipFilePath = $tempDirectory . '/' . $zipFileName;

 if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
     // Add the files to the zip archive
     foreach ($filePaths as $filePath) {
         $file = basename($filePath);
         $zip->addFile($filePath, $file);
     }

     $zip->close();
     WordDocumentController3::send_whatsapp($zipFileName ." Ready");
     // Download the zip archive
     return response()->download($zipFilePath);//->deleteFileAfterSend();
 }

 // If zip creation fails, return an error response
 WordDocumentController3::send_whatsapp($zipFileName ." can't be downloaded");
 return response()->json(['error' => 'Failed to create zip archive'], 500);
}

public static function translate($q)
{
    if(strlen($q) <10)  return $q;
    //$q= preg_replace('/[\x00-\x1F\x7F]/u', '', $q);
   // $q=htmlspecialchars($q);
    //echo $q;
 //   $positionHttp = strpos($q, "http");
 $q=urlencode($q);
    $q= str_replace("http://", " ",$q);
    $q= str_replace("https://", " ",$q);

    $secondPart="";
   // echo $positionHttp."\n";
 /*   if($positionHttp >0)
    {
        $secondPart = substr($q,$positionHttp , strlen($q)-$positionHttp);
      //  echo $secondPart."@@@@@@@@@@@@\n";
        $q = substr($q,0,$positionHttp);
      //  echo $q."@@@@@@@@@@@@€€€€€€\n";
    }*/
   // echo $q;

    $res= @file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=en&tl=fr&hl=hl&q=".urlencode($q), $_SERVER['DOCUMENT_ROOT']."/transes.html");
   // var_dump($res);

    if(isset(json_decode($res)[0][0][0]))
    {
        $q="";
        foreach(json_decode($res)[0]  as $tabOfReturns)
        {
            if(isset($tabOfReturns[0])) $q.= $tabOfReturns[0];
        }

    }

    return $q.$secondPart;
}
public static function translateAllVulnsCompliance()
{
    set_time_limit(50000);

   $allVuns =  DB::select("SELECT  `id`, `name`, `description`, `solution`,`synopsis` FROM  vuln WHERE Risk in ('FAILED', 'PASSED', 'WARNING') and BID not in ('noway', 'yes' )");
   $i=0;
   foreach($allVuns as $vuln)
   {
    echo $allVuns[$i]->id."\n";
    $re = DB::table('vuln')
    ->where('id', $allVuns[$i]->id)
    ->update(['BID'  => 'noway']);
    $re = DB::table('vuln')
    ->where('id', $allVuns[$i]->id)
    ->update(['BID' => 'yes', 'name' => self::translate($allVuns[$i]->name),'description' => self::translate($allVuns[$i]->description),'solution' => self::translate($allVuns[$i]->solution),'synopsis' => self::translate($allVuns[$i]->synopsis)]);
    $i++;
}

return response()->json(['message'=>'done','status' => 200]);
}

public static function translateAllPlugins()
{
    set_time_limit(50000);

   $allPlugins =  DB::select("SELECT  `id`, `name`, `description`, `solution`,`synopsis` FROM  plugins WHERE translated not in ('noway', 'yes' )");
   $i=0;
   foreach($allPlugins as $plugin)
   {
    $re = DB::table('plugins')
    ->where('id', $allPlugins[$i]->id)
->update(['translated' => 'noway']);

       $re = DB::table('plugins')
    ->where('id', $allPlugins[$i]->id)
    ->update(['translated' => 'yes', 'name' => self::translate($allPlugins[$i]->name),'description' => self::translate($allPlugins[$i]->description),'solution' => self::translate($allPlugins[$i]->solution),'synopsis' => self::translate($allPlugins[$i]->synopsis)]);
    $i++;
}
return response()->json(['message'=>'done','status' => 200]);

}



}
