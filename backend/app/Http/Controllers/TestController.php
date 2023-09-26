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
        $id=2;
      
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
return $data_serv;
    // Organize the data into an object
    $filteredData = [];
    
    foreach ($data_serv  as $item) {
        $host = $item->Hostip;
        $risk = $item->Risk;
        
        if (!isset($filteredData[$host])) {
            $filteredData[$host] = [];
        }
    
        if (!isset($filteredData[$host][$risk])) {
            $filteredData[$host][$risk] = [];
        }
    
        $filteredData[$host][$risk][] = [
            'synopsis' => $item->synopsis,
            'exploited_by_malware' => $item->exploited_by_malware,
            'exploit_available' => $item->exploit_available,
        ];
    }
        return $filteredData;
}
}