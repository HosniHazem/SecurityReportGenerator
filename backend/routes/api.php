<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WordDocumentController;
use App\Http\Controllers\WordDocumentController2;
use App\Http\Controllers\WordDocumentController3;
use App\Http\Controllers\WordDocumentController4;
use App\Http\Controllers\ExcelDocumentController;
use App\Http\Controllers\concatenateDocxFiles;
use App\Http\Controllers\Sanctum\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\NassusController;

use App\Http\Controllers\MyController;
use App\Http\Controllers\VmController;
use App\Http\Controllers\UploadanomaliesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\GlbPipController;
use App\Http\Controllers\SowController;
use App\Http\Controllers\AuditPreviousAuditController;
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
Route::get('/getPluginsFromAllServers', [NassusController::class,'getPluginsFromAllServers']);
Route::post('/QualityCheck', [WordDocumentController3::class,'QualityCheck']);
Route::post('/generateExcelDocument', [WordDocumentController3::class,'generateExcelDocument']);
Route::get('/translatePlugins', [WordDocumentController3::class,'translateAllPlugins']);
Route::get('/translateVulns', [WordDocumentController3::class,'translateAllVulnsCompliance']);
    Route::get('/test2', [TestController::class,'get']);

    Route::get('/get_vm', [VmController::class,'index']);
    Route::post('/generate-word-document', [WordDocumentController::class,'generateWordDocument']);
    Route::post('/generate-annexe', [WordDocumentController2::class,'generateWordDocument']);
    Route::post('/generate-ansi', [WordDocumentController4::class,'generateWordDocument']);
    Route::post('/generate-annexe3', [WordDocumentController3::class,'generateWordDocument']);
    Route::get('/generate-annexe3', [WordDocumentController3::class,'generateWordDocument']);
    Route::get('/generate-concat', [concatenateDocxFiles::class,'mergeDocxFiles']);
    Route::post('/getScan', [NassusController::class,'GetAll']);
    Route::Post('/ExportAll', [NassusController::class,'ExportAll']);
    Route::Post('/ImportAll', [NassusController::class,'ImportAll']);

    Route::Post('/uploadanomalie', [UploadanomaliesController::class,'store']);
    Route::Get('/getUpload', [UploadanomaliesController::class,'index']);
    Route::Get('/getProject', [UploadanomaliesController::class,'get']);


    Route::post('/imageProfil', [CustomerController::class, 'uploadimage']);

    Route::get('Project/{id}/show', [ProjectController::class,'show']);
    Route::get('Project', [ProjectController::class,'index']);
    Route::get('LastOne', [ProjectController::class,'default']);
    Route::delete('Project/{id}/delete', [ProjectController::class,'destroy']);
    Route::put('Project/{id}/update', [ProjectController::class,'update']);
    Route::post('Project/create',[ProjectController::class,'store']);

    Route::get('Customer/{id}/show', [CustomerController::class,'show']);
    Route::get('Customer', [CustomerController::class,'index']);
    Route::get('LastOne', [CustomerController::class,'default']);
    Route::delete('Customer/{id}/delete', [CustomerController::class,'destroy']);
    Route::put('Customer/{id}/update', [CustomerController::class,'update']);
    Route::post('Customer/create',[CustomerController::class,'store']);

    /// SOw
    Route::get('Sow/{id}/show', [SowController::class,'show']);
    Route::get('Sow', [SowController::class,'index']);
    Route::get('LastOne', [SowController::class,'default']);
    Route::delete('Sow/{id}/delete', [SowController::class,'destroy']);
    Route::put('Sow/{id}/update', [SowController::class,'update']);
    Route::post('Sow/create',[SowController::class,'store']);
    Route::post('Sow/import',[SowController::class,'multiple']);





    Route::Post('/add-glbPip', [GlbPipController::class,'store']);
    Route::get('/get-glbPip/{id}', [GlbPipController::class,'show']);
    Route::put('/update-glbPip/{id}', [GlbPipController::class,'update']);
    Route::delete('/delete-glbPip/{id}', [GlbPipController::class,'destroy']);
    Route::get('/all-glbpip', [GlbPipController::class,'index']);

    Route::get('/all-audit-previous-audits', [AuditPreviousAuditController::class, 'index']);
    Route::post('/add-audit-previous-audits', [AuditPreviousAuditController::class, 'store']);
    Route::get('/get-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'show']);
    Route::put('/update-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'update']);
    Route::delete('/delete-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'destroy']);

