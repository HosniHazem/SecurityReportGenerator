<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
use App\Models\Vuln;
use PHPUnit\Runner\Hook;
use Symfony\Component\HttpFoundation\Response;

class HtmlParser extends Controller
{
    public function parse(Request $request,$id)
    {



        $htmlPath = $request->file('vuln');
        $projectID=$id;

        $htmlContent = file_get_contents($htmlPath);

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);



        // Load the HTML from the file
        $dom->loadHTML($htmlContent);

        libxml_use_internal_errors(false);

        $xpath = new DOMXPath($dom);







        //description array
        $classes = ['criticals', 'highs', 'mediums', 'lows', 'bpractices', 'infos'];
        $descriptionArray = [];

        foreach ($classes as $class) {
            // Define the XPath query for each class excluding p elements after h3 with text "Impact"
            $queryDescription = "//div[@class='vuln-desc $class']/p[not(preceding-sibling::h3[1][text()='Impact'])]";

            // Execute the query
            $nodes = $xpath->query($queryDescription);

            $classContent = [];

            foreach ($nodes as $descriptionElement) {
                $description = $descriptionElement->nodeValue;
                $id = $descriptionElement->parentNode->getAttribute('id');

                // Check if an entry with the current id already exists
                if (isset($classContent[$id])) {
                    // Merge the content for the same id
                    $classContent[$id] .= ' ' . $description;
                } else {
                    // Create a new entry
                    $classContent[$id] = $description;
                }
            }

            // Add the class name and content array to the result
            $descriptionArray = array_merge_recursive($descriptionArray, $classContent);
        }














        $query = "//div[contains(@class, 'vuln-name')]//h2";
        $queryRisk = "//div[@class='sev-box']/div[@class='left']";
        $queryHost = "////div[@class='vuln']/div[@class='vuln-url']/a/@href";
        $querySolution = "//div[@class='row']/div[@class='col-md-8 col-sm-12 col-xs-12']";
        $queryRef = "//div[@class='row']/div[@class='col-md-8 col-sm-12 col-xs-12']//h4[(text()='Remedy References' or text()='External References')]/following-sibling::div/ul/li[1]/a/@href";






        // Now, $descriptionArray contains the content of <p> following the specified <div>


        // Execute the query
        $nodes = $xpath->query($query);
        $nodeRisk = $xpath->query($queryRisk);
        $nodeHost = $xpath->query($queryHost);
        $nodeSolution = $xpath->query($querySolution);
        $nodeRef = $xpath->query($queryRef);


        $namesArray = [];
        $riskArray = [];
        $hostArray = [];
        $solutionArray = [];
        $remArray = [];
        $refArray = [];


        //See Also

        foreach ($nodeRef as $node) {
            $refArray[] = $node->nodeValue;
        }




        //solution
        foreach ($nodeSolution as $node) {

            $solutionArray[] = $node->nodeValue;
        }

        for ($i = 0; $i < count($solutionArray); $i++) {
            preg_match('/Remedy(.*?)(Remedy References|External References)/s', $solutionArray[$i], $matches);

            if (isset($matches[1])) {
                $remArray[$i] = trim($matches[1]);
            }
        }






        // Assuming $solutionArray is an array



        // Output the results
        // echo "Remedy Array:\n";
        // print_r($remedyArray);

        // echo "\nReferences Array:\n";
        // print_r($referencesArray);









        foreach ($nodes as $node) {

            $namesArray[] = $node->nodeValue;
        }

        foreach ($nodeRisk as $singleNodeRisk) {


            $riskArray[] = ucfirst(strtolower($singleNodeRisk->nodeValue));
        }

        foreach ($nodeHost as $host) {
            $hostArray[] = ApiRequestController::parseBaseUrl($host->nodeValue);
        }

        // foreach ($nodeSolution as $solution) {
        //     $solutionArray[] = $solution->nodeValue;
        // }

