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
    
        $query->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4');
    
        return $query->get();
    } 
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

        $subquery = DB::table('vuln AS vuln')
        ->select('vuln.`Host` as Hostip',
                 'COUNT(IF( `exploited_by_malware` = `true` , 1, NULL)) AS Exp_Malware',
                 'COUNT(IF(vuln.`Risk` = `Critical` AND ( `exploit_available` = `true` ), 1, NULL)) AS Critical_Ex',
                 'COUNT(IF(vuln.`Risk` = `High` AND ( `exploit_available` = `true` ), 1, NULL)) AS High_Ex',
                 'COUNT(IF(vuln.`Risk` = `Medium` AND ( `exploit_available` = `true` ), 1, NULL)) AS Medium_Ex',
                 'COUNT(IF(vuln.`Risk` = `Critical`, 1, NULL)) AS Critical',
                 'COUNT(IF(vuln.`Risk` = `High`, 1, NULL)) AS High',
                 'COUNT(IF(vuln.`Risk` = `Medium`, 1, NULL)) AS Mediu',
                 'COUNT(IF(vuln.`Risk` = `Low`, 1, NULL)) AS Low',
                 'COUNT(IF(vuln.`Risk` = `FAILED`, 1, NULL)) AS FAILED2',
                 'COUNT(IF(vuln.`Risk` = `PASSED`, 1, NULL)) AS PASSED2')
        ->leftJoin('plugins', 'vuln.`Plugin ID`', '=', 'plugins.id')
        ->whereIn('vuln.upload_id', function($q) {
            $q->select('ID')->from('uploadanomalies')->where('ID_Projet', 2);  
        })
        ->whereNotIn('vuln.Port', function($q) {
            $q->select('Ports_List')->from('PortsMapping');
        })
        ->groupBy('vuln.Host');
    
    $data_serv = DB::table(DB::raw("(
        SELECT Hostip, 
            COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware,
            COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex, 
            COUNT(IF(High_Ex>0,1,NULL)) as High_Ex,
            COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex, 
            COUNT(IF(Critical>0,1,NULL)) as Critical,
            COUNT(IF(High>0,1,NULL)) as High, 
            COUNT(IF(Mediu>0,1,NULL)) as Mediu,
            MAX(FAILED2) as FAILED2, 
            MAX(PASSED2) as PASSED2 
        FROM (
            SELECT 
                vuln.`Host` as Hostip,
                COUNT(IF( `exploited_by_malware` = 'true' , 1, NULL)) AS Exp_Malware, 
                COUNT(IF(vuln.`Risk` = 'Critical' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Critical_Ex,
                COUNT(IF(vuln.`Risk` = 'High' AND ( `exploit_available` = 'true' ), 1, NULL)) AS High_Ex,
                COUNT(IF(vuln.`Risk` = 'Medium' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Medium_Ex,
                COUNT(IF(vuln.`Risk` = 'Critical', 1, NULL)) AS Critical,
                COUNT(IF(vuln.`Risk` = 'High', 1, NULL)) AS High,
                COUNT(IF(vuln.`Risk` = 'Medium', 1, NULL)) AS Mediu,
                COUNT(IF(vuln.`Risk` = 'Low', 1, NULL)) AS Low,
                COUNT(IF(vuln.`Risk` = 'FAILED', 1, NULL)) AS FAILED2, 
                COUNT(IF(vuln.`Risk` = 'PASSED', 1, NULL)) AS PASSED2
            FROM vuln
            LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id 
            WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet` = 2)
            AND vuln.Port NOT IN (SELECT `Ports_List` FROM PortsMapping) 
            GROUP BY `Host`, vuln.Name
        ) t
        GROUP BY Hostip
    ) subquery"))
        ->select('Hostip', 'Exp_Malware', 'Critical_Ex', 'High_Ex', 'Medium_Ex', 'Critical', 'High', 'Mediu', 'FAILED2', 'PASSED2')
        ->get();
    return $data_serv;

       /*  $data_serv = getVulnerabilityData($id, 'Serveur');
        $data_db = getVulnerabilityData($id, 'Serveur', 'DB');
        $data_pc = getVulnerabilityData($id, 'PC');
        $data_ext = getVulnerabilityData($id, 'EXT');
        $data_apps = getVulnerabilityData($id, 'Serveur', 'Apps');
        $data_mails = getVulnerabilityData($id, 'Serveur', 'Mail');
        $data_voip = getVulnerabilityData($id, 'Serveur', 'Voip');
 */


        $data_db = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'DB');
            })
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();
        /*
            SELECT
  sow.Nom,
  sow.IP_Host,
  sow.field4,
  COUNT(IF(vuln.Risk_Factor = 'Critical' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS Critical_Ex,
  COUNT(IF(vuln.Risk_Factor = 'High' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS High_Ex,
  COUNT(IF(vuln.Risk_Factor = 'Medium' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS Medium_Ex,
  COUNT(IF(vuln.Risk_Factor = 'Critical', 1, NULL)) AS Critical,
  COUNT(IF(vuln.Risk_Factor = 'High', 1, NULL)) AS High,
  COUNT(IF(vuln.Risk_Factor = 'Medium', 1, NULL)) AS Medium,
  COUNT(IF(vuln.Risk_Factor = 'Low', 1, NULL)) AS Low,
  COUNT(IF(vuln.Risk_Factor = 'WARNING', 1, NULL)) WARNING,
  COUNT(IF(vuln.Risk_Factor = 'PASSED', 1, NULL)) PASSED
FROM sow
INNER JOIN vuln ON vuln.Host = sow.IP_Host
WHERE sow.Projet = 1
  AND sow.Type = 'PC'
GROUP BY
  sow.Nom,
  sow.IP_Host,
  sow.field4;
  */
        $data_pc = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'PC')
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();

        /*
            SELECT
  sow.Nom,
  sow.IP_Host,
  sow.field4,
  COUNT(IF(vuln.Risk_Factor = 'Critical' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS Critical_Ex,
  COUNT(IF(vuln.Risk_Factor = 'High' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS High_Ex,
  COUNT(IF(vuln.Risk_Factor = 'Medium' AND (vuln.Metasploit = 'true' OR vuln.Core_Impact = 'true' OR vuln.CANVAS = 'true'), 1, NULL)) AS Medium_Ex,
  COUNT(IF(vuln.Risk_Factor = 'Critical', 1, NULL)) AS Critical,
  COUNT(IF(vuln.Risk_Factor = 'High', 1, NULL)) AS High,
  COUNT(IF(vuln.Risk_Factor = 'Medium', 1, NULL)) AS Medium,
  COUNT(IF(vuln.Risk_Factor = 'Low', 1, NULL)) AS Low,
  COUNT(IF(vuln.Risk_Factor = 'WARNING', 1, NULL)) WARNING,
  COUNT(IF(vuln.Risk_Factor = 'PASSED', 1, NULL)) PASSED
FROM sow
INNER JOIN vuln ON vuln.Host = sow.IP_Host
WHERE sow.Projet = 1
  AND sow.Type = 'EXT'
GROUP BY
  sow.Nom,
  sow.IP_Host,
  sow.field4;
  */
        $data_ext = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'EXT')
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();

        /*           SELECT `Nom`,`IP_Host`,`field4`,
            COUNT(IF(`Risk_Factor` = 'Critical', 1, NULL)) 'Critical',COUNT(IF(`Risk_Factor` = 'High', 1, NULL)) 'High',
            COUNT(IF(`Risk_Factor` = 'Meduim', 1, NULL)) 'Medium',  COUNT(IF(`Risk_Factor` = 'Low', 1, NULL)) 'Low' ,
            COUNT(IF(`Risk_Factor` = 'WARNING', 1, NULL)) 'WARNING', COUNT(IF(`Risk_Factor` = 'PASSED', 1, NULL)) 'PASSED'

            FROM sow

            LEFT JOIN vuln on vuln.Host = sow.`IP_Host`
            WHERE sow.Projet=1 AND vuln.Host = sow.`IP_Host` and
            sow.Type='Serveur' and vuln.Port in (select `Ports_List` from PortsMapping WHERE Utilisation='Apps') group by `Nom`,`IP_Host`,`field4`; */


        $data_apps = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Apps');
            })
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();



        /*           SELECT `Nom`,`IP_Host`,`field4`,
            COUNT(IF(`Risk_Factor` = 'Critical', 1, NULL)) 'Critical',COUNT(IF(`Risk_Factor` = 'High', 1, NULL)) 'High',
            COUNT(IF(`Risk_Factor` = 'Meduim', 1, NULL)) 'Medium',  COUNT(IF(`Risk_Factor` = 'Low', 1, NULL)) 'Low' ,
            COUNT(IF(`Risk_Factor` = 'WARNING', 1, NULL)) 'WARNING', COUNT(IF(`Risk_Factor` = 'PASSED', 1, NULL)) 'PASSED'

            FROM sow

            LEFT JOIN vuln on vuln.Host = sow.`IP_Host`
            WHERE sow.Projet=1 AND vuln.Host = sow.`IP_Host` and
            sow.Type='Serveur' and vuln.Port in (select `Ports_List` from PortsMapping WHERE Utilisation='Mail') group by `Nom`,`IP_Host`,`field4`; */
        $data_mails = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Mail');
            })
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();


        /*           SELECT `Nom`,`IP_Host`,`field4`,
            COUNT(IF(`Risk_Factor` = 'Critical', 1, NULL)) 'Critical',COUNT(IF(`Risk_Factor` = 'High', 1, NULL)) 'High',
            COUNT(IF(`Risk_Factor` = 'Meduim', 1, NULL)) 'Medium',  COUNT(IF(`Risk_Factor` = 'Low', 1, NULL)) 'Low' ,
            COUNT(IF(`Risk_Factor` = 'WARNING', 1, NULL)) 'WARNING', COUNT(IF(`Risk_Factor` = 'PASSED', 1, NULL)) 'PASSED'

            FROM sow

            LEFT JOIN vuln on vuln.Host = sow.`IP_Host`
            WHERE sow.Projet=1 AND vuln.Host = sow.`IP_Host` and
            sow.Type='Serveur' and vuln.Port in (select `Ports_List` from PortsMapping WHERE Utilisation='Voip') group by `Nom`,`IP_Host`,`field4`; */
        $data_voip = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Voip');
            })
            ->select('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->selectRaw('COUNT(IF(Risk_Factor = "Critical" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Critical_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "High" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS High_Ex')
            ->selectRaw('COUNT(IF(Risk_Factor = "Medium" and (Metasploit ="true" or `Core Impact` = "true" or CANVAS = "true"),1 , NULL)) AS Medium_Ex')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Critical", 1, NULL)) AS Critical')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "High", 1, NULL)) AS High')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Medium", 1, NULL)) AS Medium')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "Low", 1, NULL)) AS Low')
            ->selectRaw(' COUNT(IF(vuln.Risk_Factor = "WARNING", 1, NULL)) WARNING')
            ->selectRaw('COUNT(IF(vuln.Risk_Factor = "PASSED", 1, NULL)) PASSED')
            ->groupBy('sow.Nom', 'sow.IP_Host', 'sow.field4')
            ->get();


        ///////Table2
        /*
SELECT `Risk_Factor`,`Synopsis`,count(*), GROUP_CONCAT(HOST) AS nbr
FROM sow
RIGHT JOIN vuln on vuln.Host = sow.`IP_Host` WHERE sow.Projet=1
AND vuln.Host = sow.`IP_Host` and sow.Type='Serveur' and
vuln.Port NOT in (select `Ports_List` from PortsMapping)
group by `Risk_Factor`,`Synopsis` ORDER BY `Risk_Factor`, nbr DESC;
*/

        $data2_serv = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereNotIn('vuln.Port', function ($query) {
                $query->select('Ports_List')

                    ->from('PortsMapping');
            })/*
            ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })*/
            ->select('vuln.Risk_Factor', 'vuln.Synopsis')
            ->selectRaw('COUNT(DISTINCT vuln.HOST) as count')
            ->selectRaw('GROUP_CONCAT(DISTINCT vuln.HOST ORDER BY vuln.HOST ASC SEPARATOR "  ") AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();



        /*
SELECT `Risk_Factor`,`Synopsis`,count(*), GROUP_CONCAT(HOST) AS nbr
FROM sow
RIGHT JOIN vuln on vuln.Host = sow.`IP_Host` WHERE sow.Projet=1
AND vuln.Host = sow.`IP_Host` and sow.Type='Serveur' and
vuln.Port in (select `Ports_List` from PortsMapping WHERE portsmapping.Utilisation="DB")
group by `Risk_Factor`,`Synopsis` ORDER BY `Risk_Factor`, nbr DESC;

*/
        $data2_db = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'DB');
            })
         ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Synopsis')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Synopsis')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();

        /*
            SELECT `Risk_Factor`,`Synopsis`,count(*), GROUP_CONCAT(HOST) AS nbr
            FROM sow
            RIGHT JOIN vuln on vuln.Host = sow.`IP_Host` WHERE sow.Projet=1
            AND vuln.Host = sow.`IP_Host` and sow.Type='PC' and
            vuln.Port in (select `Ports_List` from PortsMapping)
            group by `Risk_Factor`,`Synopsis` ORDER BY `Risk_Factor`, nbr DESC;


            */


        $data2_pc = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'PC')
            ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();



        $data2_ext = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'EXT')
            ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();

        $data2_apps = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            //    ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Apps');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();

        $data2_voip = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            //->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Voip');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();

        $data2_mails = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Mail');
            })
            ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk_Factor', 'vuln.Name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('GROUP_CONCAT(vuln.HOST) AS nbr')
            ->groupBy('vuln.Risk_Factor', 'vuln.Name')
            ->orderBy('vuln.Risk_Factor')
            ->orderByRaw('nbr DESC')
            ->get();
        //////////////table3
/*
SELECT
  vuln.Risk,
  vuln.Name,
  GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score,
  GROUP_CONCAT(vuln.Host) AS Elt_Impactes,
  COUNT(*) AS nbr,
  vuln.Risk,
  vuln.Name,
  vuln.`Plugin ID`,
  vuln.Description,
  vuln.Solution,
  vuln.`See Also` AS See,
  vuln.Metasploit,
  vuln.Canvas,
  vuln.Synopsis,
  vuln.`Core Impact` AS Core
FROM sow
INNER JOIN vuln ON vuln.Host = sow.IP_Host
WHERE sow.Project = 1
  AND sow.Type = 'Serveur'
  AND vuln.Port NOT IN (
    SELECT Ports_List
    FROM PortsMapping
  )
  AND (
    vuln.Metasploit = 'true'
    OR vuln.`Core Impact` = 'true'
    OR vuln.CANVAS = 'true'
  )GROUP BY
  vuln.Risk,
  vuln.Name
ORDER BY
  vuln.Risk,
  nbr DESC;

*/

        $data3_serv = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereNotIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();

        $data3_db = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Serveur')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'DB');
            })
            ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();

        $data3_pc = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'PC')
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();
        $data3_ext = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'EXT')
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();


        $data3_mails = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Mail')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'DB');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();

        $data3_voip = SoW::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Voip')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'DB');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
            ->get();

        $data3_apps = Sow::join('vuln', 'vuln.Host', '=', 'sow.IP_Host')
            ->where('sow.Projet', $id)
            ->where('sow.Type', 'Apps')
            ->whereIn('vuln.Port', function ($query) {
                $query->select('Ports_List')
                    ->from('PortsMapping')
                    ->where('Utilisation', 'Apps');
            })
             ->where(function ($query) {
                $query->where('vuln.Metasploit', 'true')
                    ->orWhere('vuln.Core Impact', 'true')
                    ->orWhere('vuln.CANVAS', 'true');
            })
            ->select('vuln.Risk', 'vuln.Name')
            ->selectRaw('GREATEST(vuln.`CVSS v3.0 Base Score`, vuln.`CVSS v2.0 Base Score`) AS Score')
            ->selectRaw('GROUP_CONCAT(vuln.Host) AS Elt_Impactes')
            ->selectRaw('COUNT(*) AS nbr')
            ->selectRaw('vuln.Risk')
            ->selectRaw('vuln.Name')
            ->selectRaw('vuln.`Plugin ID`')
            ->selectRaw('vuln.Description')
            ->selectRaw('vuln.Solution')
            ->selectRaw('vuln.`See Also` AS See')
            ->selectRaw('vuln.Metasploit')
            ->selectRaw('vuln.Canvas')
            ->selectRaw('vuln.Synopsis')
            ->selectRaw('vuln.`Core Impact` AS Core')
            ->groupBy('vuln.Risk', 'vuln.Name')
            ->orderBy('vuln.Risk')
            ->orderByRaw('nbr DESC')
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
            $templateProcessor2->setValue('DESC',  $project->Description);
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
                foreach ($item as $item2)
                {

                switch ( $item2['Risk_Factor']) {
                    case 'High':
                        $highRisk_serv[] =  $item2;
                        break;
                    case 'Critical':
                        $criticalRisk_serv[] =  $item2;
                        break;
                    case 'Medium':
                        $mediumRisk_serv[] =  $item2;
                        break;
                        // Add more cases if needed
                }
            }
            }

            $n2_h_serv = 0;
            $values2_h_serv = [];

            foreach ($highRisk_serv as $item) {
                $n2_h_serv++;
                $values2_h_serv[] = [
                    'id_h_serv' => $n2_h_serv,
                    'SRV_Risk_Factor_High' => $item->Risk_Factor,
                    'SRV_Synopsis_High' => $item->Synopsis,
                    'SRV_count_High' => $item->count,
                    'SRV_nbr_High' => Str::limit($item->nbr, 50, '...'),

                ];
            }

            $n2_m_serv = $n2_h_serv;
            $values2_m_serv = [];

            foreach ($mediumRisk_serv as $item) {
                $n2_m_serv++;
                $values2_m_serv[] = [
                    'id_m_serv' => $n2_m_serv,
                    'SRV_Risk_Factor_Medium' => $item->Risk_Factor,
                    'SRV_Synopsis_Medium' => $item->Synopsis,
                    'SRV_count_Medium' => $item->count,
                    'SRV_nbr_Medium' => Str::limit($item->nbr, 50, '...'),

                ];
            }

            $n2_c_serv = $n2_m_serv;
            $values2_c_serv = [];

            foreach ($criticalRisk_serv as $item) {
                $n2_c_serv++;
                $values2_c_serv[] = [
                    'id_c_serv' => $n2_c_serv,
                    'SRV_Risk_Factor_Critical' => $item->Risk_Factor,
                    'SRV_Synopsis_Critical' => $item->Synopsis,
                    'SRV_count_Critical' => $item->count,
                    'SRV_nbr_Critical' => Str::limit($item->nbr, 50, '...'),

                ];
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
                $templateProcessor->setValue('SRV_VULN_Synopsis' . '#' . $m, htmlspecialchars($item3->Synopsis));
                $templateProcessor->setValue('SRV_VULN_Name' . '#' . $m, htmlspecialchars($item3->Name));
                $templateProcessor->setValue('SRV_VULN_Hosts' . '#' . $m, htmlspecialchars($item3->Elt_Impactes));
                $templateProcessor->setValue('SRV_VULN_Metasploit' . '#' . $m, htmlspecialchars($item3->Metasploit));
                $templateProcessor->setValue('SRV_VULN_Core_Impact' . '#' . $m, htmlspecialchars($item3->Core));
                $templateProcessor->setValue('SRV_VULN_CANVAS' . '#' . $m, htmlspecialchars($item3->Canvas));
                $templateProcessor->setValue('SRV_VULN_Desc' . '#' . $m, htmlspecialchars($item3->Description));
                $templateProcessor->setValue('SRV_VULN_ref' . '#' . $m, htmlspecialchars($item3->See));
                $templateProcessor->setValue('SRV_VULN_Recomendations' . '#' . $m, htmlspecialchars($item3->Solution));

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
