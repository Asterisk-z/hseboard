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
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionTemplateController;
use App\Http\Controllers\InspectionTemplateTypeController;
use App\Http\Controllers\InspectionTypeController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\JobHazardAnalysisController;
use App\Http\Controllers\JobHazardAnalysisStepController;
use App\Http\Controllers\MainAuditController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ObservationTypeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OrganizationFeatureController;
use App\Http\Controllers\PermitRequestTypeController;
use App\Http\Controllers\PermitToWorkController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ProfileController;
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
        Route::post('reset-token', [AuthController::class, 'resetToken']);
        Route::post('update-details', [AuthController::class, 'updateDetail']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('profile')->group(function ($router) {
        Route::post('set-organization', [ProfileController::class, 'setOrganization']);
        // Route::post('refresh', [AuthController::class, 'refresh']);
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
        Route::get('/users/{uuid}', [OrganisationController::class, 'users']);
        Route::post('/update-details', [OrganisationController::class, 'updateDetails']);
        Route::post('/remove-user', [OrganisationController::class, 'removeUser']);
    });

    Route::prefix('users')->group(function ($router) {
        Route::get('/all', [UsersController::class, 'index']);
        Route::get('/except', [UsersController::class, 'indexExcept']);
        Route::get('/show/{user_id}', [UsersController::class, 'show']);
        Route::post('/create', [UsersController::class, 'store']);
        Route::post('/verify-email', [UsersController::class, 'verify']);
        Route::post('/send-message', [UsersController::class, 'sendMessage']);
        Route::post('/update', [UsersController::class, 'update']);
    });

    Route::prefix('observations')->group(function ($router) {
        Route::get('/all', [ObservationController::class, 'index']);
        Route::post('/create', [ObservationController::class, 'store']);
        Route::post('/update', [ObservationController::class, 'update']);
        Route::post('/delete', [ObservationController::class, 'delete']);
    });

    Route::prefix('actions')->group(function ($router) {
        Route::get('/all', [ActionController::class, 'index']);
        Route::post('/create', [ActionController::class, 'store']);
        Route::post('/update', [ActionController::class, 'update']);
        Route::post('/delete', [ActionController::class, 'delete']);
        Route::post('/status-update', [ActionController::class, 'changeStatus']);
    });

    Route::prefix('statistic')->group(function ($router) {
        Route::get('/all', [StatisticsController::class, 'index']);
        Route::post('/create', [StatisticsController::class, 'store']);
        Route::post('/delete', [StatisticsController::class, 'delete']);
    });

    Route::prefix('investigations')->group(function ($router) {
        Route::get('/all', [InvestigationController::class, 'index']);
        Route::post('/start', [InvestigationController::class, 'start']);
        Route::post('/external-member', [InvestigationController::class, 'setExternalTeamMembers']);
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
        Route::get('/completed/{investigation_id}', [InvestigationController::class, 'completed']);

        Route::post('/create', [InvestigationController::class, 'store']);
        Route::post('/delete', [InvestigationController::class, 'delete']);
    });

    Route::prefix('audit-documents')->group(function ($router) {
        Route::get('/all', [AuditDocumentController::class, 'index']);
        Route::post('/upload', [AuditDocumentController::class, 'store']);
        Route::post('/delete', [AuditDocumentController::class, 'delete']);
    });
    Route::prefix('audit-templates')->group(function ($router) {
        Route::get('/all/{type_id?}', [AuditTemplateController::class, 'index']);
        Route::get('/single/{type_id?}', [AuditTemplateController::class, 'single']);
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
        Route::get('/all', [MainAuditController::class, 'index']);
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
        Route::post('/initiate', [SubscriptionController::class, 'initiate']);
        Route::get('/all-transactions/{organization_id?}', [SubscriptionController::class, 'index']);
        Route::get('/organization-features/{organization_id?}', [OrganizationFeatureController::class, 'index']);
        Route::get('/plans/{organization_id?}', [SubscriptionController::class, 'plans']);
        Route::get('/features/{organization_id?}', [FeatureController::class, 'features']);
        Route::get('/currencies/{organization_id?}', [CurrencyController::class, 'index']);
    });

    Route::prefix('inspection')->group(function ($router) {
        Route::get('/types', [InspectionTypeController::class, 'index']);
        Route::get('/template-types', [InspectionTemplateTypeController::class, 'index']);
        Route::get('/templates-type/{organization_id?}/{type_id?}', [InspectionTemplateController::class, 'index_type']);
        Route::post('/initiate', [InspectionController::class, 'store']);
        Route::get('/all/{organization_id?}', [InspectionController::class, 'index']);
        Route::get('/ongoing/{inspection_id}', [InspectionController::class, 'ongoing']);
        Route::post('/update-base', [InspectionController::class, 'update']);
        Route::get('/inspectors/{inspection_id}', [InspectionController::class, 'inspectors']);
        Route::get('/inspectees/{inspection_id}', [InspectionController::class, 'inspectees']);
        Route::post('/set-inspectors/{organization_id?}', [InspectionController::class, 'setInspectors']);
        Route::post('/set-representatives/{organization_id?}', [InspectionController::class, 'setRepresentatives']);
        Route::post('/remove-member/{organization_id?}', [InspectionController::class, 'removeMember']);
        Route::post('/proceed/{organization_id?}', [InspectionController::class, 'proceed']);
        Route::post('/propose-time/{organization_id?}', [InspectionController::class, 'proposeTime']);
        Route::post('/action-time/{organization_id?}', [InspectionController::class, 'actionTime']);
        Route::post('/send-response/{organization_id?}', [InspectionController::class, 'actionSendResponse']);
        Route::post('/send-comment/{organization_id?}', [InspectionController::class, 'actionSendComment']);
        Route::post('/send-finding/{organization_id?}', [InspectionController::class, 'sendFinding']);
        Route::post('/send-recommendation/{organization_id?}', [InspectionController::class, 'sendRecommendation']);
        Route::post('/complete/{organization_id?}', [InspectionController::class, 'complete']);
        Route::post('/action/{organization_id?}', [InspectionController::class, 'actionInspection']);
        Route::get('/completed/{inspection_id}', [InspectionController::class, 'completed']);

        Route::post('/create', [InvestigationController::class, 'store']);
        Route::post('/delete', [InvestigationController::class, 'delete']);
    });

    Route::prefix('jha')->group(function ($router) {
        Route::get('/all', [JobHazardAnalysisController::class, 'index']);
        Route::get('/ongoing/{job_id?}', [JobHazardAnalysisController::class, 'ongoing']);
        Route::get('/review/{job_id?}', [JobHazardAnalysisController::class, 'review']);
        Route::post('/createJob', [JobHazardAnalysisController::class, 'createJob']);
        Route::post('/add-step', [JobHazardAnalysisStepController::class, 'addStep']);
        Route::post('/remove-step', [JobHazardAnalysisStepController::class, 'removeStep']);
        Route::post('/add-event', [JobHazardAnalysisStepController::class, 'topEvent']);
        Route::post('/add-source', [JobHazardAnalysisStepController::class, 'hazardSource']);
        Route::post('/add-target', [JobHazardAnalysisStepController::class, 'target']);
        Route::post('/add-consequence', [JobHazardAnalysisStepController::class, 'consequence']);
        Route::post('/add-action', [JobHazardAnalysisStepController::class, 'preventiveAction']);
        Route::post('/add-rcp', [JobHazardAnalysisStepController::class, 'rcp']);
        Route::post('/add-recovery', [JobHazardAnalysisStepController::class, 'recoveryMeasure']);
        Route::post('/add-party', [JobHazardAnalysisStepController::class, 'actionParty']);
        Route::post('/delete-item', [JobHazardAnalysisStepController::class, 'deleteItem']);
        Route::post('/complete', [JobHazardAnalysisController::class, 'complete']);
        Route::post('/action', [JobHazardAnalysisController::class, 'actionStatus']);

        Route::post('/create', [InvestigationController::class, 'store']);
        Route::post('/delete', [InvestigationController::class, 'delete']);
    });

    Route::prefix('permit-to-work')->group(function ($router) {
        Route::get('/types', [PermitTypeController::class, 'index']);
        Route::get('/request-types', [PermitRequestTypeController::class, 'index']);
        Route::get('/all', [PermitToWorkController::class, 'index']);
        Route::get('/ongoing/{organization_id}/{permit_id?}', [PermitToWorkController::class, 'ongoing']);
        Route::get('/holding-members/{permit_id}', [PermitToWorkController::class, 'holdings']);
        Route::get('/active-jha', [PermitToWorkController::class, 'activeJHA']);

        // Route::get('/review/{organization_id}/{job_id?}', [JobHazardAnalysisController::class, 'review']);
        Route::post('/create-permit', [PermitToWorkController::class, 'createPermit']);
        Route::post('/update-permit', [PermitToWorkController::class, 'updatePermit']);
        Route::post('/set-members/{organization_id?}', [PermitToWorkController::class, 'setMembers']);
        Route::post('/remove-member/{organization_id?}', [PermitToWorkController::class, 'removeMember']);
        Route::post('/send-jha/{organization_id?}', [PermitToWorkController::class, 'sendJHA']);
        Route::post('/action-jha/{organization_id?}', [PermitToWorkController::class, 'actionJHA']);
        Route::post('/action-request-form/{organization_id?}', [PermitToWorkController::class, 'actionRequestForm']);
        Route::post('/action-declaration/{organization_id?}', [PermitToWorkController::class, 'sendForDeclaration']);
        Route::post('/send-declaration/{organization_id?}', [PermitToWorkController::class, 'sendDeclaration']);
        Route::post('/send-issue-permit/{organization_id?}', [PermitToWorkController::class, 'issuePermit']);
        Route::post('/suspend-permit/{organization_id?}', [PermitToWorkController::class, 'suspendPermit']);
        Route::post('/reactivate-permit/{organization_id?}', [PermitToWorkController::class, 'reactivatePermit']);
        Route::post('/send-permit-request/{organization_id?}', [PermitToWorkController::class, 'sendPermitRequest']);
        Route::post('/action-permit-request/{organization_id?}', [PermitToWorkController::class, 'actionPermitRequest']);

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
