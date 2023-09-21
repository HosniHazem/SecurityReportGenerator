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

class WordDocumentController extends Controller
{


  /*   function getVulnerabilityData($id, $type, $portUtilization = null) {
        $query = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', $type);
    
        if (!is_null($portUtilization)) {
            $query->whereIn('vuln.Port', function ($query) use ($portUtilization) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', $portUtilization);
            });
        } else {
            $query->whereNotIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping');
            });
        }
    
        $query->select('sow.Nom', 'sow.IP_Host', 'sow.field4 as field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4 as field4');
    
        return $query->get();
    } 
 */
       /*  $data_serv = getVulnerabilityData($id, 'Serveur');
        $data_db = getVulnerabilityData($id, 'Serveur', 'DB');
        $data_pc = getVulnerabilityData($id, 'PC');
        $data_ext = getVulnerabilityData($id, 'EXT');
        $data_apps = getVulnerabilityData($id, 'Serveur', 'Apps');
        $data_mails = getVulnerabilityData($id, 'Serveur', 'Mail');
        $data_voip = getVulnerabilityData($id, 'Serveur', 'Voip');
 */

    public function generateWordDocument(Request $request)


    {


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
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
->whereRaw('(0,0,0,0,0,0,0) <> (Exp_Malware, Critical_Ex, High_Ex, Medium_Ex, Critical, High, Mediu)')
->groupBy('Hostip', 'Nom', 'field4')
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
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'PC\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
        WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'EXT\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'Apps\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
    WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=2) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=2
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
    ->groupBy('Hostip', 'Nom', 'field4')
    ->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
    ->get();


