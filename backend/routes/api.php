<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WordDocumentController;
use App\Http\Controllers\concatenateDocxFiles;
use App\Http\Controllers\Sanctum\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\NassusController;

use App\Http\Controllers\MyController;
use App\Http\Controllers\VmController;
use App\Http\Controllers\UploadanomaliesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




    Route::get('/get_vm', [VmController::class,'index']);
    Route::get('/generate-word-document/{id}', [WordDocumentController::class,'generateWordDocument']);
    Route::get('/generate-concat', [concatenateDocxFiles::class,'mergeDocxFiles']);
    Route::get('/getScan', [NassusController::class,'GetAll']);
    Route::Post('/ExportAll', [NassusController::class,'ExportAll']);
    Route::Post('/ImportAll', [NassusController::class,'ImportAll']);

    Route::Post('/uploadanomalie', [UploadanomaliesController::class,'store']);
    Route::Get('/getUpload', [UploadanomaliesController::class,'index']);
    Route::Get('/getProject', [UploadanomaliesController::class,'get']);




