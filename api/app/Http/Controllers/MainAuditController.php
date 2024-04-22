<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Action;
use App\Models\AuditTemplate;
use App\Models\AuditTemplateQuestion;
use App\Models\AuditType;
use App\Models\MainAudit;
use App\Models\MainAuditDocument;
use App\Models\MainAuditFinding;
use App\Models\MainAuditMember;
use App\Models\MainAuditQuestionResponse;
use App\Models\MainAuditSchedule;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class MainAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = MainAudit::where('is_del', 'no');

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                    $query->orWhere('recipient_organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Audit Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Audit Fetched Successfully', []);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function auditors()
    {

        if (!$main_audit = MainAudit::where('uuid', request('main_audit_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
        }

        $organization = Organisation::where('id', $main_audit->organization_id)->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function auditees()
    {

        if (!$main_audit = MainAudit::where('uuid', request('main_audit_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
        }

        $organization = Organisation::where('id', $main_audit->recipient_organization_id)->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'recipient_organization_id' => ['required', 'string'],
            'audit_type_id' => ['required', 'integer'],
            'audit_template_id' => ['required', 'integer'],
            'audit_scope' => ['required', 'string'],
            'audit_title' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }
            if (!$recipient_organization = Organisation::where('uuid', $data['recipient_organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Recipient Organization");
            }

            if (!$audit_type = AuditType::where('id', $data['audit_type_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Type");
            }

            if (!$audit_template = AuditTemplate::where('id', $data['audit_template_id'])->where('audit_type_id', $data['audit_type_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Template");
            }

            $main_audit = MainAudit::create([
                'user_id' => auth()->user()->id,
                'organization_id' => $organization->id,
                'recipient_organization_id' => $recipient_organization->id,
                'audit_type_id' => $audit_type->id,
                'audit_template_id' => $audit_template->id,
                'audit_scope' => $data['audit_scope'],
                'audit_title' => $data['audit_title'],
                'start_date' => now(),
                'status' => $organization->id == $recipient_organization->id ? 'accepted' : 'pending',
            ]);

            logAction(auth()->user()->email, 'You started an Audit ', 'Start Audit', $request->ip());

            return successResponse('Audit started Successfully', $main_audit);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ongoing()
    {
        try {

            $stats = [];

            if (!$main_audit = MainAudit::where('uuid', request('main_audit_id'))->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            return successResponse('Audit Fetched Successfully', $main_audit);

        } catch (Exception $error) {

            return successResponse('Audit Fetched Successfully', []);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function completed()
    {
        try {

            $stats = [];

            if (!$main_audit = MainAudit::where('uuid', request('main_audit_id'))->where('end_date', '!=', null)->where('status', 'completed')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            $stats['main_audit'] = $main_audit;

            return successResponse('Audit Fetched Successfully', $stats);

        } catch (Exception $error) {

            return successResponse('Audit Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setAuditors(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'lead_auditor' => ['required'],
            'support_auditor' => ['required'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($main_audit->recipient_organization_id == $main_audit->organization_id && $lead_representative = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'LEAD_REPRESENTATIVE')->where('user_id', $data['lead_auditor'])->first()) {
                // $lead_auditor = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'LEAD_AUDITOR')->where('user_id', $data['lead_auditor'])->first();
                // $lead_representative = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'LEAD_REPRESENTATIVE')->where('user_id', $data['lead_auditor'])->first();

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Cant be both lead auditor and lead representative");

            }

            if ($lead_auditor = User::where('id', $data['lead_auditor'])->first()) {

                MainAuditMember::updateOrCreate(
                    ['audit_id' => $main_audit->id, 'position' => 'LEAD_AUDITOR'],
                    [
                        'organization_id' => $organization->id,
                        'audit_id' => $main_audit->id,
                        'user_id' => $lead_auditor->id,
                        'position' => 'LEAD_AUDITOR',
                    ]);

                if ($lean_member = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'SUPPORT_AUDITOR')->where('user_id', $lead_auditor->user_id)->first()) {

                    $lean_member->delete();

                }

            }

            if ($support_auditor = User::where('id', $data['support_auditor'])->first()) {

                MainAuditMember::updateOrCreate(
                    ['audit_id' => $main_audit->id, 'position' => 'SUPPORT_AUDITOR'],
                    [
                        'organization_id' => $organization->id,
                        'audit_id' => $main_audit->id,
                        'user_id' => $support_auditor->id,
                        'position' => 'SUPPORT_AUDITOR',
                    ]);

                if ($lean_member = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'LEAD_AUDITOR')->where('user_id', $support_auditor->id)->first()) {

                    $lean_member->delete();

                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Team Added Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeMember(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'team_member' => ['required'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($member = MainAuditMember::where('audit_id', $main_audit->id)->where('user_id', request('team_member'))->where('organization_id', $organization->id)->first()) {

                $member->delete();

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Team Removed Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setAuditees(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'lead_representative' => ['required'],
            'team_representatives' => ['required', 'array'],
            'team_representatives.*' => ['required'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->recipient_organization_id || Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($main_audit->recipient_organization_id == $main_audit->organization_id && $lead_representative = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'LEAD_AUDITOR')->where('user_id', $data['lead_representative'])->first()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Cant be both lead auditor and lead representative");

            }

            if ($lead_representative = User::where('id', $data['lead_representative'])->first()) {

                MainAuditMember::updateOrCreate(
                    ['audit_id' => $main_audit->id, 'position' => 'LEAD_REPRESENTATIVE'],
                    [
                        'organization_id' => $organization->id,
                        'audit_id' => $main_audit->id,
                        'user_id' => $lead_representative->id,
                        'position' => 'LEAD_REPRESENTATIVE',
                    ]);

                if ($lean_member = MainAuditMember::where('audit_id', $main_audit->id)->where('position', 'REPRESENTATIVE')->where('user_id', $lead_representative->user_id)->first()) {

                    $lean_member->delete();

                }

            }

            $team_representatives = request('team_representatives');

            foreach ($team_representatives as $team_representative) {

                if ($member = User::where('id', $team_representative)->first()) {

                    MainAuditMember::updateOrCreate(
                        ['audit_id' => $main_audit->id, 'user_id' => $member->id, 'position' => 'REPRESENTATIVE'],
                        [
                            'organization_id' => $organization->id,
                            'audit_id' => $main_audit->id,
                            'user_id' => $member->id,
                            'position' => 'REPRESENTATIVE',
                        ]);

                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Team Added Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function actionAudit(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'action' => ['required', 'in:accepted,rejected'],
            'reason' => ['nullable', 'string'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'pending')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->recipient_organization_id || Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (request('action') == 'rejected' && !request('reason')) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Reason is Required");
            }

            $main_audit->status = request('action');
            $main_audit->status_reason = request('reason') ?? null;
            $main_audit->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Sent to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestDocument(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            $document = MainAuditDocument::create([
                "user_id" => auth()->user()->id,
                "organization_id" => $organization->id,
                "recipient_organization_id" => $main_audit->recipient_organization_id,
                "audit_id" => $main_audit->id,
                "title" => request('title'),
                "description" => request('description'),
            ]);

            $main_audit->status = 'ongoing';
            $main_audit->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Document Sent to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendDocument(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_document_id' => ['required', 'integer'],
            'recipient_comment' => ['required', 'string'],
            // 'files' => ['required', 'array'],
            // 'files.*' => ['required'],
            'file' => ['required'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->recipient_organization_id || $main_audit->lead_representative->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$main_audit_document = MainAuditDocument::where('id', $data['main_audit_document_id'])->where('audit_id', $main_audit->id)->whereIn('status', ['rejected', 'pending'])->where('recipient_organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Document");
            }

            $main_audit_document->recipient_user_id = auth()->user()->id;
            $main_audit_document->recipient_comment = request('recipient_comment');
            $main_audit_document->status = 'uploaded';
            $main_audit_document->save();

            $main_audit->status = 'ongoing';
            $main_audit->save();

            storeMedia(request('file'), MainAuditDocument::class, $main_audit_document->id, 'audit_documents');

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Document Sent to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function actionAuditDocument(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_document_id' => ['required', 'integer'],
            'action' => ['required', 'in:accepted,rejected'],
            'reason' => ['nullable', 'string'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$main_audit_document = MainAuditDocument::where('id', $data['main_audit_document_id'])->where('audit_id', $main_audit->id)->where('status', 'uploaded')->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Document");
            }

            if (request('action') == 'rejected' && !request('reason')) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Reason is Required");
            }

            $main_audit_document->status = request('action');
            $main_audit_document->user_comment = request('reason') ?? null;

            if (request('action') == 'rejected') {
                $main_audit_document->recipient_user_id = null;
                $main_audit_document->recipient_comment = null;

                $main_audit_document->media->delete();
            }

            $main_audit_document->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Sent to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function actionRevertDocument(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_document_id' => ['required', 'integer'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$main_audit_document = MainAuditDocument::where('id', $data['main_audit_document_id'])->where('audit_id', $main_audit->id)->whereIn('status', ['pending', 'rejected'])->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Document");
            }

            $main_audit_document->is_del = 'yes';
            $main_audit_document->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Delte to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function actionRemoveDocument(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_document_id' => ['required', 'integer'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->recipient_organization_id || $main_audit->lead_representative->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$main_audit_document = MainAuditDocument::where('id', $data['main_audit_document_id'])->where('audit_id', $main_audit->id)->where('status', 'uploaded')->where('recipient_organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Document");
            }

            $main_audit_document->recipient_user_id = null;
            $main_audit_document->recipient_comment = null;

            $main_audit_document->status = 'pending';
            $main_audit_document->save();
            $main_audit_document->media->delete();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Delte to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function actionAuditorTime(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }
            logger($organization->id);
            logger($main_audit->organization_id);
            logger($main_audit->lead_auditor);
            logger(auth()->user()->id);
            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }
            $start_time = Carbon::create(request('start_date'));

            if ($start_time < now()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Start Date Cant be less than present time");
            }

            MainAuditSchedule::updateOrCreate(
                ['audit_id' => $main_audit->id],
                [
                    'organization_id' => $organization->id,
                    'audit_id' => $main_audit->id,
                    'user_id' => auth()->user()->id,
                    'auditor_time' => $start_time,
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Audit Time to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    public function actionAuditeeTime(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->recipient_organization_id || $main_audit->lead_representative->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            $start_time = Carbon::create(request('start_date'));

            if ($start_time < now()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Start Date Cant be less than present time");
            }

            MainAuditSchedule::updateOrCreate(
                ['audit_id' => $main_audit->id],
                [
                    'recipient_organization_id' => $organization->id,
                    'audit_id' => $main_audit->id,
                    'recipient_user_id' => auth()->user()->id,
                    'recipient_time' => $start_time,
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Delte to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    public function actionAcceptedTime(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'action' => ['required', 'in:auditor,recipient'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$schedule = MainAuditSchedule::where('audit_id', $main_audit->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!$schedule->recipient_time || !$schedule->auditor_time) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Date not Confirmed by Organization");
            }

            $schedule->accepted_time = request('action') == 'auditor' ? $schedule->auditor_time : $schedule->auditor_time;
            $schedule->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Delte to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    public function actionSendResponse(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_question_id' => ['required'],
            'response' => ['required', 'in:yes,na,nc_minor,nc_major'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$audit_template = AuditTemplate::where('id', $main_audit->audit_template_id)->where('audit_type_id', $main_audit->audit_type_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            if (!$audit_question = AuditTemplateQuestion::where('id', $data['main_audit_question_id'])->where('audit_template_id', $audit_template->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Question");
            }

            MainAuditQuestionResponse::updateOrCreate(
                ['audit_id' => $main_audit->id, 'audit_question_id' => $audit_question->id],
                [
                    'recipient_organization_id' => $main_audit->recipient_organization_id,
                    'organization_id' => $organization->id,
                    'audit_id' => $main_audit->id,
                    'audit_question_id' => $audit_question->id,
                    'user_id' => auth()->user()->id,
                    'response' => request('response'),
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    public function actionSendComment(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'main_audit_question_id' => ['required'],
            'comment' => ['required'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$audit_template = AuditTemplate::where('id', $main_audit->audit_template_id)->where('audit_type_id', $main_audit->audit_type_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            if (!$audit_question = AuditTemplateQuestion::where('id', $data['main_audit_question_id'])->where('audit_template_id', $audit_template->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Question");
            }

            MainAuditQuestionResponse::updateOrCreate(
                ['audit_id' => $main_audit->id, 'audit_question_id' => $audit_question->id],
                [
                    'recipient_organization_id' => $main_audit->recipient_organization_id,
                    'organization_id' => $organization->id,
                    'audit_id' => $main_audit->id,
                    'audit_question_id' => $audit_question->id,
                    'user_id' => auth()->user()->id,
                    'comment' => request('comment'),
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    public function sendAuditFinding(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'detail' => ['required', 'string'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            MainAuditFinding::updateOrCreate(
                ['main_audit_id' => $main_audit->id], [
                    'user_id' => auth()->user()->id,
                    'main_audit_id' => $main_audit->id,
                    'description' => request('detail'),
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Findings Sent Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendAuditRecommendation(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'assignee_id' => ['required', 'string'],
            'priority_id' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            // if ($organization->user_id != auth()->user()->id) {
            //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            // }

            if (!$user = User::where('uuid', $data['assignee_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Not Found");
            }

            if (!$find_user_in_organization = Organisation::find_user($organization, $user->email)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is not in Organization");
            }

            Action::create([
                'user_id' => auth()->user()->id,
                'organization_id' => $organization->id,
                'assignee_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'start_datetime' => Carbon::create($request->start_date),
                'end_datetime' => Carbon::create($request->end_date),
                'priority_id' => $request->priority_id,
                'audit_id' => $main_audit->id,
            ]);

            logAction(auth()->user()->email, 'You created an action ', 'Action Create', $request->ip());
            // SENDMAIL

            logAction($user->email, 'An action was sent to you ', 'Create Action', $request->ip());
            // SENDMAIl

            return successResponse('Recommendations Created Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'main_audit_id' => ['required', 'string'],
        ]);

        try {

            if (!$main_audit = MainAudit::where('uuid', $data['main_audit_id'])->where('end_date', null)->where('status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $main_audit->organization_id || $main_audit->lead_auditor->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$main_audit->is_completed()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You can't close audit until all steps are completed");
            }

            $main_audit->end_date = now();
            $main_audit->status = 'completed';
            $main_audit->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Audit Closed Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // try {

        //     $stats = [];

        //     if (!$observation = Observation::where('uuid', request('observation_id'))->first()) {
        //         return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
        //     }

        //     if (!$investigation = Investigation::where('observation_id', $observation->id)->where('end_date', null)->first()) {
        //         return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is no running investigation");
        //     }

        //     return successResponse('Investigation Fetched Successfully', $investigation);

        // } catch (Exception $error) {

        //     return successResponse('Investigation Fetched Successfully', []);

        // }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
