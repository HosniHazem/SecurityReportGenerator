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

public static function translate($q)
{

    $res= file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=en&tl=fr&hl=hl&q=".urlencode($q), $_SERVER['DOCUMENT_ROOT']."/transes.html");
    return json_decode($res)[0][0][0];
}


public static function translateAllPlugins()
{

   $allPlugins =  DB::select("SELECT  `id`, `name`, `description`, `solution`,`synopsis` FROM  plugins WHERE translated <> 'yes'");
   $i=0;
   foreach($allPlugins as $plugin)
   {


       $re = DB::table('plugins')
    ->where('id', $allPlugins[$i]->id)
    ->update(['translated' => 'yes', 'name' => self::translate($allPlugins[$i]->name),'description' => self::translate($allPlugins[$i]->description),'solution' => self::translate($allPlugins[$i]->solution),'synopsis' => self::translate($allPlugins[$i]->synopsis)]);
    $i++;
}

}

    public function get()
    {

        set_time_limit(5000);
        echo self::translateAllPlugins(); //[]
        /*
        $id = 2;
        $pluginIds = DB::table('vuln as v')
        ->select('v.Plugin ID as PluginID')
        ->distinct()
        ->whereNotIn('v.Plugin ID', function ($query) {
            $query->select('id')
                ->from('plugins');
        })
        ->get();*/

        $pluginIds=  "";//DB::select("SELECT DISTINCT `Plugin ID` FROM vuln WHERE `Plugin ID` NOT IN (SELECT ID from plugins) ");

<<<<<<< HEAD
        $pluginIds =  DB::select("SELECT DISTINCT `Plugin ID`  as PluginID FROM vuln  WHERE `Plugin ID` NOT IN (SELECT DISTINCT id FROM  plugins)");
        return  $pluginIds;
=======
        return $pluginIds;
>>>>>>> fb072f86f32a14399a2d88823a8ef5d09e269765



}
}
