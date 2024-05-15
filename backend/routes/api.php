<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WordDocumentController;
use App\Http\Controllers\WordDocumentController2;
use App\Http\Controllers\AnnexesController;
use App\Http\Controllers\WordDocumentController4;
use App\Http\Controllers\ExcelDocumentController;
use App\Http\Controllers\concatenateDocxFiles;
// use App\Http\Controllers\Sanctum\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\NassusController;
use App\Http\Controllers\NassusController2;

use App\Http\Controllers\MyController;
use App\Http\Controllers\VmController;
use App\Http\Controllers\UploadanomaliesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\GlbPipController;
use App\Http\Controllers\SowController;
use App\Http\Controllers\AuditPreviousAuditController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RmAnswerController;
use App\Http\Controllers\ApiRequestController;
use App\Http\Controllers\CloneController;
use App\Http\Controllers\HtmlParser;
use App\Http\Controllers\CustomerSitesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RmProcessusDomainsController;
use App\Models\RmProcessusDomains;

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
Route::get('Project', [ProjectController::class,'index']);
Route::get('/get_vm', [VmController::class,'index']);
Route::get('Customer', [CustomerController::class,'index']);

Route::get('/generate-ansi/{customerId}', [WordDocumentController4::class,'generateWordDocument']);

//Route::group(['middleware' => ['jwt.verify', 'log_activity']], function () {
    
Route::get('/DangerLocateSelectedPluginsCompliance', [AnnexesController::class,'DangerLocateSelectedPluginsCompliance']);
Route::get('/DangerCorrectPluginsAges', [AnnexesController::class,'DangerCorrectPluginsAges']);
Route::get('/populateOSDanger', [AnnexesController::class,'populateOSDanger']);
Route::get('/removeBadCharsFromDB', [AnnexesController::class,'removeBadCharsFromDB']);
Route::get('/executeCronJobs', [AnnexesController::class,'executeCronJobs']);
Route::get('/setAsExternal', [AnnexesController::class,'setAsExternal']);
Route::get('/cleanDescCompliance', [AnnexesController::class,'cleanDescCompliance']);
Route::get('/removeSpaceHOST_IP', [AnnexesController::class,'removeSpaceHOST_IP']);
Route::get('/markAsOutOfScope', [AnnexesController::class,'markAsOutOfScope']);
Route::get('/getPluginsFromAllServers', [AnnexesController::class,'getPluginsFromAllServers']);
//Route::get('/getPluginsFromAllServers', [NassusController2::class,'getPluginsFromAllServers']);
Route::post('/QualityCheck', [AnnexesController::class,'QualityCheck']);
Route::post('/generateExcelDocument', [AnnexesController::class,'generateExcelDocument']);
Route::get('/translatePlugins', [AnnexesController::class,'translateAllPlugins']);
Route::get('/translateVulns', [AnnexesController::class,'translateAllVulnsCompliance']);
    Route::get('/test2', [TestController::class,'get']);
    Route::get('/tet', [TestController::class,'test']);
    Route::get('/testit', [TestController::class,'updateIPHostInformation']);

    Route::get('/test', [TestController::class,'get2'])->middleware('web');
    

    Route::post('/generate-word-document', [WordDocumentController::class,'generateWordDocument']);
    Route::post('/generate-annexe', [WordDocumentController2::class,'generateWordDocument']);
    // Route::post('/generate-ansi', [WordDocumentController4::class,'generateWordDocument']);
    Route::post('/getAnnexes', [AnnexesController::class,'getAnnexes']);
    Route::get('/getAnnexes', [AnnexesController::class,'getAnnexes']);
    Route::get('/generate-concat', [concatenateDocxFiles::class,'mergeDocxFiles']);
///nessus1
    Route::post('/getScan', [NassusController::class,'GetAll']);
    Route::Post('/ExportAll', [NassusController::class,'ExportAll']);
    Route::Post('/ImportAll', [NassusController::class,'ImportAll']);