        // return response()->json($solutionArray);

        $i = 0;

        foreach ($descriptionArray as $key => $value) {
            $vuln = new Vuln();

            $vuln->Name = self::removeNumbers($namesArray[$i]);
            $vuln->Risk = $riskArray[$i];
            $vuln->Description = $value;
            $vuln->Host = $hostArray[$i];
            if (isset($remArray[$i])) {
                $vuln->Solution = $remArray[$i];
            } else {
                $vuln->Solution = "";
            }
            if (isset($refArray[$i])) {
                $vuln['See Also'] = $refArray[$i];
            } else {
                $vuln['See Also'] = "";
            }
            $vuln->ID_Projet=$projectID;
            $vuln->save();
            $i++;
            if ($i == 29) break;
        }



        return response()->json(['message' => 'vuln names are stored succefully', 'success' => true,'number'=>$i]);
    }



    public function parseHcl(Request $request,$id)
    {
        $htmlPath = $request->file('vuln');
        $projectID=$id;

        $htmlContent = file_get_contents($htmlPath);

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);



        // Load the HTML from the file
        $dom->loadHTML($htmlContent);

        libxml_use_internal_errors(false);

        $xpath = new DOMXPath($dom);
        $results = [];

        // Extract data using XPath and loop through elements
        $issueNodes = $xpath->query("//div[@class='issueHeader']");
        foreach ($issueNodes as $issueNode) {
            $name = $xpath->query(".//div[@class='headerIssueType']/a", $issueNode)->item(0)->nodeValue;
            $severity = $xpath->query(".//div[@class='row'][div[@class='name']='Severity:']/div[@class='value']/span", $issueNode)->item(0)->nodeValue;
            $cvssScore = $xpath->query(".//div[@class='row'][div[@class='name']='CVSS Score:']/div[@class='value']", $issueNode)->item(0)->nodeValue;
            $url = $xpath->query(".//div[@class='row'][div[@class='name']='URL:']/div[@class='value wordBreaker']/a", $issueNode)->item(0)->nodeValue;

            $risk = $xpath->query(".//div[@class='row'][div[@class='name']='Risk:']/div[@class='value']/ul/li", $issueNode)->item(0)->nodeValue;
            $cause = $xpath->query(".//div[@class='row'][div[@class='name']='Cause:']/div[@class='value']/ul/li", $issueNode)->item(0)->nodeValue;
            $fix = $xpath->query(".//div[@class='row'][div[@class='name']='Fix:']/div[@class='value']/a", $issueNode)->item(0)->nodeValue;

            // Store the extracted data in an array
            $results[] = [
                //name
                'name' => $name,

                //risk
                'Severity' => $severity,
                //CVSSv3BaseScore
                'CVSS Score' => $cvssScore,
                //Host
                'URL' => ApiRequestController::parseBaseUrl($url),
                //Description

                'Description' => $risk . "and the cause is " . $cause,

                //Solution
                'Fix' => $fix,
            ];
        }

        // Output the extracted data
        $uniqueResults = array_map('unserialize', array_unique(array_map('serialize', $results)));

        // Now $uniqueResults contains the unique elements based on all sub-array values


        // return response()->json($results['name']);

        foreach ($uniqueResults as $result){
            $vuln=new Vuln();
            $vuln->Name=$result['name'];
            $vuln->Risk=$result['Severity'];
            $vuln->CVSSv3BaseScore=$result['CVSS Score'];
            $vuln->Host=$result['URL'];
            $vuln->Description=$result['Description'];
            $vuln->Solution=$result['Fix'];
            $vuln->ID_Projet=$projectID;


            $vuln->save();

        }

        return response()->json(['message'=>"vuln saved succeffully",'number'=>count($uniqueResults),'success'=>true]);


    }



    public static function removeNumbers($item)
    {
        $text = preg_replace('/^\s*\d+\.\s*/', '', $item);
        return $text;
    }
}
