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
        //sql for  Siege Description
        $sqlApplication = "SELECT `Nom` as App_Name , `field3` as App_Module , `field4` as  App_Descr , `field5` as App_EnvDev , `dev by` as App_DevPar , `URL` as App_IPs , `Number of users`  as App_NumberUsers FROM `Audit_sow` WHERE Type = 'Application' and `Customer` = ?";
        //sql for "serveurs par plateforme"
        $sqlServers = "SELECT `Nom` as Srv_Name , IP_Host as Srv_IP , `field3` as Srv_Type, `field4` as Srv_SE , `field5` as Srv_Role FROM `Audit_sow` WHERE Type='Serveur' and `Customer`=?";
        //sql for customers site
        $sqlCustomerSite = 'SELECT Numero_site as N_Site, Structure as Structure_Site, Lieu as Lieu_Site FROM `Customer_sites` WHERE Customer_ID=? ';
        //sql for "Infrastucture Réseau et sécurité"
        $sqlInfrastructure = "SELECT Nom as Infra_Nature, IP_Host as Infra_Marque , field3 as Infra_Number, field4 as Infra_ManagedBy, field5 as Infra_Obs FROM Audit_sow WHERE Type='Infra' AND Customer=?";
        //sql for postes de travail
        $sqlPosteTravail = "SELECT field4 as PC_SE , COUNT(field4) as PC_Number FROM Audit_sow WHERE Type='PC' AND Customer=? GROUP BY field4";
        //sql for network Design Image
        $sqlNetworkDesign="SELECT `Network_Design` FROM `glb_customers` WHERE id=?";
        //sql for audit tools
        $sqlAuditTools="SELECT `Tool_name` as tool ,`Version` tool_version,`License` as tool_license,`Feature` as tool_features,`Composante_SI` as tool_sow FROM `Audit_Tools` ORDER BY `Composante_SI`;";

        $templatePath = public_path("0.docx");
        $templateProcessor = new TemplateProcessor($templatePath);


        $outputFileName = 'ansi2023.docx';
        $outputPath = public_path('' . $outputFileName);


        //audit tools table

        $auditTools = DB::select($sqlAuditTools);
        $auditToolsArray=[];
        $auditToolsArray = self::processDatabaseData($auditTools);
        $templateProcessor->cloneRowAndSetValues('tool', $auditToolsArray);

        
        





        //Network Design image
        $networkDesign = DB::select($sqlNetworkDesign, [$request->customer]);
        $networkDesignArray = [];
        
        foreach ($networkDesign as $item) {
        $modifiedItem = [];

        foreach ($item as $key => $value) {
        $modifiedItem[$key] = htmlspecialchars($value, ENT_XML1);
        }

        $networkDesignArray[] = $modifiedItem;
        }
        $networkDesignRow = $networkDesignArray[0]; 
        $networkDesignValue = isset($networkDesignRow->Network_Design) ? networkDesignRow->Network_Design : "pas de network Design";
        $templateProcessor->setValue('NetworkDesign:800:800', $networkDesignValue);

        

        //table:Postes de travail
        $posteTravail=DB::select($sqlPosteTravail, [$request->customer]);
        $posteTravailArray=[];

        foreach ($posteTravail as $item) {
            $modifiedposteTravail = [];
   
            foreach ($item as $key => $value) {
           $modifiedposteTravail[$key] = htmlspecialchars($value, ENT_XML1);
            }
   
           $posteTravailArray[] =  $modifiedposteTravail;
           }
        //    return response()->json($posteTravailArray);
           $templateProcessor->cloneRowAndSetValues('PC_SE', $posteTravailArray);

        //table:Infrastucture Réseau et sécurité 
        $Infrastructure=DB::select($sqlInfrastructure, [$request->customer]);
        $InfrastructureArray=[];

        foreach ($Infrastructure as $item) {
            $modifiedInfrastructure = [];
   
            foreach ($item as $key => $value) {
           $modifiedInfrastructure[$key] = htmlspecialchars($value, ENT_XML1);
            }
   
           $InfrastructureArray[] =  $modifiedInfrastructure;
           }
           $templateProcessor->cloneRowAndSetValues('Infra_Nature', $InfrastructureArray);



        //Table:serveur par plateforme
        $servers = DB::select($sqlServers, [$request->customer]);
        $serverArray = [];

        foreach ($servers as $item) {
         $modifiedServer = [];

         foreach ($item as $key => $value) {
        $modifiedServer[$key] = htmlspecialchars($value, ENT_XML1);
         }

        $serverArray[] = $modifiedServer;
        }

        $templateProcessor->cloneRowAndSetValues('Srv_Name', $serverArray);














        //description du siege (Applications):

        $application = DB::select($sqlApplication, [$request->customer]);
        $applicationArray = [];
        

        foreach ($application as $item) {
            $modifiedAppName = htmlspecialchars($item->App_Name, ENT_XML1);
            $modifiedAppModule = htmlspecialchars($item->App_Module, ENT_XML1);
            $modifiedAppDescr = htmlspecialchars($item->App_Descr, ENT_XML1);
            $modifiedAppEnvDev = htmlspecialchars($item->App_EnvDev, ENT_XML1);
            $modifiedAppDevPar = htmlspecialchars($item->App_DevPar, ENT_XML1);
            $modifiedAppIPs = htmlspecialchars($item->App_IPs, ENT_XML1);
            $modifiedAppNumberUsers = htmlspecialchars($item->App_NumberUsers, ENT_XML1);

            $item->App_Name = $modifiedAppName;
            $item->App_Module = $modifiedAppModule;
            $item->App_Descr = $modifiedAppDescr;
            $item->App_EnvDev = $modifiedAppEnvDev;
            $item->App_DevPar = $modifiedAppDevPar;
            $item->App_IPs = $modifiedAppIPs;
            $item->App_NumberUsers = $modifiedAppNumberUsers;

            $applicationArray[] = $item;
        }

        $templateProcessor->cloneRowAndSetValues('App_Name', $applicationArray);











        //customer site 
        $CustomersSite =  DB::select($sqlCustomerSite, [$request->customer]);
        $CustomersSiteArray = [];

        foreach ($CustomersSite as $item) {
            $modifiedCustomersSiteNumeroSite = htmlspecialchars($item->N_Site, ENT_XML1);
            $modifiedCustomersSiteStructure = htmlspecialchars($item->Structure_Site, ENT_XML1);
            $modifiedCustomersSiteLieu = htmlspecialchars($item->Lieu_Site, ENT_XML1);


            $item->N_Site = $modifiedCustomersSiteNumeroSite;
            $item->Structure_Site = $modifiedCustomersSiteStructure;
            $item->Lieu_Site = $modifiedCustomersSiteLieu;
            // Add the modified object to the new array
            $CustomersSiteArray[] = $item;
        }
        $templateProcessor->cloneRowAndSetValues('N_Site', $CustomersSiteArray);


        //Process Table  
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

        $templateProcessor->cloneRowAndSetValues('process', $modifiedProcessArray);



        //Customer Table 
        $Customers =  DB::select($sqlCustomers, [$request->customer]);
        if (!empty($Customers)) {
            $firstRow = $Customers[0];
            $SN = $firstRow->SN;
            $LN = $firstRow->LN;
            $typeCompany = $firstRow->leType;

            $ActivitySector = $firstRow->SecteurActivite;
            $Categorie = $firstRow->Categorie;
            $siteWeb = $firstRow->Site_Web;
            $mailAddress = $firstRow->Adresse_mail;
            $description = $firstRow->Description;
            $organigrame = $firstRow->Organigramme ? $firstRow->Organigramme : " organigramme non disponible";


            $templateProcessor->setValue('SN', $SN);
            $templateProcessor->setValue('LN', $LN);
            $templateProcessor->setValue('typeCompany', $typeCompany);
            $templateProcessor->setValue('SecteurActivite', $ActivitySector);
            $templateProcessor->setValue('Categorie', $Categorie);
            $templateProcessor->setValue('siteweb', $siteWeb);
            $templateProcessor->setValue('mailadress', $mailAddress);
            $templateProcessor->setValue('DescriptionCompany', $description);
            $templateProcessor->setValue('organigrame:800:800', $organigrame);
        } else {
            return response()->json("no customer with this id exists ");
        }

        $AllRows =  DB::select($sql);

        $allRowsAsArray = [];

        foreach ($AllRows as $row) {

            if ($row->Answer > 0) {
                $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][] = $row->bp;
            } else {
                $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][] = $row->vuln;
                //  echo $row->Answer."aaaaaaaaaaaaaaaa";
            }
        }
        //return $allRowsAsArray;
        foreach ($allRowsAsArray as $ClauseId => $rowData) {


            foreach ($rowData as $ControlID => $cellData) {

                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 1, "_BestPractice#");

                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 0, "_Vuln#");
            }
        }


        $templateProcessor->saveAs($outputPath);


        //  return response()->download($filepath,$filename)->deleteFileAfterSend(true);



    }

    static function setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, $type, $typeTag)
    {
        //   echo  $ClauseId."_". $ControlID."\n";

        if (!isset($cellData[$type])) {
            echo $ClauseId . $typeTag . $ControlID;
            $templateProcessor->setValue($ClauseId . $typeTag . $ControlID, "");
            return;
        };

        $templateProcessor->cloneRow($ClauseId . $typeTag . $ControlID, count($cellData[$type]));
        //        var_dump("\${".$ClauseId.$typeTag.$ControlID."#$i}", $value);
        $i = 1;

        foreach ($cellData[$type] as $attr => $value) {

            $templateProcessor->setValue($ClauseId . $typeTag . $ControlID . "#$i", $value);
            $templateProcessor->setValue($ClauseId . $typeTag . $ControlID, $value);
            $i++;
        }
        if ($i == 1) {
            echo $ClauseId . $typeTag . $ControlID;
            $templateProcessor->cloneRowAndSetValues($ClauseId . $typeTag . $ControlID, []);
        }
    }
    
    static function preparePagesDeGarde($templateProcessor, $annex_id, $customer, $project)
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
    public static function send_whatsapp($message = "Test")
    {
        $url = 'https://api.callmebot.com/whatsapp.php?phone=21629961666&apikey=2415178&text=' . urlencode($message);
        if ($ch = curl_init($url)) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $html = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // echo "Output:".$html;  // you can print the output for troubleshooting
            curl_close($ch);
            return (int) $status;
        } else return;
    }
    static function processDatabaseData($data) {
        $result = [];
    
        foreach ($data as $item) {
            $modifiedItem = [];
    
            foreach ($item as $key => $value) {
                $modifiedItem[$key] = htmlspecialchars($value, ENT_XML1);
            }
    
            $result[] = $modifiedItem;
        }
    
        return $result;
    }
    
}
