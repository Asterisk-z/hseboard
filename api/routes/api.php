<?php

use App\Http\Controllers\AccountRoleController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuditDocumentController;
use App\Http\Controllers\AuditOptionController;
use App\Http\Controllers\AuditTemplateController;
use App\Http\Controllers\AuditTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\MainAuditController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ObservationTypeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OrganizationFeatureController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('validate/account', [AuthController::class, 'validateAccount']);

    Route::prefix('reset')->group(function () {
        Route::post('/initiate', [AuthController::class, 'forgotPassword']);
        Route::post('/otp', [AuthController::class, 'validateForgotPasswordOtp'])->middleware('throttle:10,5');
        Route::post('/complete', [AuthController::class, 'resetPassword'])->middleware('passwordReset');
    });

});

Route::get('account-types', [AccountTypeController::class, 'index']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('industries', [IndustryController::class, 'index']);
Route::get('account-roles', [AccountRoleController::class, 'index']);

Route::middleware('auth:api')->group(function ($router) {

    Route::prefix('auth')->group(function ($router) {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::get('observation-types', [ObservationTypeController::class, 'index']);
    Route::get('priorities', [PriorityController::class, 'index']);

    Route::prefix('offers')->group(function ($router) {
        Route::get('/', [OfferController::class, 'index']);
        Route::post('send', [OfferController::class, 'store']);
        Route::post('action-offer', [OfferController::class, 'action']);
    });

    Route::prefix('organizations')->group(function ($router) {
        Route::get('/', [OrganisationController::class, 'index']);
        Route::get('/{organization_id}', [OrganisationController::class, 'show']);
        Route::get('/org_token/{org_token}/{uuid}', [OrganisationController::class, 'show_token']);
        Route::post('/remove-user', [OrganisationController::class, 'removeUser']);
    });

    Route::prefix('users')->group(function ($router) {
        Route::get('/all/{organization_id}', [UsersController::class, 'index']);
        Route::get('/except/{organization_id}', [UsersController::class, 'indexExcept']);
        Route::get('/{organization_id}/{user_id}', [UsersController::class, 'show']);
        Route::post('/create', [UsersController::class, 'store']);
        Route::post('/update', [UsersController::class, 'update']);
    });

    Route::prefix('observations')->group(function ($router) {
        Route::get('/all/{organization_id?}', [ObservationController::class, 'index']);
        Route::post('/create', [ObservationController::class, 'store']);
        Route::post('/update', [ObservationController::class, 'update']);
        Route::post('/delete', [ObservationController::class, 'delete']);
    });

    Route::prefix('actions')->group(function ($router) {
        Route::get('/all/{organization_id?}', [ActionController::class, 'index']);
        Route::post('/create', [ActionController::class, 'store']);
        Route::post('/update', [ActionController::class, 'update']);
        Route::post('/delete', [ActionController::class, 'delete']);
        Route::post('/status-update', [ActionController::class, 'changeStatus']);
    });

    Route::prefix('statistic')->group(function ($router) {
        Route::get('/all/{organization_id?}', [StatisticsController::class, 'index']);
        Route::post('/create', [StatisticsController::class, 'store']);
        Route::post('/delete', [StatisticsController::class, 'delete']);
    });

    Route::prefix('investigations')->group(function ($router) {
        Route::get('/all/{organization_id?}', [InvestigationController::class, 'index']);
        Route::post('/start', [InvestigationController::class, 'start']);
        Route::post('/member/{organization_id?}', [InvestigationController::class, 'setTeamMembers']);
        Route::post('/remove-member/{organization_id?}', [InvestigationController::class, 'removeMember']);
        Route::post('/send-questions/{organization_id?}', [InvestigationController::class, 'sendQuestions']);
        Route::post('/send-invites/{organization_id?}', [InvestigationController::class, 'sendInvites']);
        Route::post('/recommendation/{organization_id?}', [InvestigationController::class, 'recommendation']);
        Route::post('/report/{organization_id?}', [InvestigationController::class, 'sendReport']);
        Route::post('/complete/{organization_id?}', [InvestigationController::class, 'complete']);
        Route::post('/send-findings/{organization_id?}', [InvestigationController::class, 'sendFindings']);
        Route::get('/questions/{observation_id?}', [InvestigationController::class, 'getQuestions']);
        Route::post('/question/{investigation_id?}', [InvestigationController::class, 'setQuestions']);
        Route::get('/show/{observation_id}', [InvestigationController::class, 'show']);

        Route::post('/create', [InvestigationController::class, 'store']);
        Route::post('/delete', [InvestigationController::class, 'delete']);
    });

    Route::prefix('audit-documents')->group(function ($router) {
        Route::get('/all/{organization_id?}', [AuditDocumentController::class, 'index']);
        Route::post('/upload', [AuditDocumentController::class, 'store']);
        Route::post('/delete', [AuditDocumentController::class, 'delete']);
    });
    Route::prefix('audit-templates')->group(function ($router) {
        Route::get('/all/{organization_id?}/{type_id?}', [AuditTemplateController::class, 'index']);
        Route::get('/single/{organization_id?}/{type_id?}', [AuditTemplateController::class, 'single']);
        Route::post('/upload', [AuditTemplateController::class, 'store']);
        Route::post('/delete', [AuditTemplateController::class, 'delete']);
    });

    Route::prefix('audit-types')->group(function ($router) {
        Route::get('/all', [AuditTypeController::class, 'index']);
    });

    Route::prefix('audit-options')->group(function ($router) {
        Route::get('/all', [AuditOptionController::class, 'index']);
    });

    Route::prefix('main-audit')->group(function ($router) {
        Route::get('/all/{organization_id?}', [MainAuditController::class, 'index']);
        Route::get('/ongoing/{main_audit_id}', [MainAuditController::class, 'ongoing']);
        Route::get('/completed/{main_audit_id}', [MainAuditController::class, 'completed']);
        Route::post('/start', [MainAuditController::class, 'store']);
        Route::get('/auditors/{main_audit_id}', [MainAuditController::class, 'auditors']);
        Route::get('/auditees/{main_audit_id}', [MainAuditController::class, 'auditees']);
        Route::post('/action/{organization_id?}', [MainAuditController::class, 'actionAudit']);
        Route::post('/set-auditors/{organization_id?}', [MainAuditController::class, 'setAuditors']);
        Route::post('/set-auditees/{organization_id?}', [MainAuditController::class, 'setAuditees']);
        Route::post('/remove-member/{organization_id?}', [MainAuditController::class, 'removeMember']);
        Route::post('/request-document/{organization_id?}', [MainAuditController::class, 'requestDocument']);
        Route::post('/send-document/{organization_id?}', [MainAuditController::class, 'sendDocument']);
        Route::post('/revert-document/{organization_id?}', [MainAuditController::class, 'actionRevertDocument']);
        Route::post('/remove-document/{organization_id?}', [MainAuditController::class, 'actionRemoveDocument']);
        Route::post('/action-document/{organization_id?}', [MainAuditController::class, 'actionAuditDocument']);
        Route::post('/auditor-time/{organization_id?}', [MainAuditController::class, 'actionAuditorTime']);
        Route::post('/auditee-time/{organization_id?}', [MainAuditController::class, 'actionAuditeeTime']);
        Route::post('/accept-time/{organization_id?}', [MainAuditController::class, 'actionAcceptedTime']);
        Route::post('/send-response/{organization_id?}', [MainAuditController::class, 'actionSendResponse']);
        Route::post('/send-comment/{organization_id?}', [MainAuditController::class, 'actionSendComment']);
        Route::post('/send-finding/{organization_id?}', [MainAuditController::class, 'sendAuditFinding']);
        Route::post('/send-recommendation/{organization_id?}', [MainAuditController::class, 'sendAuditRecommendation']);
        Route::post('/complete/{organization_id?}', [MainAuditController::class, 'complete']);

        Route::post('/create', [InvestigationController::class, 'store']);
        Route::post('/delete', [InvestigationController::class, 'delete']);
    });

    Route::prefix('billing')->group(function ($router) {
        Route::post('/initiate/{organization_id?}', [SubscriptionController::class, 'initiate']);
        Route::get('/all-transactions/{organization_id?}', [SubscriptionController::class, 'index']);
        Route::get('/organization-features/{organization_id?}', [OrganizationFeatureController::class, 'index']);
        Route::get('/plans/{organization_id?}', [SubscriptionController::class, 'plans']);
        Route::get('/features/{organization_id?}', [FeatureController::class, 'features']);
        Route::get('/currencies/{organization_id?}', [CurrencyController::class, 'index']);

        // Route::get('/ongoing/{main_audit_id}', [MainAuditController::class, 'ongoing']);
        // Route::get('/completed/{main_audit_id}', [MainAuditController::class, 'completed']);
        // Route::post('/start', [MainAuditController::class, 'store']);
        // Route::get('/auditors/{main_audit_id}', [MainAuditController::class, 'auditors']);
        // Route::get('/auditees/{main_audit_id}', [MainAuditController::class, 'auditees']);
        // Route::post('/action/{organization_id?}', [MainAuditController::class, 'actionAudit']);
        // Route::post('/set-auditors/{organization_id?}', [MainAuditController::class, 'setAuditors']);
        // Route::post('/set-auditees/{organization_id?}', [MainAuditController::class, 'setAuditees']);
        // Route::post('/remove-member/{organization_id?}', [MainAuditController::class, 'removeMember']);
        // Route::post('/request-document/{organization_id?}', [MainAuditController::class, 'requestDocument']);
        // Route::post('/send-document/{organization_id?}', [MainAuditController::class, 'sendDocument']);
        // Route::post('/revert-document/{organization_id?}', [MainAuditController::class, 'actionRevertDocument']);
        // Route::post('/remove-document/{organization_id?}', [MainAuditController::class, 'actionRemoveDocument']);
        // Route::post('/action-document/{organization_id?}', [MainAuditController::class, 'actionAuditDocument']);
        // Route::post('/auditor-time/{organization_id?}', [MainAuditController::class, 'actionAuditorTime']);
        // Route::post('/auditee-time/{organization_id?}', [MainAuditController::class, 'actionAuditeeTime']);
        // Route::post('/accept-time/{organization_id?}', [MainAuditController::class, 'actionAcceptedTime']);
        // Route::post('/send-response/{organization_id?}', [MainAuditController::class, 'actionSendResponse']);
        // Route::post('/send-comment/{organization_id?}', [MainAuditController::class, 'actionSendComment']);
        // Route::post('/send-finding/{organization_id?}', [MainAuditController::class, 'sendAuditFinding']);
        // Route::post('/send-recommendation/{organization_id?}', [MainAuditController::class, 'sendAuditRecommendation']);
        // Route::post('/complete/{organization_id?}', [MainAuditController::class, 'complete']);

        // Route::post('/create', [InvestigationController::class, 'store']);
        // Route::post('/delete', [InvestigationController::class, 'delete']);
    });

});

Route::get('billing/verify/{organization_id?}', [SubscriptionController::class, 'verify'])->name('verify_payment');

Route::any('/{any}', function ($router) {
    return response()->json([
        'status' => '404 Not Found',
        'statusCode' => '404',
    ], 404);
});