        ///////Table2


$data2_serv = DB::table('vuln')
->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
->where('sow.Type', '=', 'Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', 2)
->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
->whereIn('Risk', ['Critical', 'High', 'Medium'])
->groupBy(['Risk', 'vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->limit(20)
->get();

$data2_db = DB::table('vuln')
->select('Risk','vuln.Synopsis',DB::raw('count(DISTINCT Risk,vuln.Synopsis,vuln.Host) As Num'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'),'exploited_by_malware','exploit_available')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT `ID`from uploadanomalies WHERE ID_Projet = 2)')
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',2)
->whereIn('Risk',['Critical', 'High', 'Medium'])
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE Utilisation=\'DB\')')
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->limit(20)
->get();


$data2_pc = DB::table('vuln')
->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'), 
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
->where('sow.Type', '=', 'PC')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', 2)
->whereIn('Risk', ['Critical', 'High', 'Medium'])
->groupBy(['Risk', 'vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->limit(20)
->get();

$data2_ext = DB::table('vuln')
->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'), 
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
->where('sow.Type', '=', 'EXT')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', 2)
->whereIn('Risk', ['Critical', 'High', 'Medium'])
->groupBy(['Risk', 'vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->limit(20)
->get();

$data2_apps = DB::table('vuln')
    ->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', 2)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium'])
    ->groupBy(['Risk', 'vuln.Synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->limit(20)
    ->get();
$data2_mails = DB::table('vuln')
    ->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', 2)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium'])
    ->groupBy(['Risk', 'vuln.Synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->limit(20)
    ->get();



$data2_voip = DB::table('vuln')
    ->select('Risk', 'vuln.Synopsis', DB::raw('count(DISTINCT Risk, vuln.Synopsis, vuln.Host) as count'),
     DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
    ->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
    ->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
    ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = 2)')
    ->where('sow.Type', '=', 'Apps')
    ->whereRaw('sow.IP_Host = vuln.Host')
    ->where('sow.Projet', '=', 2)
    ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
    ->whereIn('Risk', ['Critical', 'High', 'Medium'])
    ->groupBy(['Risk', 'vuln.Synopsis'])
    ->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
    ->limit(20)
    ->get();



        
        
        
    



        //////////////table3


        $data3_serv = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();

        $data3_db = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping)')
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'DB\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC ')
        ->limit(20)
        ->get();

        
       

    

        $data3_pc = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','PC')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)

        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();
        

        $data3_ext = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','EXT')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();


        $data3_apps = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','Apps')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();
       
       

        $data3_mails = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();

        

        $data3_voip = DB::table('vuln')
        ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score',
        DB::raw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes'),
        DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
        ->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
        ->rightJoin('sow','vuln.Host','=','sow.IP_Host')
        ->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=2)')
        ->where('sow.Type','=','Serveur')
        ->whereRaw('sow.IP_Host = vuln.Host')
        ->where('sow.Projet','=',2)
        ->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
        ->whereIn('Risk',['Critical', 'High', 'Medium'])
        ->groupBy(['Risk','vuln.Synopsis'])
        ->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
        ->limit(20)
        ->get();
  
            // Fetch data from the database based on the received 'project_id'
            $project =Project::find($id);
            $customer =Customer::find($project->customer_id);


            $templatePath2 = public_path('storage/app/file0.docx');
            $templateProcessor2 = new TemplateProcessor($templatePath2);
            $imageData = file_get_contents($customer->Logo);
            $localImagePath = public_path('images/logo.png'); // Specify the local path to save the image
            file_put_contents($localImagePath, $imageData);
            $templateProcessor2->setImageValue('icon', $localImagePath);
            $templateProcessor2->setValue('SN',  $customer->SN);
            $templateProcessor2->setValue('LN',  $customer->LN);
            $templateProcessor2->setValue('PRJ',  $project->Nom);
            $templateProcessor2->setValue('Y',  $project->year);
            $templateProcessor2->setValue('URL',  $project->URL);
            $templateProcessor2->setValue('DESC',  $project->description);
            $outputPath2 = storage_path('app/file0.docx');
            $templateProcessor2->saveAs($outputPath2);



        $val_serv = [
            'data1' => [$data_serv],
            'data2' => [$data2_serv],
            'data3' => [$data3_serv],
        ];
        $val_db = [
            'data1' => [$data_db],
            'data2' => [$data2_db],
            'data3' => [$data3_db],
        ];
        $val_pc = [
            'data1' => [$data_pc],
            'data2' => [$data2_pc],
            'data3' => [$data3_pc],
        ];
        $val_ext = [
            'data1' => [$data_ext],
            'data2' => [$data2_ext],
            'data3' => [$data3_ext],
        ];
        $val_apps = [
            'data1' => [$data_apps],
            'data2' => [$data2_apps],
            'data3' => [$data3_apps],
        ];
        $val_voip = [
            'data1' => [$data_voip],
            'data2' => [$data2_voip],
            'data3' => [$data3_voip],
        ];
        $val_mails = [
            'data1' => [$data_mails],
            'data2' => [$data2_mails],
            'data3' => [$data3_mails],
        ];

        $all = [$val_serv, $val_db, $val_pc, $val_ext, $val_apps, $val_voip, $val_mails];

        $v = 1;
        foreach ($all as $it) {

if($v===1){
    $templateProcessor->setValue('SRV_TITLE', "Serveur");  
}else if($v===2){
    $templateProcessor->setValue('SRV_TITLE', "DB");  
}else if($v===3){
    $templateProcessor->setValue('SRV_TITLE', "PC");  
}else if($v===4){
    $templateProcessor->setValue('SRV_TITLE', "EXT");  
}else if($v===5){
    $templateProcessor->setValue('SRV_TITLE', "APPS");  
}else if($v===6){
    $templateProcessor->setValue('SRV_TITLE', "VOIP");  
}else if($v===7){
    $templateProcessor->setValue('SRV_TITLE', "MAILS");  
}




            $templatePath1 = public_path('1.docx');
            $templateProcessor1 = new TemplateProcessor($templatePath1);
            $dataLength = $it['data3'][0]->count();
            $templateProcessor1->cloneBlock('Block_Vun', $dataLength, true, true);
            $outputPath1 = public_path('storage/template' . $v . '.docx');
            $templateProcessor1->saveAs($outputPath1);
             $templatePath = public_path('storage/template' . $v . '.docx');



            $templateProcessor = new TemplateProcessor($templatePath);
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

            ////////////

            $highRisk_serv = [];
            $criticalRisk_serv = [];
            $mediumRisk_serv = [];
            
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
                        // Add more cases if needed
                    }
                }
            }
            
            $n2_h_serv = 0;
            $values2_h_serv = [];
            
            foreach ($highRisk_serv as $item) {
                $n2_h_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'true') {
                    $exp = 'exploité by malware';
                } else if ($item->exploit_available === 'true') {
                    $exp = 'exploit available ';
                }
                $values2_h_serv[] = [
                    'id_h_serv' => $n2_h_serv,
                    'SRV_Risk_Factor_High' => $item->Risk,
                    'SRV_Synopsis_High' => $item->Synopsis,
                
                    'SRV_exploi' => $exp,
                    'SRV_nbr_High' => Str::limit($item->nbr, 50, '...'),
                ];
                if (isset($item->count)) {
                    $itemArray['SRV_count_High'] = $item->count;
                }else {
                    $itemArray['SRV_count_High'] = "";
                }
            }
          
            
            $n2_m_serv = $n2_h_serv;
            $values2_m_serv = [];
            
