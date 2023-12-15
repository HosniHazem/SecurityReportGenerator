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
    public function parse(Request $request)
    {



        $htmlPath = $request->file('vuln');

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
            $vuln->save();
            $i++;
            if ($i == 29) break;
        }



        return response()->json(['message' => 'vuln names are stored succefully', 'success' => true]);
    }



    public function parseHcl(Request $request){
        $htmlPath = $request->file('vuln');

        $htmlContent = file_get_contents($htmlPath);

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);



        // Load the HTML from the file
        $dom->loadHTML($htmlContent);

        libxml_use_internal_errors(false);

        $xpath = new DOMXPath($dom);
        $query = "//div[@class='issueHeader']/div[@class='headerIssueType']";

        $node = $xpath->query($query);

        $array=[];

        foreach($node as $value){

            $array[]=$value->nodeValue;
        }


        return response()->json($array);






    }



    public static function removeNumbers($item)
    {
        $text = preg_replace('/^\s*\d+\.\s*/', '', $item);
        return $text;
    }
}
