<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use vendor\seblucas\tbszip;
use clsTbsZip;


class concatenateDocxFiles extends Controller
{
 
    
    public function mergeDocxFiles(Request $request)
    {
        $filePath = public_path('/storage/');
        $filesName = ['app/file0.docx','file1.docx', 'file2.docx', 'file3.docx', 'file4.docx', 'file5.docx', 'file6.docx', 'file7.docx']; // Replace with your file names
    
        $zip = new clsTbsZip();
        $content = [];
        $r = '';
    
        // Iterate through the DOCX files
        for ($i = 1; $i < count($filesName); $i++) {
            // Open the document
            $zip->Open($filePath . $filesName[$i]);
            $content[$i] = $zip->FileRead('word/document.xml');
            $zip->Close();
    
            // Extract the content of the document
            $p = strpos($content[$i], '<w:body');
            if ($p === false) {
                echo ("Tag <w:body> not found in document ." . $filesName[$i]);
            }
            $p = strpos($content[$i], '>', $p);
            $content[$i] = substr($content[$i], $p + 1);
            $p = strpos($content[$i], '</w:body>');
            if ($p === false) {
                echo ("Tag <w:body> not found in document ." . $filesName[$i]);
            }
            $content[$i] = substr($content[$i], 0, $p);
            $r .= $content[$i];
        }
    
        // Insert after the first document
        $zip->Open($filePath . $filesName[0]);
        $content2 = $zip->FileRead('word/document.xml');
        $p = strpos($content2, '</w:body>');
        if ($p === false) {
            echo ("Tag <w:body> not found in document ." . $filesName[0]);
        }
        $content2 = substr_replace($content2, $r, $p, 0);
        $zip->FileReplace('word/document.xml', $content2, TBSZIP_STRING);
    
        // Save the merged DOCX as 'merge.docx' in the storage folder
        $zip->Flush(TBSZIP_FILE, storage_path('app/merge.docx'));
    
        return response()->download(storage_path('app/merge.docx'))->deleteFileAfterSend();
    }
    

}