            foreach ($mediumRisk_serv as $item) {
                $n2_m_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'true') {
                    $exp = 'exploité by malware';
                } else if ($item->exploit_available === 'true') {
                    $exp = 'exploit available ';
                }
                $values2_m_serv[] = [
                    'id_m_serv' => $n2_m_serv,
                    'SRV_Risk_Factor_Medium' => $item->Risk,
                    'SRV_Synopsis_Medium' => $item->Synopsis,
                   
                    'SRV_exploi' => $exp,
                    'SRV_nbr_Medium' => Str::limit($item->nbr, 50, '...'),
                ];
                if (isset($item->count)) {
                    $itemArray['SRV_count_Medium'] = $item->count;
                }else {
                    $itemArray['SRV_count_Medium'] = "";
                }
            }
          
            $n2_c_serv = $n2_m_serv;
            $values2_c_serv = [];
            
            foreach ($criticalRisk_serv as $item) {
                $n2_c_serv++;
                $exp = '';
                if ($item->exploited_by_malware === 'true') {
                    $exp = 'exploité by malware';
                } else if ($item->exploit_available === 'true') {
                    $exp = 'exploit available ';
                }
                $values2_c_serv[] = [
                    'id_c_serv' => $n2_c_serv,
                    'SRV_Risk_Factor_Critical' => $item->Risk,
                    'SRV_Synopsis_Critical' => $item->Synopsis,
                    'SRV_exploi' => $exp,
                    'SRV_nbr_Critical' => Str::limit($item->nbr, 50, '...'),
                ];
                if (isset($item->count)) {
                    $itemArray['SRV_count_Critical'] = $item->count;
                }else {
                    $itemArray['SRV_count_Critical'] = "";
                }
            }

            $templateProcessor->cloneRowAndSetValues('id', $values_serv);
            $templateProcessor->cloneRowAndSetValues('id_h_serv', $values2_h_serv);
            $templateProcessor->cloneRowAndSetValues('id_c_serv', $values2_c_serv);
            $templateProcessor->cloneRowAndSetValues('id_m_serv', $values2_m_serv);



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
                $templateProcessor->setValue('SRV_VULN_Synopsis' . '#' . $m, htmlspecialchars($item3->synopsis));
                $templateProcessor->setValue('SRV_VULN_Name' . '#' . $m, htmlspecialchars($item3->Name));
                $templateProcessor->setValue('SRV_VULN_Hosts' . '#' . $m, htmlspecialchars($item3->Elt_Impactes));
                $templateProcessor->setValue('SRV_VULN_Metasploit' . '#' . $m, htmlspecialchars($item3->exploit_framework_metasploit));
                $templateProcessor->setValue('SRV_VULN_Core_Impact' . '#' . $m, htmlspecialchars($item3->exploit_framework_core));
                $templateProcessor->setValue('SRV_VULN_CANVAS' . '#' . $m, htmlspecialchars($item3->exploit_framework_canvas));
                $templateProcessor->setValue('SRV_VULN_Desc' . '#' . $m, htmlspecialchars($item3->description));
                $templateProcessor->setValue('SRV_VULN_ref' . '#' . $m, htmlspecialchars($item3->See));
                $templateProcessor->setValue('SRV_VULN_Recomendations' . '#' . $m, htmlspecialchars($item3->solution));
                $templateProcessor->setValue('SRV_VULN_available' . '#' . $m, htmlspecialchars($item3->exploit_available));
                $templateProcessor->setValue('SRV_VULN_malware' . '#' . $m, htmlspecialchars($item3->exploited_by_malware));
                $templateProcessor->setValue('SRV_VULN_age_of_vuln' . '#' . $m, htmlspecialchars($item3->age_of_vuln));

                $m++;

            }
        }

            $outputFileName = 'file' . $v . '.docx';
            $outputPath = public_path('storage/' . $outputFileName);
            $templateProcessor->saveAs($outputPath);
            $v++;
        }





        $filePath = public_path('/storage/');
        $filesName = ['file1.docx', 'file2.docx', 'file3.docx', 'file4.docx', 'file5.docx', 'file6.docx', 'file7.docx']; // Replace with your file names

        $zip = new clsTbsZip();
        $content = [];
        $r = '';

        // Iterate through the DOCX files
        for ($i = 1; $i < count($filesName); $i++) {
            // Open the document
            $zip->Open($filePath . $filesName[$i]);
            $content[$i] = $zip->FileRead('word/document.xml');
            $zip->Close();

            // Extract the content of the document
            $p = strpos($content[$i], '<w:body');
            if ($p === false) {
                echo ("Tag <w:body> not found in document ." . $filesName[$i]);
            }
            $p = strpos($content[$i], '>', $p);
            $content[$i] = substr($content[$i], $p + 1);
            $p = strpos($content[$i], '</w:body>');
            if ($p === false) {
                echo ("Tag <w:body> not found in document ." . $filesName[$i]);
            }
            $content[$i] = substr($content[$i], 0, $p);
            $r .= $content[$i];
        }

        // Insert after the first document
        $zip->Open($filePath . $filesName[0]);
        $content2 = $zip->FileRead('word/document.xml');
        $p = strpos($content2, '</w:body>');
        if ($p === false) {
            echo ("Tag <w:body> not found in document ." . $filesName[0]);
        }
        $content2 = substr_replace($content2, $r, $p, 0);
        $zip->FileReplace('word/document.xml', $content2, TBSZIP_STRING);

        // Save the merged DOCX as 'merge.docx' in the storage folder
        $zip->Flush(TBSZIP_FILE, storage_path('app/merge.docx'));





 // Create a unique temporary directory to store the zip file
 $tempDirectory = storage_path('app/temp');

 // File paths for the two files you want to download
 $filePaths = [
     storage_path('app/merge.docx'),
     storage_path('app/file0.docx'),
 ];

 // Create a zip archive
 $zip = new ZipArchive;
 $zipFileName = 'downloaded_files.zip';
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
