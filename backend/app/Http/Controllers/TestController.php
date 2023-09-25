<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\HtmlString;

class TestController extends Controller
{
    public function get()
    {
        
        
        
        $te = "L'hôte distant exécute un service « de jour ». Ce service est {{1}}  - conçu pour donner l'heure locale de cet hôte à quiconque {{1}} se connecte à ce port. {{1}} {{1}} Le format de date émis par ce service peut parfois aider un attaquant {{1}} à deviner le type de système d'exploitation de cet hôte, ou à mettre en place des attaques d'authentification {{1}} chronométrées contre l'hôte distant. {{1}} {{1}} De plus, si le service de jour s'exécute sur un port UDP, un {{1}} attaquant peut le lier au port d'écho d'un hôte tiers en utilisant {{1}} usurpation d'identité, créant ainsi une éventuelle > £ condition de déni de service entre {{1}} cet hôte et le tiers.";
      $text = htmlspecialchars($te);
        $pattern1 = "/[[:punct:]]+ *(\{\{1\}\})+/";
$pattern2 = "/(\{\{1\}\})+ *-/"; 
$pattern3 = "/(\{\{1\}\})+/";
$replacement = "</w:t></w:r><w:r><w:br/><w:t>";


$text = preg_replace($pattern1, $replacement, $text);
 $text = preg_replace($pattern2, $replacement, $text);
$text = preg_replace($pattern3, " ", $text); 


return $text;
    }
}