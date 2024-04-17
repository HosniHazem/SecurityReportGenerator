<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{

    public function uploadimage(Request $request)
    {

       $file      = $request->file('attach');
       $filename  = $file->getClientOriginalName();
       $extension = $file->getClientOriginalExtension();
       $picture   = $filename;
       //move image to public/img folder
       $file->move(public_path('doc'), $picture);
       return response()->json(["message" => "Image Uploaded Succesfully",'status' => 200]);
    }

    public function show($partialFilename)
    {
        // Get all files in the directory
        $files = scandir(public_path('images/uploads'));
    
        // Search for the file that contains the partial filename
        foreach ($files as $filename) {
            if (strpos($filename, $partialFilename) !== false) {
                // If found, construct the full path
                $path = public_path('images/uploads/' . $filename);
    
                // Return the image file with proper MIME type
                $file = file_get_contents($path);
                $type = mime_content_type($path);
    
                return response()->make($file, 200, ['Content-Type' => $type]);
            }
        }
    
        // If file not found, return 404 error
        abort(404);
    }
    
}

