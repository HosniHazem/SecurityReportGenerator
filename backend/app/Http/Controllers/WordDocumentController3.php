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

class WordDocumentController2 extends Controller
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
    private static function generateVulnPerHostPart($it, $templateProcessor)
    {
        $allVulns = [];
        $allVulnsAsArray= [];
        $currenthost='nothing';
                foreach ($it['data4'] as $item) {
         //   print_r($item);
            foreach ($item as $vuln) {

                $allVulns[$vuln->Host] []=$vuln;
            }
        }
    $HostOrder=1;
    $templateProcessor->cloneRow("id_SRV_host", count($allVulns));
    foreach ($allVulns as $allHostsVulns) {
        $C_vulnOrder=$H_vulnOrder=$M_vulnOrder=$L_vulnOrder=[];
          foreach ($allHostsVulns as $vuln) {
//print_r($allHostsVulns); return 0;
            switch ($vuln->Risk) { // Access object property using -> notation

                case 'Critical':
                   if(isset($allVulnsAsArray[$HostOrder]) && isset($allVulnsAsArray[$HostOrder]['C'])) $C_vulnOrder[$vuln->Host]=count($allVulnsAsArray[$HostOrder]['C'])+1;
                   else  $C_vulnOrder[$vuln->Host]=1;

                    $C_vulnAsArray = [
                        'c_SRV_host#'. $HostOrder => $vuln->Host,
                        'c_SRV_Risk_Factor_host#'. $HostOrder => $vuln->Risk,
                        'c_SRV_Synopsis#'. $HostOrder => $vuln->synopsis,
                        'c_SRV_vuln_port_host#'. $HostOrder => $vuln->ports,
                        'c_SRV_exploi_host#'. $HostOrder => $vuln->exploitability,
                        'c_SRV_vuln_age_host#'. $HostOrder => $vuln->age_of_vuln,
                        'id_c2_serv#'.$HostOrder=>$C_vulnOrder[$vuln->Host]
                    ];

                         break;
                    case 'High':
                        if(isset($allVulnsAsArray[$HostOrder])  && isset($allVulnsAsArray[$HostOrder]['H']))  $H_vulnOrder[$vuln->Host]=count($allVulnsAsArray[$HostOrder]['H'])+1;
                        else  $H_vulnOrder[$vuln->Host]=1;
                        $H_vulnAsArray = [
                            'h_SRV_host#'. $HostOrder => $vuln->Host,
                            'h_SRV_Risk_Factor_host#'. $HostOrder => $vuln->Risk,
                            'h_SRV_Synopsis#'. $HostOrder => $vuln->synopsis,
                            'h_SRV_vuln_port_host#'. $HostOrder => $vuln->ports,
                            'h_SRV_exploi_host#'. $HostOrder => $vuln->exploitability,
                            'h_SRV_vuln_age_host#'. $HostOrder => $vuln->age_of_vuln,
                            'id_h2_serv#'.$HostOrder=>$H_vulnOrder[$vuln->Host]
                        ];

                        break;
                    case 'Medium':

                        if(isset($allVulnsAsArray[$HostOrder]) && isset($allVulnsAsArray[$HostOrder]['M'])) $M_vulnOrder[$vuln->Host]=count($allVulnsAsArray[$HostOrder]['M'])+1;
                        else  $M_vulnOrder[$vuln->Host]=1;
                        $M_vulnAsArray = [
                            'm_SRV_host#'. $HostOrder => $vuln->Host,
                            'm_SRV_Risk_Factor_host#'. $HostOrder => $vuln->Risk,
                            'm_SRV_Synopsis#'. $HostOrder => $vuln->synopsis,
                            'm_SRV_vuln_port_host#'. $HostOrder => $vuln->ports,
                            'm_SRV_exploi_host#'. $HostOrder => $vuln->exploitability,
                            'm_SRV_vuln_age_host#'. $HostOrder => $vuln->age_of_vuln,
                            'id_m2_serv#'.$HostOrder=>$M_vulnOrder[$vuln->Host]
                        ];

                        break;
                    case 'Low':

                        if(isset($allVulnsAsArray[$HostOrder]) && isset($allVulnsAsArray[$HostOrder]['L']))  $L_vulnOrder[$vuln->Host]=count($allVulnsAsArray[$HostOrder]['L'])+1;
                        else  $L_vulnOrder[$vuln->Host]=1;
                        $L_vulnAsArray = [
                            'l_SRV_host#'. $HostOrder => $vuln->Host,
                            'l_SRV_Risk_Factor_host#'. $HostOrder => $vuln->Risk,
                            'l_SRV_Synopsis#'. $HostOrder => $vuln->synopsis,
                            'l_SRV_vuln_port_host#'. $HostOrder => $vuln->ports,
                            'l_SRV_exploi_host#'. $HostOrder => $vuln->exploitability,
                            'l_SRV_vuln_age_host#'. $HostOrder => $vuln->age_of_vuln,
                            'id_l2_serv#'.$HostOrder=>$L_vulnOrder[$vuln->Host]
                        ];

                        break;

                   // Add more cases if needed
            }
            if (isset($C_vulnAsArray)) $allVulnsAsArray[$HostOrder]['C'][] = $C_vulnAsArray ;
            if (isset($H_vulnAsArray)) $allVulnsAsArray[$HostOrder]['H'][] = $H_vulnAsArray ;
            if (isset($M_vulnAsArray)) $allVulnsAsArray[$HostOrder]['M'][] = $M_vulnAsArray ;
            if (isset($L_vulnAsArray)) $allVulnsAsArray[$HostOrder]['L'][] = $L_vulnAsArray ;

        }


        //     $templateProcessor->setValue('SRV_host#'.$HostOrder,    $allVulnsAsArray[$HostOrder][1]['SRV_host#'. $HostOrder]);
        $templateProcessor->setValue('id_SRV_host#'.$HostOrder,  $HostOrder );
      //  print_r($allVulnsAsArray);        return 0;
        if (isset($allVulnsAsArray[$HostOrder]['C']))
        {
            $templateProcessor->cloneRowAndSetValues('id_c2_serv#'.$HostOrder,      $allVulnsAsArray[$HostOrder]['C']);
            unset($allVulnsAsArray[$HostOrder]['C'][0]);
        }
        if (isset($allVulnsAsArray[$HostOrder]['H']))
        {
            $templateProcessor->cloneRowAndSetValues('id_h2_serv#'.$HostOrder,      $allVulnsAsArray[$HostOrder]['H']);
            unset($allVulnsAsArray[$HostOrder]['H'][0]);
        }
        if (isset($allVulnsAsArray[$HostOrder]['M']))
        {
             $templateProcessor->cloneRowAndSetValues('id_m2_serv#'.$HostOrder,      $allVulnsAsArray[$HostOrder]['M']);
             unset($allVulnsAsArray[$HostOrder]['M'][0]);
        }
        if (isset($allVulnsAsArray[$HostOrder]['L']))
        {
            $templateProcessor->cloneRowAndSetValues('id_l2_serv#'.$HostOrder,      $allVulnsAsArray[$HostOrder]['L']);
            unset($allVulnsAsArray[$HostOrder]['L'][0]);
        }

        $HostOrder++;

    }

   // print_r($allVulnsAsArray);        return 0;
    }


    public function generateWordDocument(Request $request)


    {

        set_time_limit(5000);

        $id = $request->project_id;

        $docxDirectory = public_path('storage');
        $docxs = glob($docxDirectory . '/*.docx'); // Get a list of all PNG files in the directory

        foreach ($docxs as $docx) {
            File::delete($docx); // Delete each docx file
        }
        $docxDirectory = storage_path('app/temp');
        $docxs = glob($docxDirectory . '/*.docx'); // Get a list of all PNG files in the directory

        foreach ($docxs as $docx) {
            File::delete($docx); // Delete each docx file
        }


        $data_serv = DB::table(DB::raw('(SELECT
        vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4 as field4,
        COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`= ?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
    AND vuln.Port NOT IN (SELECT `Ports_List` FROM PortsMapping)
    GROUP BY
    `Host` ,  vuln.Name) t'))
    ->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
        DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
        DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
        DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
        DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
        DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
        DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
        DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
        DB::raw('max(FAILED2) as FAILED2'),
        DB::raw('max(PASSED2) as PASSED2'))
        ->setBindings([$id, $id])
    ->groupBy('hostip')
    ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
    ->get();

    $data_db = DB::table(DB::raw('(SELECT
    vuln.`Host` as Hostip,
    sow.Nom as Nom,
    sow.field4 as field4 ,
    COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
    COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
    COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
    COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
    COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
    COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
    COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
    COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
    COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
    COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'DB\')
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
    DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
    DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
    DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
    DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
    DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
    DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
    DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
    DB::raw('max(FAILED2) as FAILED2'),
    DB::raw('max(PASSED2) as PASSED2'))
    ->setBindings([$id, $id])
->whereRaw('(0,0,0,0,0,0,0) <> (Exp_Malware, Critical_Ex, High_Ex, Medium_Ex, Critical, High, Mediu)')
->groupBy('Hostip', 'Nom', 'field4')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();

$data_rs = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'R_S\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();
$data_pc = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'PC\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();

        $data_ext = DB::table(DB::raw('(SELECT
        vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4 as field4,
        COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
        FROM vuln
        LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
        RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
        WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'EXT\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
        GROUP BY
        `Host` ,  vuln.Name) t'))
        ->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
        DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
        DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
        DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
        DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
        DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
        DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
        DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
        DB::raw('max(FAILED2) as FAILED2'),
        DB::raw('max(PASSED2) as PASSED2'))
        ->setBindings([$id, $id])
        ->groupBy('hostip')
        ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
        ->get();



        $data_apps = DB::table(DB::raw('(SELECT
        vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4 as field4,
        COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Apps\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
    AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'Apps\')
    GROUP BY
    `Host` ,  vuln.Name) t'))
    ->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
        DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
        DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
        DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
        DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
        DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
        DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
        DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
        DB::raw('max(FAILED2) as FAILED2'),
        DB::raw('max(PASSED2) as PASSED2'))
        ->setBindings([$id, $id])
    ->groupBy('Hostip', 'Nom', 'field4')
    ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
    ->get();



        $data_mails = DB::table(DB::raw('(SELECT
        vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4 as field4,
        COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
    AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'Mail\')
    GROUP BY
    `Host` ,  vuln.Name) t'))
    ->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
        DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
        DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
        DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
        DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
        DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
        DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
        DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
        DB::raw('max(FAILED2) as FAILED2'),
        DB::raw('max(PASSED2) as PASSED2'))
        ->setBindings([$id, $id])
    ->groupBy('Hostip', 'Nom', 'field4')
    ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
    ->get();



        $data_voip = DB::table(DB::raw('(SELECT
        vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4 as field4,
        COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
    AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'voip\')
    GROUP BY
    `Host` ,  vuln.Name) t'))
    ->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
        DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
        DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
        DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
        DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
        DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
        DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
        DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
        DB::raw('max(FAILED2) as FAILED2'),
        DB::raw('max(PASSED2) as PASSED2'))
        ->setBindings([$id, $id])
    ->groupBy('Hostip', 'Nom', 'field4')
    ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
    ->get();


        ///////Table2


$data2_serv = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

 $data2_db = DB::table('vuln')
->select('Risk','plugins.synopsis',DB::raw('count(DISTINCT Risk,plugins.synopsis,vuln.Host) As count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'),'exploited_by_malware','exploit_available')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT `ID`from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE Utilisation=\'DB\')')
->groupBy(['Risk','plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();


$data2_rs = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'R_S')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();
$data2_pc = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'PC')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

$data2_ext = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'EXT')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

$data2_apps = DB::table('vuln')
    ->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', $id)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
    ->groupBy(['Risk', 'plugins.synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->get();
$data2_mails = DB::table('vuln')
    ->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', $id)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
    ->groupBy(['Risk', 'plugins.synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->get();



$data2_voip = DB::table('vuln')
    ->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
     DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', $id)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
    ->groupBy(['Risk', 'plugins.synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->get();










        //////////////table3


        $data3_serv = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
        ->whereIn('Risk',['Critical', 'High', 'Medium','Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();

         $data3_db = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping)')
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'DB\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC ')
        ->get();






        $data3_rs = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','R_S')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();

        $data3_pc = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','PC')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)

        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();


        $data3_ext = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','EXT')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();


        $data3_apps = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','Apps')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();



        $data3_mails = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();



        $data3_voip = DB::table('vuln')
         ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
        DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',$id)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->get();

        $data21_serv = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'Serveur')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereNotIn('vuln.Port', function($query) {
            $query->select('Ports_List')
                  ->from('PortsMapping');
        })
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();






         $data21_db = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'Serveur')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('vuln.Port', function($query) {
            $query->select('Ports_List')
                  ->from('PortsMapping')
                  ->where('Utilisation', '=', 'DB');
        })
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

        $data21_pc = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'PC')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

        $data21_ext = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->where('sow.Type', '=', 'EXT')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

        $data21_apps = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'Apps')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('vuln.Port', function($query) {
            $query->select('Ports_List')
                  ->from('PortsMapping')
                  ->where('Utilisation', '=', 'Apps');
        })
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

        $data21_mails = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'Serveur')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('vuln.Port', function($query) {
            $query->select('Ports_List')
                  ->from('PortsMapping')
                  ->where('Utilisation', '=', 'Mail');
        })
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

        $data21_voip = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'Serveur')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('vuln.Port', function($query) {
            $query->select('Ports_List')
                  ->from('PortsMapping')
                  ->where('Utilisation', '=', 'Voip');
        })
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();
        $data21_rs = DB::table('vuln')
        ->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
            DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
            DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
            'plugins.age_of_vuln')
        ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
        ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
        ->whereIn('vuln.upload_id', function($query) use ($id) {
            $query->select('ID')
                  ->from('uploadanomalies')
                  ->where('ID_Projet', '=', $id);
         })
        ->where('sow.Type', '=', 'R_S')
        ->whereColumn('sow.IP_Host', 'vuln.Host')
        ->where('sow.Projet', '=', $id)
        ->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
        ->groupBy('vuln.Host', 'vuln.Name')
        ->orderBy('vuln.Host')
        ->orderByDesc('exploitability')
        ->orderByDesc('Risk')
        ->get();

            // Fetch data from the database based on the received 'project_id'
            $project =Project::find($id);
            $customer =Customer::find($project->customer_id);




           /*  $templatePath2 = public_path('storage/app/file0.docx');
            $templateProcessor2 = new TemplateProcessor($templatePath2);

            $templateProcessor2->setValue('SN',  $customer->SN);
            $templateProcessor2->setValue('LN',  $customer->LN);
            $templateProcessor2->setValue('PRJ',  $project->Nom);
            $templateProcessor2->setValue('Y',  $project->year);
            $templateProcessor2->setValue('URL',  $project->URL);
            $templateProcessor2->setValue('DESC',  $project->description);
            $outputPath2 = storage_path('app/file0.docx');
            $templateProcessor2->saveAs($outputPath2);
 */


        $val_serv = [
            'data1' => [$data_serv],
            'data2' => [$data2_serv],
            'data3' => [$data3_serv],
            'data4' => [$data21_serv],
        ];
     $val_rs = [
            'data1' => [$data_rs],
            'data2' => [$data2_rs],
            'data3' => [$data3_rs],
            'data4' => [$data21_rs],
        ];
        $val_db = [
            'data1' => [$data_db],
            'data2' => [$data2_db],
            'data3' => [$data3_db],
            'data4' => [$data21_db],
        ];
        $val_pc = [
            'data1' => [$data_pc],
            'data2' => [$data2_pc],
            'data3' => [$data3_pc],
            'data4' => [$data21_pc],
        ];
        $val_ext = [
            'data1' => [$data_ext],
            'data2' => [$data2_ext],
            'data3' => [$data3_ext],
            'data4' => [$data21_pc],
        ];
        $val_apps = [
            'data1' => [$data_apps],
            'data2' => [$data2_apps],
            'data3' => [$data3_apps],
            'data4' => [$data21_apps],
        ];
        $val_voip = [
            'data1' => [$data_voip],
            'data2' => [$data2_voip],
            'data3' => [$data3_voip],
            'data4' => [$data21_voip],
        ];
        $val_mails = [
            'data1' => [$data_mails],
            'data2' => [$data2_mails],
            'data3' => [$data3_mails],
            'data4' => [$data21_mails],
        ];

        $all = [$val_serv ,$val_rs, $val_db, $val_pc, $val_ext, $val_apps, $val_voip, $val_mails ];
        $all = [$val_serv  ];


        $v = 1;
        foreach ($all as $it) {




            $templatePath1 = public_path('2.docx');
            $templateProcessor1 = new TemplateProcessor($templatePath1);
            $dataLength = $it['data3'][0]->count();
            $templateProcessor1->cloneBlock('Block_Vun', $dataLength, true, true);
            $outputPath1 = public_path('storage/template' . $v . '.docx');
            $templateProcessor1->saveAs($outputPath1);
             $templatePath = public_path('storage/template' . $v . '.docx');



            $templateProcessor = new TemplateProcessor($templatePath);


           if ($v<9) {
            $templateProcessor->setValue('SRV_TITLE', self::$AnnexesTitles[$v]);
            $templateProcessor->setValue('SRV_LETTER', self::$AnnexesLetters[$v]);
           }




$imageData = file_get_contents($customer->Logo);
$localImagePath = public_path('images/'.basename($customer->Logo)); // Specify the local path to save the image
file_put_contents($localImagePath, $imageData);
$templateProcessor->setImageValue('icon', $localImagePath);
$templateProcessor->setValue('SN',  $customer->SN);
$templateProcessor->setValue('LN',  $customer->LN);
$templateProcessor->setValue('PRJ',  $project->Nom);
$templateProcessor->setValue('Y',  $project->year);
$templateProcessor->setValue('URL',  $project->URL);
$templateProcessor->setValue('DESC',  $project->description);

            $n_serv = 0;
            $c_serv = 0;
            $h_serv = 0;
            $m_serv = 0;
            $cex_serv = 0;
            $hex_serv = 0;
            $mex_serv = 0;
            $f_serv = 0;
            $nc_serv = 0;
            $cc_serv = 0;
            $mlw_serv = 0;
            $values_serv=[];
            foreach ($it['data1'] as $item) {
           foreach ($item as $item2)
           {
                $n_serv++;

                $values_serv[] = [
                'id' => $n_serv,
                'SRV_Name' => $item2->Nom,
                'SRV_IP' => $item2->Hostip,
                'SRV_OS' => $item2->field4,
                'SRV_MLW' => $item2->Exp_Malware,
                'SRV_CR' => $item2->Critical,
                'SRV_HI' => $item2->High,
                'SRV_ME' => $item2->Mediu,
                'SRV_LO' => $item2->Low,
                'SRV_NC' => $item2->FAILED2,
                'SRV_CF' => $item2->PASSED2,
                'SRV_CR_Exp' => $item2->Critical_Ex,
                'SRV_HI_Exp' => $item2->High_Ex,
                'SRV_MO_Exp' => $item2->Medium_Ex
                ];
            $c_serv = $c_serv + $item2->Critical;
            $mlw_serv = $mlw_serv + $item2->Exp_Malware;
            $h_serv = $h_serv + $item2->High;
            $m_serv = $m_serv + $item2->Mediu;
            $cex_serv = $cex_serv + $item2->Critical_Ex;
            $hex_serv = $hex_serv + $item2->High_Ex;
            $mex_serv = $mex_serv + $item2->Medium_Ex;
            $f_serv = $f_serv + $item2->Low;
            $nc_serv = $nc_serv + $item2->FAILED2;
            $cc_serv = $cc_serv + $item2->PASSED2;
                }
            }
            $templateProcessor->setValue('TLT_SRV', $n_serv);
            $templateProcessor->setValue('TLT_SRV_MLW', $mlw_serv);
            $templateProcessor->setValue('TLT_SRV_CR', $c_serv);
            $templateProcessor->setValue('TLT_SRV_HI', $h_serv);
            $templateProcessor->setValue('TLT_SRV_ME', $m_serv);
            $templateProcessor->setValue('TLT_SRV_LO', $f_serv);
            $templateProcessor->setValue('TLT_SRV_NC', $nc_serv);
            $templateProcessor->setValue('TLT_SRV_CF', $cc_serv);
            $templateProcessor->setValue('TLT_SRV_CR_Ex', $cex_serv);
            $templateProcessor->setValue('TLT_SRV_HI_Ex', $hex_serv);
            $templateProcessor->setValue('TLT_SRV_MO_Ex', $mex_serv);
        ////////

            $highRisk_serv = [];
            $criticalRisk_serv = [];
            $mediumRisk_serv = [];
            $lowRisk_serv = [];

            foreach ($it['data2'] as $item) {
                foreach ($item as $item2) {

                    switch ($item2->Risk) { // Access object property using -> notation
                        case 'High':
                            $highRisk_serv[] = $item2;
                            break;
                        case 'Critical':
                            $criticalRisk_serv[] = $item2;
                            break;
                        case 'Medium':
                            $mediumRisk_serv[] = $item2;
                            break;
                        case 'Low':
                            $lowRisk_serv[] = $item2;
                            break;
                        // Add more cases if needed
                    }
                }
            }
            $n2_c_serv = 0;
            $values2_c_serv = [];

            foreach ($criticalRisk_serv as $item) {
                $n2_c_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'true') {
                    $exp = 'exploité par malware';
                } else if ($item->exploit_available === 'true') {
                    $exp = 'exploit available ';
                }
                $itemValues = [
                    'id_c_serv' => $n2_c_serv,
                    'SRV_Risk_Factor_Critical' => $item->Risk,
                    'SRV_Synopsis_Critical' => $item->synopsis,
                    'SRV_count' => $item->count,
                    'SRV_exploi' => $exp,
                    'SRV_nbr_Critical' => Str::limit($item->nbr, 50, '...'),
                ];


                $values2_c_serv[] = $itemValues;
            }
            $n2_h_serv = $n2_c_serv ;
            $values2_h_serv = [];

            foreach ($highRisk_serv as $item) {
                $n2_h_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'vrai') {
                    $exp = 'exploité par malware';
                } else if ($item->exploit_available === 'vrai') {
                    $exp = 'exploit available ';
                }
                $itemValues = [
                    'id_h_serv' => $n2_h_serv,
                    'SRV_Risk_Factor_High' => $item->Risk,
                    'SRV_Synopsis_High' => $item->synopsis,
                    'SRV_count' => $item->count,
                    'SRV_exploi' => $exp,
                    'SRV_nbr_High' => Str::limit($item->nbr, 50, '...'),
                ];


                $values2_h_serv[] = $itemValues;
            }


            $n2_m_serv = $n2_h_serv;
            $values2_m_serv = [];

            foreach ($mediumRisk_serv as $item) {
                $n2_m_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'vrai') {
                    $exp = 'exploité par malware';
                } else if ($item->exploit_available === 'vrai') {
                    $exp = 'exploit available ';
                }
                $itemValues = [
                    'id_m_serv' => $n2_m_serv,
                    'SRV_Risk_Factor_Medium' => $item->Risk,
                    'SRV_Synopsis_Medium' => $item->synopsis,
                    'SRV_count' => $item->count,
                    'SRV_exploi' => $exp,
                    'SRV_nbr_Medium' => Str::limit($item->nbr, 50, '...'),
                ];



                $values2_m_serv[] = $itemValues;
            }


            $n2_l_serv = $n2_m_serv;
            $values2_l_serv = [];

            foreach ($lowRisk_serv as $item) {
                $n2_l_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'vrai') {
                    $exp = 'exploité par malware';
                } else if ($item->exploit_available === 'vrai') {
                    $exp = 'exploit available ';
                }
                $itemValues = [
                    'id_l_serv' => $n2_l_serv,
                    'SRV_Risk_Factor_Low' => $item->Risk,
                    'SRV_Synopsis_Low' => $item->synopsis,
                    'SRV_count' => $item->count,
                    'SRV_exploi' => $exp,
                    'SRV_nbr_Low' => Str::limit($item->nbr, 50, '...'),
                ];


                $values2_l_serv[] = $itemValues;
            }

            $templateProcessor->cloneRowAndSetValues('id', $values_serv);
            $templateProcessor->cloneRowAndSetValues('id_h_serv', $values2_h_serv);
            $templateProcessor->cloneRowAndSetValues('id_c_serv', $values2_c_serv);
            $templateProcessor->cloneRowAndSetValues('id_m_serv', $values2_m_serv);
            $templateProcessor->cloneRowAndSetValues('id_l_serv', $values2_l_serv);



            $trieCounter = 1;
            $m = 1;
            foreach ($it['data3'] as $item2)
            {


            $data3 = $item2->sortByDesc('Score');
            foreach ($data3 as $item3) {

                $trieValue = str_pad($trieCounter, 3, '0', STR_PAD_LEFT);




                $trieCounter++;
                $templateProcessor->setValue('SRV_VULN_ID' . '#' . $m, htmlspecialchars($trieValue));
                $templateProcessor->setValue('SRV_VULN_RISK' . '#' . $m, htmlspecialchars($item3->Risk));
                $templateProcessor->setValue('SRV_VULN_CVSS' . '#' . $m, $item3->Score);
                $templateProcessor->setValue('SRV_VULN_pluginID' . '#' . $m, htmlspecialchars($item3->Plugin_Id));
                $templateProcessor->setValue('SRV_VULN_PluginOutput' . '#' . $m, self::cleanNewLineProblem($item3->Plugin_Output,0));
                $templateProcessor->setValue('SRV_VULN_Hosts_ports' . '#' . $m, htmlspecialchars($item3->Port));
                $templateProcessor->setValue('SRV_VULN_Age' . '#' . $m, htmlspecialchars($item3->age_of_vuln));
                $templateProcessor->setValue('SRV_VULN_Synopsis' . '#' . $m, htmlspecialchars($item3->synopsis));
                $templateProcessor->setValue('SRV_VULN_Name' . '#' . $m, htmlspecialchars($item3->Name));
                $templateProcessor->setValue('SRV_VULN_Hosts' . '#' . $m, htmlspecialchars($item3->Elt_Impactes));
                $templateProcessor->setValue('SRV_VULN_Metasploit' . '#' . $m, !empty($item3->exploit_framework_metasploit) ? htmlspecialchars($item3->exploit_framework_metasploit) : 'N/A');
                $templateProcessor->setValue('SRV_VULN_Core_Impact' . '#' . $m, !empty($item3->exploit_framework_core) ? htmlspecialchars($item3->exploit_framework_core) : 'N/A');
                $templateProcessor->setValue('SRV_VULN_CANVAS' . '#' . $m, !empty($item3->exploit_framework_canvas) ? htmlspecialchars($item3->exploit_framework_canvas) : 'N/A');
                $templateProcessor->setValue('SRV_VULN_Desc' . '#' . $m, self::cleanNewLineProblem($item3->description,0));
                $templateProcessor->setValue('SRV_VULN_ref' . '#' . $m, self::cleanNewLineProblem($item3->See, 1));
                $templateProcessor->setValue('SRV_VULN_Recomendations' . '#' . $m, self::cleanNewLineProblem($item3->solution, 0));
                $templateProcessor->setValue('SRV_VULN_available' . '#' . $m, !empty($item3->exploit_available) ? htmlspecialchars($item3->exploit_available) : 'N/A');
                $templateProcessor->setValue('SRV_VULN_malware' . '#' . $m, !empty($item3->exploited_by_malware) ? htmlspecialchars($item3->exploited_by_malware) : 'N/A');
                $templateProcessor->setValue('SRV_VULN_age_of_vuln' . '#' . $m, !empty($item3->age_of_vuln) ? htmlspecialchars($item3->age_of_vuln) : 'N/A');


                $m++;

            }
        }

        comehere:
        self::generateVulnPerHostPart($it, $templateProcessor);






            $outputFileName = 'tchRpt_Annx_' . self::$AnnexesLetters[$v] ."_".self::$AnnexesTitles[$v]."_".$customer->SN. '.docx';
            $outputPath = public_path('storage/' . $outputFileName);
            $templateProcessor->saveAs($outputPath);
            $filePaths [$v-1] = public_path('storage/'.$outputFileName);
            $v++;
        }








 // Create a unique temporary directory to store the zip file
 $tempDirectory = storage_path('app/temp');
 // File paths for the two files you want to download

 // Create a zip archive


 $zip = new ZipArchive;
 $zipFileName =  "TechReport".$project->Nom ."-". $customer->SN.'.zip';
 $zipFilePath = $tempDirectory . '/' . $zipFileName;

 if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
     // Add the files to the zip archive
     foreach ($filePaths as $filePath) {
         $file = basename($filePath);
         $zip->addFile($filePath, $file);
     }

     $zip->close();

     // Download the zip archive
     return response()->download($zipFilePath)->deleteFileAfterSend();
 }

 // If zip creation fails, return an error response
 return response()->json(['error' => 'Failed to create zip archive'], 500);

        /* return response()->download(storage_path('app/merge.docx'))->deleteFileAfterSend();  */
    }

   }