///nessus2
    Route::post('/getScan2', [NassusController2::class,'GetAll']);
    Route::Post('/ExportOne', [NassusController2::class,'ExportOne']);
    Route::Post('/ImportOne', [NassusController2::class,'ImportOne']);

    Route::Post('/uploadanomalie', [UploadanomaliesController::class,'store']);
    Route::Get('/getUpload', [UploadanomaliesController::class,'index']);
    Route::Get('/getProject', [UploadanomaliesController::class,'get']);


    Route::post('/imageProfil', [CustomerController::class, 'uploadimage']);

    Route::get('Project/{id}/show', [ProjectController::class,'show']);
    Route::get('LastOne', [ProjectController::class,'default']);
    Route::delete('Project/{id}/delete', [ProjectController::class,'destroy']);
    Route::put('Project/{id}/update', [ProjectController::class,'update']);
    Route::put('Project/{id}/updateQuality', [ProjectController::class,'updateQuality']);
    Route::post('Project/create',[ProjectController::class,'store']);

    Route::get('Customer/{id}/show', [CustomerController::class,'show']);
    Route::get('LastOne', [CustomerController::class,'default']);
    Route::delete('Customer/{id}/delete', [CustomerController::class,'destroy']);
    Route::post('Customer/{id}/update', [CustomerController::class,'update']);
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
    Route::post('/update-glbPip/{id}', [GlbPipController::class,'update']);
    Route::delete('/delete-glbPip/{id}', [GlbPipController::class,'destroy']);
    Route::get('/all-glbpip', [GlbPipController::class,'index']);
    Route::get('/get-glbpip-by-customer-id/{customerId}', [GlbPipController::class,'getGlbPipByProjectId']);


    Route::get('/all-audit-previous-audits', [AuditPreviousAuditController::class, 'index']);
    Route::post('/add-audit-previous-audits', [AuditPreviousAuditController::class, 'store']);
    Route::get('/get-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'show']);
    Route::put('/update-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'update']);
    Route::delete('/delete-audit-previous-audits/{id}', [AuditPreviousAuditController::class, 'destroy']);
    Route::get('/get-audit-previous-audits-by-projectID/{projectID}', [AuditPreviousAuditController::class, 'getauditPrevAuditByProjectId']);


    Route::Post('/Uploadfile', [ImageController::class, 'uploadimage']);

    Route::get('Customer/getProjectByCustomerId/{customerId}',[CustomerController::class,'getProjectByCustomerId']);
    
    //test
    Route::get('/download-file/{filename}', [WordDocumentController4::class,'downloadFile']);

    Route::post('/create-iteration', [RmAnswerController::class, 'CreateIteration']);

    Route::get('/get-all-questions', [RmAnswerController::class, 'getAllQuestions']);
    Route::post('/answer-a-question', [RmAnswerController::class, 'associateResponseWithQuestion']);


    Route::get('/Insert-Into-Answers/{c}', [WordDocumentController4::class,'getAnswersFromWebsiteServer']);
  
    Route::get('/vmtype',[VmController::class,'getAccunetixAndOwaszap']);
    Route::get('/all-tables',[CloneController::class,'getTables']);
    Route::post('/all-attributes',[CloneController::class,'getTableAttributes']);
    Route::put('/modify',[CloneController::class,'Modify']);
    Route::delete('/delete-row',[CloneController::class,'DeleteRow']);
    Route::post('/add-customersite', [CustomerSitesController::class, 'createCustomerSite']);
    Route::get('all-customerSites',[CustomerSitesController::class,'index']);
    Route::get('/customer-sites/{id}', [CustomerSitesController::class, 'show']);
    Route::post('/customer-sites/{id}', [CustomerSitesController::class, 'update']);
    Route::delete('/customer-sites/{id}', [CustomerSitesController::class, 'destroy']);
    Route::get('/customer-sites-by-customer-id/{id}', [CustomerSitesController::class, 'getCustomerSiteByCustomerId']);
    Route::post('/add-processus-domain', [RmProcessusDomainsController::class, 'create']);

    Route::get('/rm-processus-domains', [RmProcessusDomainsController::class, 'index']);
    Route::post('/rm-processus-domains', [RmProcessusDomainsController::class, 'create']);
    Route::get('/rm-processus-domains/{id}', [RmProcessusDomainsController::class, 'show']);
    Route::post('/update-rm-processus-domains/{id}', [RmProcessusDomainsController::class, 'update']);
    Route::delete('/rm-processus-domains/{id}', [RmProcessusDomainsController::class, 'destroy']);
    Route::get('/rm-processus-domains/getRmProccessByIterationID/{idIteration}', [RmProcessusDomainsController::class, 'getRmProccessByIterationID']);
    Route::post('/create-user', [AuthController::class, 'createUser']);

//});

Route::post('/get-vuln', [ApiRequestController::class, 'index']);
Route::get('/get-vulns', [ApiRequestController::class, 'getVulns']);
Route::post('/owaszap', [ApiRequestController::class, 'fillWithOWasZap']);
Route::post('/vuln-from-html/{id}',[HtmlParser::class,'parse']);
Route::post('/vuln-from-hcl/{id}',[HtmlParser::class,'parseHcl']);

Route::get('/all-logs',[ActivityLogController::class,'index']);

    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
        // Registration route
        Route::post('/register', [AuthController::class, 'register']);
        
        // Login route
        Route::post('/login', [AuthController::class, 'login']);
        // Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    
    
    });


