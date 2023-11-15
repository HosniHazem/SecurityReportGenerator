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
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\PDF;
use Barryvdh\DomPDF\Facade as PDFDom;
use Dompdf\Dompdf;



class WordDocumentController4 extends Controller
{



    //the function to fill ansi  repot
    public function generateWordDocument($customerId)
    {
        set_time_limit(1000);
        //query for  customers table 
        $sqlCustomers = 'SELECT 
        c.id AS ID,
        c.SN AS SN,
        c.LN AS LN,
        c.Logo AS Logo,
        c.Description AS Description,
        c.SecteurActivité AS SecteurActivite,
        c.Categorie AS Categorie,
        c.`Site web` AS `Site_Web`,
        c.`Addresse mail` AS `Adresse_mail`,
        c.Organigramme AS Organigramme,
        c.Network_Design AS Network_Design,
        c.type AS leType
    FROM customers AS c
    WHERE c.id = ?;';

        //query for process  table
        $sqlProcess = 'SELECT
        rpd.ID AS Processus_domaine_ID,
        rpd.Processus_domaine AS process,
        MAX(rpav.D) AS Process_D,
        MAX(rpav.I) AS Process_I,
        MAX(rpav.C) AS Process_C
    FROM
        rm_iteration ri
    JOIN
        rm_processus_domains rpd ON ri.ID = rpd.ID_ITERATION
    LEFT JOIN
        rm_processus_actifs_valeurs rpav ON rpd.ID = rpav.ID_Processus
    WHERE
        ri.CustomerId =?
    GROUP BY
        rpd.ID, rpd.Processus_domaine;';
    



        $sql =  <<<HERE10
        SELECT `standards_controls`.`Clause`, `standards_controls`.`controle`, rm_answers.Answer, `rm_questions`.`Bonne pratique` as 'bp', `rm_questions`.`Vulnérabilité` as 'vuln' FROM `standards_controls` LEFT JOIN `rm_questions` ON `standards_controls`.`ID` = `rm_questions`.`ID_control` LEFT JOIN `rm_answers` ON `rm_questions`.`QuestionID` = `rm_answers`.`ID_Question` LEFT JOIN `rm_iteration` ON `rm_answers`.`ID_ITERATION` = `rm_iteration`.`ID` WHERE LENGTH(`rm_questions`.`Vulnérabilité`) > 5 AND `rm_iteration`.`CustomerID` = ?  ORDER BY `Clause`, `controle`, `rm_questions`.`Question_numero` ASC LIMIT 1000;;
        HERE10;

        //sql for  Siege Description
        $sqlApplication = "SELECT
        a.`Nom` AS App_Name,
        a.`field3` AS App_Module,
        a.`field4` AS App_Descr,
        a.`field5` AS App_EnvDev,
        a.`dev_by` AS App_DevPar,
        a.`URL` AS App_IPs,
        a.`Number_users` AS App_NumberUsers
    FROM
        `sow` a
    JOIN
        `projects` p ON a.`Projet` = p.`id`
    WHERE
        (a.`Type` = 'Apps' OR a.`Type` = 'Ext') AND p.`customer_id` = ?";
            //sql for "serveurs par plateforme"
        $sqlServers = "SELECT
        a.`Nom` AS Srv_Name,
        a.`IP_Host` AS Srv_IP,
        a.`field3` AS Srv_Type,
        a.`field4` AS Srv_SE,
        a.`field5` AS Srv_Role
    FROM
        `sow` a
    JOIN
        `projects` p ON a.`Projet` = p.`id`
    WHERE
        a.`Type` = 'Serveur' AND p.`customer_id` = ?";

        //sql for customers site
        $sqlCustomerSite = 'SELECT Numero_site as N_Site, Structure as Structure_Site, Lieu as Lieu_Site FROM `Customer_sites` WHERE Customer_ID=? ';
        //sql for "Infrastucture Réseau et sécurité"
        $sqlInfrastructure = "SELECT
        a.`Nom` AS Infra_Nature,
        a.`IP_Host` AS Infra_Marque,
        a.`field3` AS Infra_Number,
        a.`field4` AS Infra_ManagedBy,
        a.`field5` AS Infra_Obs
    FROM
        `sow` a
    JOIN
        `projects` p ON a.`Projet` = p.`id`
    WHERE
        a.`Type` = 'R_S' AND p.`customer_id` = ?";
            //sql for postes de travail
        $sqlPosteTravail = "SELECT sow.field4 AS PC_SE, COUNT(sow.field4) AS PC_Number FROM sow JOIN projects ON sow.Projet = projects.id JOIN customers ON projects.customer_id = customers.id WHERE sow.`Type` LIKE 'PC' AND customers.id = ?";
                //sql for network Design Image
        $sqlNetworkDesign="SELECT `Network_Design` FROM `customers` WHERE id=?";
        //sql for audit tools
        $sqlAuditTools="SELECT `Tool_name` as tool ,`Version` tool_version,`License` as tool_license,`Feature` as tool_features,`Composante_SI` as tool_sow FROM `Audit_Tools` ORDER BY `Composante_SI`;";
        //sql for "equipe de projet"
        $sqlProjectTeam="SELECT `Nom` as SPOC_Tech_Name ,`Titre` as SPOC_Tech_Title,`Adresse mail primaire` as SPOC_Tech_email ,`Adresse mail secondaire`,`Tél` as SPOC_Tech_Tel FROM `glb_pip` WHERE `Cusotmer_ID`=?";

       
        //sql for domain table 
        $sqlDomain = <<<HERE10
        SELECT
            `Clause_name` as Domain,
            `controle_name` as Mesures,
            ROUND(SUM(5 * `rm_questions`.`P` * rm_answers.Answer) / SUM(`rm_questions`.`P`), 1) as Value
        FROM `standards_controls`
        LEFT JOIN rm_questions ON standards_controls.ID = `rm_questions`.`ID_control`
        LEFT JOIN rm_answers ON rm_answers.ID_Question = rm_questions.QuestionID
        GROUP BY `Clause`, `controle`
        ORDER BY `Clause`, `controle` ASC;
        HERE10;
    

        $templatePath = public_path("0.docx");

        $templateProcessor = new TemplateProcessor($templatePath);  



        $outputFileName = 'ansi-2023.docx';
        
        $outputPath = public_path('' . $outputFileName);

        //Year

      
        
        //today's date
        $today=self::currentDate();
        $templateProcessor->setValue('today',$today);

        //table "domaine"
        $domain= DB::select($sqlDomain);
        $domainArray= self::processDatabaseData($domain);
        $templateProcessor->cloneRowAndSetValues('Domain', $domainArray);


        



        //table "equipe de projet"
        $projectTeam = DB::select($sqlProjectTeam, [$customerId]);
        $projectTeamArray = self::processDatabaseData($projectTeam);

        // twice becuz if I do once it only fills the first table
        $templateProcessor->cloneRowAndSetValues('SPOC_Tech_Name', $projectTeamArray);
        $templateProcessor->cloneRowAndSetValues('SPOC_Tech_Name', $projectTeamArray);

        //audit tools table

        $auditTools = DB::select($sqlAuditTools);
        $auditToolsArray = self::processDatabaseData($auditTools);

        $templateProcessor->cloneRowAndSetValues('tool', $auditToolsArray);

        //Network Design image
        $networkDesign = DB::select($sqlNetworkDesign, [$customerId]);
        
        $networkDesignArray = self::processDatabaseData($networkDesign);

        //NetworkDesign:800:800
        $networkDesignRow = $networkDesignArray[0];

        $networkDesignValue = $networkDesignRow['Network_Design'] ?? "pas de network Design";

        $templateProcessor->setImageValue('NetworkDesign:800:800', array('path'=>$networkDesignValue ,'width'=>500));
        //table:Postes de travail
        $posteTravail=DB::select($sqlPosteTravail, [$customerId]);
        $posteTravailArray= self::processDatabaseData($posteTravail);
        $templateProcessor->cloneRowAndSetValues('PC_SE', $posteTravailArray);

        //table:Infrastucture Réseau et sécurité 
        $Infrastructure=DB::select($sqlInfrastructure, [$customerId]);
        $InfrastructureArray= self::processDatabaseData($Infrastructure);
        $templateProcessor->cloneRowAndSetValues('Infra_Nature', $InfrastructureArray);



        //Table:serveur par plateforme
        $servers = DB::select($sqlServers, [$customerId]);
        $serverArray = self::processDatabaseData($servers);
         $templateProcessor->cloneRowAndSetValues('Srv_Name', $serverArray);


        //description du siege (Applications):

        $application = DB::select($sqlApplication, [$customerId]);
        $applicationArray = self::processDatabaseData($application );
        $templateProcessor->cloneRowAndSetValues('App_Name', $applicationArray);











        //customer site 
        $CustomersSite =  DB::select($sqlCustomerSite, [$customerId]);
        $CustomersSiteArray =  self::processDatabaseData($CustomersSite);
        $templateProcessor->cloneRowAndSetValues('N_Site', $CustomersSiteArray);


        //Process Table  
        $Process = DB::select($sqlProcess, [$customerId]);
        $modifiedProcessArray = self::processDatabaseData($Process);
        $templateProcessor->cloneRowAndSetValues('process', $modifiedProcessArray);



        //Customer Table 
        $Customers =  DB::select($sqlCustomers, [$customerId]);
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

            $templateProcessor->setImageValue('organigrame:800:800', array('path'=>$organigrame,'width'=>500));
        } else {
            return response()->json("no customer with this id exists ");
        }

