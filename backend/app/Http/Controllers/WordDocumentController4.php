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
    SELECT
        rm_answers.ID_Question,
        standards_controls.Controle_ID,
        standards_controls.Clause,
        standards_controls.controle,
        rm_answers.Answer,
        rm_questions.`Bonne pratique` as bp,
        rm_questions.Vulnérabilité as vuln
    FROM
        standards_controls
    LEFT JOIN
        rm_questions ON standards_controls.Controle_ID = rm_questions.`ISO 27002:2022`
    LEFT JOIN
        rm_answers ON rm_answers.ID_Question = rm_questions.QuestionID
    LEFT JOIN
        rm_iteration ON rm_answers.ID_ITERATION = rm_iteration.id
    WHERE
        LENGTH(rm_questions.Vulnérabilité) > 5
        AND rm_iteration.CustomerID = ?
    ORDER BY
        rm_answers.ID_Question ASC;
HERE10;

        $sqlVuln = "SELECT

        rm_questions.QuestionID AS Vuln_ref,
        rm_questions.Vulnérabilité AS Vuln_desc,
        rm_questions.`plan d'action` AS Vuln_recom,
        standards_controls.Controle_ID,
        rm_questions.`ISO 27002:2022` as Vuln_si,
        rm_answers.Pertinence,
        rm_answers.Answer
        FROM
        rm_questions
        JOIN
        standards_controls ON standards_controls.Controle_ID = rm_questions.`ISO 27002:2022`
        LEFT JOIN
        rm_answers ON rm_answers.ID_Question = rm_questions.QuestionID
        LEFT JOIN
        rm_iteration ON rm_answers.ID_ITERATION = rm_iteration.id
        WHERE
        rm_answers.Pertinence = 4
        AND rm_answers.Answer = 0
        AND rm_iteration.CustomerID = ?
        AND rm_answers.ID_ITERATION = rm_iteration.id
        ORDER BY
        rm_questions.QuestionID ASC;";


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
        $sqlNetworkDesign = "SELECT `Network_Design` FROM `customers` WHERE id=?";
        //sql for audit tools
        $sqlAuditTools = "SELECT `Tool_name` as tool ,`Version` tool_version,`License` as tool_license,`Feature` as tool_features,`Composante_SI` as tool_sow FROM `Audit_Tools` ORDER BY `Composante_SI`;";
        //sql for "equipe de projet"
        $sqlProjectTeam = "SELECT `Nom` as SPOC_Tech_Name ,`Titre` as SPOC_Tech_Title,`Adresse mail primaire` as SPOC_Tech_email ,`Adresse mail secondaire`,`Tél` as SPOC_Tech_Tel FROM `glb_pip` WHERE `Cusotmer_ID`=?";


        //sql for domain table 
        $sqlDomain = <<<HERE10
        SELECT `Clause_name` AS Domain, `controle_name` AS Mesures, ROUND( SUM(5 * `rm_questions`.`P` * rm_answers.Answer) / SUM(`rm_questions`.`P`), 1 ) AS Value FROM `standards_controls` LEFT JOIN `rm_questions` ON `standards_controls`.`ID` = `rm_questions`.`ID_control` LEFT JOIN `rm_answers` ON `rm_answers`.`ID_Question` = `rm_questions`.`QuestionID` LEFT JOIN `rm_iteration` ON `rm_iteration`.`ID` = `rm_answers`.`ID_ITERATION` WHERE `rm_iteration`.`CustomerID` = ? GROUP BY `Clause`, `controle` ORDER BY `Clause`, `controle` ASC;
        HERE10;


        //sql for prev audit
        $sqlPrevAudit = "SELECT Project_name as ProjectName,`Action` as Action, `ActionNumero` as ActionNumero,`ProjetNumero` as projNum ,`Criticite` as Criticite ,`Chargee_action` as chargeaction,`ChargeHJ` as charge,`TauxRealisation` as tauxrealisation,`Evaluation` as Evaluation FROM audit_previousaudits_ap AS ap JOIN projects ON ap.projectID = projects.id JOIN customers ON projects.customer_id = customers.id WHERE customers.id = ? Order by `ProjetNumero`,`ActionNumero`";


        $sqlYear = 'SELECT `year` from `projects`WHERE `customer_id`=?';

        $templatePath = public_path("0.docx");

        $templateProcessor = new TemplateProcessor($templatePath);

        $allImagesPath = public_path('images/uploads');

        $outputFileName = 'ansi-2023.docx';

        $outputPath = public_path('' . $outputFileName);

        //Year of customer
        $yearResult = DB::select($sqlYear, [$customerId]);
        if (!empty($yearResult)) {
            $yearRow = $yearResult[0];
            $year = $yearRow->year;
            if (isset($year)) {
            }
            $templateProcessor->setValue('Y', $year);
        } else {
            $templateProcessor->setValue('Y', "2023");
        }

        //current year 
        $currentYear = date('Y');
        $templateProcessor->setValue('year', $year);


        // Part 9.2
        $vuln = DB::select($sqlVuln, [$customerId]);
        $vulnArray = self::processDatabaseData($vuln);
        if (!empty($vulnArray)) {
            $templateProcessor->cloneRowAndSetValues('RowNumber', $vulnArray);
        }


        $vulnArrayLength = count($vulnArray);


        //Part 11.1

        // self::processPA_chapter11(5, "3", $templateProcessor);
        // self::processPA_chapter11(6, "3", $templateProcessor);
        // self::processPA_chapter11(7, "3", $templateProcessor);
        // self::processPA_chapter11(8, "3", $templateProcessor);




        //today's date
        $today = self::currentDate();
        $templateProcessor->setValue('today', $today);

        //table "domaine"
        $domain = DB::select($sqlDomain, [$customerId]);
        $domainArray = self::processDatabaseData($domain);
        $templateProcessor->cloneRowAndSetValues('Domain', $domainArray);


        //table prev audit 
        //to do:Merge Cells

        $prevAudit = DB::select($sqlPrevAudit, [$customerId]);
        $prevAuditArray = self::processDatabaseData($prevAudit);
        if (!empty($prevAuditArray)) {
            $prevAuditArrayLength = count($prevAuditArray);
            //get number of projects 
            $uniqueProjects = [];
            foreach ($prevAuditArray as $entry) {
                $projectName = $entry['ProjectName'];
                if (!in_array($projectName, $uniqueProjects)) {
                    $uniqueProjects[] = $projectName;
                }
            }
            $numberOfProjects = count($uniqueProjects);

            $maxProjNum = self::getMaxProjNum($prevAuditArray);
            $organizedData = [];
            foreach ($prevAuditArray as $entry) {
                $projectName = $entry['ProjectName'];
                if (!isset($organizedData[$projectName])) {
                    $organizedData[$projectName] = [];
                }
                $organizedData[$projectName][] = [
                    'Action' => $entry['Action'],
                    'ActionNumero' => $entry['ActionNumero'],
                    'Criticite' => $entry['Criticite'],
                    'chargeaction' => $entry['chargeaction'],
                    'charge' => $entry['charge'],
                    'tauxrealisation' => $entry['tauxrealisation'],
                    'Evaluation' => $entry['Evaluation'],
                    'projNum' => $entry['projNum'],
                ];
            }


            $flattenedData = [];
            foreach ($organizedData as $projectName => $entries) {
                foreach ($entries as $entry) {
                    $flattenedData[] = $entry;
                }
            }
            $templateProcessor->cloneRowAndSetValues('ProjectName', $flattenedData);

            for ($x = 0; $x < count($prevAuditArray) + 1; $x++) {

                if (isset($prevAuditArray[$x]['ProjectName'])) {
                    $projectName = $prevAuditArray[$x]['ProjectName'];
                    $templateProcessor->setValue("ProjectName#" . $x + 1, $projectName);
                } else {
                    error_log("Warning: 'ProjectName' key not found in entry at index $x");
                }
            }
        }




















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

        $networkDesignRow = $networkDesign[0] ?? null;
        $networkDesignValue = $networkDesignRow->Network_Design ?? "pas de network Design";

        $imagePath = $allImagesPath . DIRECTORY_SEPARATOR . $networkDesignValue;


        if ($networkDesignValue != "pas de network Design") {
            $templateProcessor->setImageValue('NetworkDesign', ['path' => $imagePath, 'width' => 500]);
        }
        //table:Postes de travail
        $posteTravail = DB::select($sqlPosteTravail, [$customerId]);
        $posteTravailArray = self::processDatabaseData($posteTravail);
        $templateProcessor->cloneRowAndSetValues('PC_SE', $posteTravailArray);

        //table:Infrastucture Réseau et sécurité 
        $Infrastructure = DB::select($sqlInfrastructure, [$customerId]);
        $InfrastructureArray = self::processDatabaseData($Infrastructure);
        $templateProcessor->cloneRowAndSetValues('Infra_Nature', $InfrastructureArray);



        //Table:serveur par plateforme
        $servers = DB::select($sqlServers, [$customerId]);
        $serverArray = self::processDatabaseData($servers);
        $templateProcessor->cloneRowAndSetValues('Srv_Name', $serverArray);


        //description du siege (Applications):

        $application = DB::select($sqlApplication, [$customerId]);
        $applicationArray = self::processDatabaseData($application);
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
            AnnexesController::sendMessage("Starting Generating Report for " . $SN . "\n Time now:" . date("Y-m-d H:i:s"));

            $LN = $firstRow->LN;
            $typeCompany = $firstRow->leType;

            $ActivitySector = $firstRow->SecteurActivite;
            $Categorie = $firstRow->Categorie;
            $siteWeb = $firstRow->Site_Web;
            $mailAddress = $firstRow->Adresse_mail;
            $description = $firstRow->Description;
            $organigrame = $firstRow->Organigramme ? $firstRow->Organigramme : " organigramme non disponible";
            $Logo = $firstRow->Logo ? $firstRow->Logo : " Logo non dispo";

            $templateProcessor->setValue('SN', $SN);
            $templateProcessor->setValue('LN', $LN);
            $templateProcessor->setValue('typeCompany', $typeCompany);
            $templateProcessor->setValue('SecteurActivite', $ActivitySector);
            $templateProcessor->setValue('Categorie', $Categorie);
            $templateProcessor->setValue('siteweb', $siteWeb);
            $templateProcessor->setValue('mailadress', $mailAddress);
            $templateProcessor->setValue('DescriptionCompany', $description);

            $logoPath = $allImagesPath . DIRECTORY_SEPARATOR . $Logo;

            if ($organigrame != "organigramme non disponible") {
                $organigramePath = $allImagesPath . DIRECTORY_SEPARATOR . $organigrame;


                if (!empty($organigramePath)) {
                    $templateProcessor->setImageValue('organigrame:800:800', array('path' => $organigramePath));
                }
            }


            $templateProcessor->setImageValue('icon', array('path' => $logoPath));
        } else {
            return response()->json("no customer with this id exists ");
        }

        $AllRows =  DB::select($sql, [$customerId]);
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
                // Call the setOneRowControl function for Best Practices
                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 1, "_BestPractice#");

                // Call the setOneRowControl function for Vulnerabilities
                self::setOneRowControl($templateProcessor, $ClauseId, $ControlID, $cellData, 0, "_Vuln#");

                // Check if both Best Practices and Vulnerabilities are empty

            }
        }

        for ($clause = 5; $clause <= 8; $clause++) {
            for ($control = 1; $control <= 40; $control++) {
                // Replace Best Practice placeholder
                $bestPracticePlaceholder = $clause . "_BestPractice#" . $control;
                $bestPracticeVariables = $templateProcessor->getVariables($bestPracticePlaceholder);
                if (!empty($bestPracticeVariables)) {
                    $templateProcessor->setValue($bestPracticePlaceholder, '');
                }

                // Replace Vulnerability placeholder
                $vulnPlaceholder = $clause . "_Vuln#" . $control;
                $vulnVariables = $templateProcessor->getVariables($vulnPlaceholder);
                if (!empty($vulnVariables)) {
                    $templateProcessor->setValue($vulnPlaceholder, '');
                }
            }
        }





        // return $allRowsAsArray;


        $templateProcessor->saveAs($outputPath);
        AnnexesController::sendMessage("Finishing Generating Report for " . $SN . "\n Time now:" . date("Y-m-d H:i:s"));

        // $pdfContent = self::ConvertPDF($outputPath);

        // if ($pdfContent) {
        //     return response($pdfContent)
        //         ->header('Content-Type', 'application/pdf')
        //         ->header('Content-Disposition', 'attachment; filename=output.pdf');
        // } else {
        //     abort(500, 'Failed to generate PDF');
        // }

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        // Set the Content-Disposition header to force download with a specific filename
        header('Content-Disposition: attachment;filename="ansi-2023.docx"');

        // Set the Content-Length header
        header('Content-Length: ' . filesize($outputPath));

        // Clear the output buffer
        ob_clean();

        // Read the file and output it to the browser
        readfile($outputPath);

        // Read the file and output it to the browser


    }

    static function getMaxProjNum($prevAuditArray)
    {
        $maxProjNum = isset($prevAuditArray[0]['projNum']) ? $prevAuditArray[0]['projNum'] : null;

        foreach ($prevAuditArray as $item) {
            if (isset($item['projNum']) && $item['projNum'] > $maxProjNum) {
                $maxProjNum = $item['projNum'];
            }
        }

        return $maxProjNum;
    }


    // function setOneRowProject($templateProcessor, $project) {
    //     // Extract project data
    //     $projectName = $project['ProjectName'];
    //     $actionNumero = $project['ActionNumero'];
    //     $projNum = $project['projNum'];
    //     $criticite = $project['Criticite'];
    //     $chargeAction = $project['chargeaction'];
    //     $charge = $project['charge'];
    //     $tauxRealisation = $project['tauxrealisation'];
    //     $evaluation = $project['Evaluation'];

    //     // Call setOneRowControl function for each project
    //     self::setOneRowControl($templateProcessor, $projectName, $actionNumero, $projNum, $criticite, $chargeAction, $charge, $tauxRealisation, $evaluation);
    // }



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
            return response()->download($outputPath, $filename, ['Content-Type' => $mimeType]);
        } else {
            // If the file doesn't exist in the source directory, return a 404 Not Found response
            dd("File does not exist at path: ");
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

    static function preparePagesDeGarde($templateProcessor,  $customer, $project)
    {


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

    static function processDatabaseData($data)
    {
        $result = [];
        $priorities = ["", "Très Urgent", "Urgent", "Normal"];

        foreach ($data as $item) {
            $modifiedItem = [];

            foreach ($item as $key => $value) {
                if ($key == "priorité") {
                    $modifiedItem[$key] = $priorities[$value];
                    $modifiedItem["Planification"] = "Fin de " . ($value + 2023);
                } else $modifiedItem[$key] = AnnexesController::cleanStrings($value,);
            }

            $result[] = $modifiedItem;
        }

        return $result;
    }
    static function currentDate()
    {
        $current_date = date('Y-m-d');

        return $current_date;
    }
    // private function setTableRowValues(Row $table, int $rowIndex, array $data)
    // {
    //     // Iterate through each cell of the row and set values
    //     foreach ($data as $columnName => $value) {
    //         $table->setValue("${columnName}_$rowIndex", $value);
    //     }
    // }


    public function getAnswersFromWebsiteServer($c)
    {
        $url = "https://smartskills.com.tn/wqkjsdhvj76vhbnc565ds/generateCsv.php?c=$c&e=qkljsdfqd25154dQDSFSDFQdv45q2dfqfDCX";

        $response = Http::get($url);
        $content = $response->body();

    
        // Save the modified content to a new file
        file_put_contents(storage_path('csv.csv'), $content);
        try {
            // Load data into the database
            $loadData = "LOAD DATA   INFILE '" . str_replace('/', '\\\\', str_replace('\\', '\\\\', storage_path('csv.csv'))) . "' IGNORE
                INTO TABLE rm_answers
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                ( `ID_Question`, `Answer`, `Commentaire`, `ID_ITERATION`, `Pertinence`);
            ";

            print_r($loadData);

            // Execute the database statement
            DB::statement($loadData);

            return response()->json(['message' => 'CSV file stored with success', 'success' => true]);
        } catch (\Throwable $th) {
            // Log or handle the exception
            return response()->json(['message' => 'Error: ' . $th->getMessage(), 'success' => false]);
        }
    }


    static function populateTemplate($templateProcessor, $rowPlaceholder, $dataArray)
    {
        if (!empty($dataArray)) {
            $templateProcessor->cloneRowAndSetValues($rowPlaceholder, $dataArray);
        }
    }



    static function processPA_chapter11($num, $iteration, $templateProcessor)
    {
        $sqlPA = "SELECT
     ROW_NUMBER() OVER () AS AP_" . $num . "_numero,
    rm_questions.`plan d'action` AS AP_$num,
    rm_questions.`priorité` as priorité,
    rm_questions.Responsable as Responsable,
    rm_questions.ChargeHJ as ChargeHJ
    FROM
    rm_questions
    JOIN
    standards_controls ON standards_controls.Controle_ID = rm_questions.`ISO 27002:2022`
    LEFT JOIN
    rm_answers ON rm_answers.ID_Question = rm_questions.QuestionID
    LEFT JOIN
    rm_iteration ON rm_answers.ID_ITERATION = rm_iteration.id
    WHERE
    rm_answers.Pertinence in(3, 4)
    AND rm_answers.Answer = 0
    AND rm_iteration.ID = '3'
    AND standards_controls.clause=$num
    ORDER BY
    standards_controls.Controle_ID  ASC;";
        $vuln = DB::select($sqlPA);
        $vulnArray = self::processDatabaseData($vuln);
        $vulnArrayLength = count($vulnArray);

        $templateProcessor->cloneRowAndSetValues('AP_' . $num, $vulnArray);
    }
}
