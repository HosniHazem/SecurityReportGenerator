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

class TestController extends Controller
{
    public function get()
    {
        $id = 2;

/*
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

        $data2_serv = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
->whereIn('Risk', ['Critical', 'High', 'Medium'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();*/


/*

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
        ->get();*/

$string= "L’hôte Windows distant ne dispose pas de la mise à jour de sécurité KB4025339. Il est donc {{1}} affecté par plusieurs vulnérabilités : {{1}} {{1}} - Une vulnérabilité de divulgation d'informations existe dans la {{1}} console Windows Performance Monitor en raison d'un {{1}} incorrect. analyse d'une entrée XML contenant une référence à une entité externe {{1}}. Un attaquant distant non authentifié {{1}} peut exploiter cela, en convainquant un utilisateur de créer un {{1}} ensemble de collecteurs de données et d'importer un fichier XML {{1}} spécialement conçu, pour divulguer des fichiers arbitraires via un fichier XML externe. Déclaration d'entité {{1}} (XXE). (CVE-2017-0170) {{1}} {{1}} - Il existe une vulnérabilité d'exécution de code à distance dans l'Explorateur Windows {{1}} en raison d'une mauvaise gestion des fichiers exécutables {{1}} et des partages lors des opérations de changement de nom. Un attaquant distant {{1}} non authentifié peut exploiter cela, en convainquant un utilisateur {{1}} d'ouvrir un fichier spécialement conçu, pour exécuter du code {{1}} arbitraire dans le contexte de l'utilisateur actuel. (CVE-2017-8463) {{1}} {{1}} - Plusieurs vulnérabilités d'élévation de privilèges existent dans {{1}} le composant Microsoft Graphics en raison d'une mauvaise gestion {{1}} des objets en mémoire. Un attaquant local peut {{1}} les exploiter, via une application spécialement conçue, pour {{1}} exécuter du code arbitraire en mode noyau. (CVE-2017-8467, {{1}} CVE-2017-8556, CVE-2017-8573, CVE-2017-8574, {{1}} CVE-2017-8577, CVE-2017-8578, CVE-2017 -8580) {{1}} {{1}} - Une vulnérabilité de divulgation d'informations existe dans Win32k {{1}} en raison d'une mauvaise gestion des objets en mémoire. Un attaquant local {{1}} peut exploiter cela, via une application {{1}} spécialement conçue, pour divulguer des informations sensibles. {{1}} (CVE-2017-8486) {{1}} {{1}} - Une vulnérabilité de contournement de sécurité existe dans Microsoft {{1}} Windows lors du traitement des échanges de tickets Kerberos en raison d\'un échec {{1}} pour empêcher la falsification du champ SNAME. Un {{1}} homme dans le";



        $pattern1 = "/([[:punct:]]+ *)(\n)+/";
		$pattern11 = "/([[:punct:]]+ )(\{\{1\}\})+/";/*
        $pattern2 = "/(\n)+/ *-";
		$pattern21 = "/(\{\{1\}\})+/ *-";

        $pattern3 = "/(\n)+/";
		$pattern31 = "/(\{\{1\}\})+/";

        $replacement = "</w:t></w:r><w:r><w:br/><w:t>";
        $string = htmlspecialchars($string);
        $string = preg_replace($pattern1, $replacement, $string);
		$string = preg_replace($pattern11, $replacement, $string);
        $string = preg_replace($pattern2, $replacement, $string);
		$string = preg_replace($pattern21, $replacement, $string);
        if ($seeAlso==1)  $string = preg_replace( $pattern3 , $replacement, $string);
        $string = preg_replace( $pattern31 , " ", $string);


        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);*/
        $replacement = "</w:t></w:r><w:r><w:br/><w:t>";
        $string = preg_replace($pattern11,'${1}'. $replacement, $string);
        return $string;



}
}
