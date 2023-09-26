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

       



return $data21_serv;
   
 
}
}