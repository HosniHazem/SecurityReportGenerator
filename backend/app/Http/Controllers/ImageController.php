<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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


}