        $AllRows =  DB::select($sql,[$customerId]);
        $allRowsAsArray = [];

        foreach ($AllRows as $row) {

            if ($row->Answer > 0) {
                $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][] = $row->bp;
            } else {
                $allRowsAsArray[$row->Clause][$row->controle][$row->Answer][] = $row->vuln;
                //  echo $row->Answer."aaaaaaaaaaaaaaaa";
            }
        }
        foreach ($allRowsAsArray as $ClauseId => $rowData) {
            foreach ($rowData as $ControlID => $cellData) {
                // Add debug statements here
        
                // Call the setOneRowControl function for Best Practices
                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 1, "_BestPractice#");
        
                // Call the setOneRowControl function for Vulnerabilities
                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 0, "_Vuln#");
            }
        }
        
        // return $allRowsAsArray;


        $templateProcessor->saveAs($outputPath);
        // $pdfContent = self::ConvertPDF($outputPath);
        
        // if ($pdfContent) {
        //     return response($pdfContent)
        //         ->header('Content-Type', 'application/pdf')
        //         ->header('Content-Disposition', 'attachment; filename=output.pdf');
        // } else {
        //     abort(500, 'Failed to generate PDF');
        // }

        //header for the type  of the file which is .docx
        // $headers = array(
        //     'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        // );
        
        // // Create a response with the file content and headers
        // $response = response()->download($outputPath, 'ansi2023.docx', $headers);
        
        // // Delete the file after it's downloaded to avoid cluttering the server
        
        // // Return the response
        // return $response;

 
    }

    //to test downloadble ifle
    public function downloadFile(Request $request, $filename)
{
    // Define the source directory path where you want to check for the file
    $sourcePath = 'C:\xampp\htdocs\AppGenerator\backend\public';

    // Combine the source directory path with the requested filename to check for existence
    $sourceFile = $sourcePath . '/' . $filename;

    // Check if the file exists in the source directory
    if (file_exists($sourceFile)) {
        // Define the destination directory path where you want to save the downloaded file
        $destinationPath = 'C:\xampp\htdocs\AppGenerator\backend\public\downloads';

        // Combine the destination directory path with the requested filename
        $outputPath = $destinationPath . '/' . $filename;

        // Determine the file's MIME type
        $mimeType = mime_content_type($sourceFile);

        // Copy the file from the source directory to the destination directory
        copy($sourceFile, $outputPath);

        // Return the copied file as a downloadable response
        return response()->download($outputPath, $filen, ['Content-Type' => $mimeType]);
    } else {
        // If the file doesn't exist in the source directory, return a 404 Not Found response
        dd("File does not exist at path: " . $outputPath);
    }
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
    
    public static function ConvertPDF($inputPath)
{
    $docxFilePath = $inputPath;

    // Load the DOCX file
    $phpWord = IOFactory::load($docxFilePath);

    // Save the DOCX content as HTML
    $tempHtmlFile = tempnam(sys_get_temp_dir(), 'docx_to_pdf');
    $phpWord->save($tempHtmlFile, 'HTML');

    // Convert HTML to PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml(file_get_contents($tempHtmlFile));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Clean up temporary files
    unlink($tempHtmlFile);

    // Return the PDF content as response
    return $dompdf->output();
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
    static function currentDate(){
        $current_date = date('Y-m-d'); 

        return $current_date;
    }
    
}
