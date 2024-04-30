<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Action;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\InspectionMember;
use App\Models\InspectionQuestionResponse;
use App\Models\InspectionSchedule;
use App\Models\InspectionTemplate;
use App\Models\InspectionTemplateQuestions;
use App\Models\InspectionTemplateType;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = Inspection::where('is_del', 'no');

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                    $query->orWhere('recipient_organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('inspection Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('inspection Fetched Successfully', []);

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

            if (!$inspection = Inspection::where('uuid', request('inspection_id'))->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            return successResponse('Inspection Fetched Successfully', $inspection);

        } catch (Exception $error) {

            return successResponse('Inspection Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'recipient_organization_id' => ['required', 'string'],
            // 'selectedType' => ['required', 'string'],
            'templateId' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'inspectionTemplateTypeId' => ['required', 'integer'],
            'location' => ['required', 'string'],
            'description' => ['required', 'string'],
            'objective' => ['required', 'string'],
            'ppe' => ['required', 'string'],
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

            if (!$inspection_template_type = InspectionTemplateType::where('id', $data['inspectionTemplateTypeId'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template Type");
            }

            if (!$inspection_template = InspectionTemplate::where('id', $data['templateId'])->where('inspection_template_type_id', $inspection_template_type->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection Template");
            }

            $inspection = Inspection::create([
                'user_id' => auth()->user()->id,
                'organization_id' => $organization->id,
                'recipient_organization_id' => $recipient_organization->id,
                'inspection_type_id' => $inspection_template_type->id,
                'inspection_template_id' => $inspection_template->id,
                'facility_name' => $data['name'],
                'location' => $data['location'],
                'description' => $data['description'],
                'objective' => $data['objective'],
                'ppe' => $data['ppe'],
                'start_date' => now(),
                'status' => $organization->id == $recipient_organization->id ? 'accepted' : 'pending',
            ]);

            logAction(auth()->user()->email, 'You started an Inspection ', 'Start Inspection', $request->ip());

            return successResponse('Inspection started Successfully', $inspection);

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
    public function inspectors()
    {

        if (!$inspection = Inspection::where('uuid', request('inspection_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
        }

        $organization = Organisation::where('id', $inspection->organization_id)->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function inspectees()
    {

        if (!$inspection = Inspection::where('uuid', request('inspection_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
        }

        $organization = Organisation::where('id', $inspection->recipient_organization_id)->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setInspectors(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'lead_inspector' => ['required'],
            'team_members' => ['required', 'array'],
            'team_members.*' => ['required'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            // if ($inspection->recipient_organization_id == $inspection->organization_id && $lead_representative = InspectionMember::where('inspection_id', $inspection->id)->where('position', 'LEAD_REPRESENTATIVE')->where('user_id', $data['lead_inspector'])->first()) {

            //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Cant be both lead auditor and lead representative");

            // }

            if ($lead_inspector = User::where('id', $data['lead_inspector'])->first()) {

                InspectionMember::updateOrCreate(
                    ['inspection_id' => $inspection->id, 'position' => 'LEAD_INSPECTOR'],
                    [
                        'organization_id' => $organization->id,
                        'inspection_id' => $inspection->id,
                        'user_id' => $lead_inspector->id,
                        'position' => 'LEAD_INSPECTOR',
                    ]);

                if ($lean_member = InspectionMember::where('inspection_id', $inspection->id)->where('position', 'INSPECTION_MEMBER')->where('user_id', $lead_inspector->user_id)->first()) {

                    $lean_member->delete();

                }

            }

            $team_members = request('team_members');

            foreach ($team_members as $team_member) {

                if ($member = User::where('id', $team_member)->first()) {

                    InspectionMember::updateOrCreate(
                        ['inspection_id' => $inspection->id, 'position' => 'INSPECTION_MEMBER', 'user_id' => $member->id],
                        [
                            'organization_id' => $organization->id,
                            'inspection_id' => $inspection->id,
                            'user_id' => $member->id,
                            'position' => 'INSPECTION_MEMBER',
                        ]);

                    if ($lean_member = InspectionMember::where('inspection_id', $inspection->id)->where('position', 'LEAD_INSPECTOR')->where('user_id', $member->id)->first()) {

                        $lean_member->delete();

                    }

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
    public function setRepresentatives(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'lead_representative' => ['required'],
            'team_representatives' => ['required', 'array'],
            'team_representatives.*' => ['required'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->recipient_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform Action");
            }

            // if ($inspection->recipient_organization_id == $inspection->organization_id && $lead_representative = InspectionMember::where('inspection_id', $inspection->id)->where('position', 'LEAD_INSPECTOR')->where('user_id', $data['lead_representative'])->first()) {

            //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Cant be both lead auditor and lead representative");
            // }

            if ($lead_representative = User::where('id', $data['lead_representative'])->first()) {

                InspectionMember::updateOrCreate(
                    ['inspection_id' => $inspection->id, 'position' => 'LEAD_REPRESENTATIVE'],
                    [
                        'organization_id' => $organization->id,
                        'inspection_id' => $inspection->id,
                        'user_id' => $lead_representative->id,
                        'position' => 'LEAD_REPRESENTATIVE',
                    ]);

                if ($lean_member = InspectionMember::where('inspection_id', $inspection->id)->where('position', 'REPRESENTATIVE')->where('user_id', $lead_representative->user_id)->first()) {

                    $lean_member->delete();

                }

            }

            $team_representatives = request('team_representatives');

            foreach ($team_representatives as $team_representative) {

                if ($member = User::where('id', $team_representative)->first()) {

                    InspectionMember::updateOrCreate(
                        ['inspection_id' => $inspection->id, 'user_id' => $member->id, 'position' => 'REPRESENTATIVE'],
                        [
                            'organization_id' => $organization->id,
                            'inspection_id' => $inspection->id,
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeMember(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'team_member' => ['required'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }
            // ERROR ANY OWNER OF ORGANIZATION CAN DELETE A MEMBER
            // IT IS SUPPOSE TO BE FOR THEIR INDIVIDUAL ORGANIZATION
            if ($member = InspectionMember::where('inspection_id', $inspection->id)->where('user_id', request('team_member'))->where('organization_id', $organization->id)->first()) {

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function proceed(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'accepted')->where('declare_proceed', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            $inspection->declare_proceed = 'yes';
            $inspection->status = 'ongoing';
            $inspection->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Inspection Can Proceed Successfully', []);

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

    public function proposeTime(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->whereIn('status', ['ongoing', 'accepted'])->where('declare_proceed', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_inspector->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            $start_time = Carbon::create(request('start_date'));

            if ($start_time < now()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Start Date Cant be less than present time");
            }

            if ($inspection->schedule) {
                if ($inspection->schedule->is_accepted) {
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
                }
            }

            InspectionSchedule::updateOrCreate(
                ['inspection_id' => $inspection->id],
                [
                    'organization_id' => $organization->id,
                    'recipient_organization_id' => $inspection->recipient_organization_id,
                    'inspection_id' => $inspection->id,
                    'user_id' => auth()->user()->id,
                    'propose_time' => $start_time,
                    'comment' => request('comment'),
                    'status' => 'sent',
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Inspection Time to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    public function actionTime(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:accepted,declined'],
            'suggested_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->whereIn('status', ['ongoing', 'accepted'])->where('declare_proceed', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            if (!$organization = Organisation::where('id', $inspection->recipient_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_representative->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            $suggested_time = request('suggested_time') ? Carbon::create(request('suggested_time')) : null;
            if ($suggested_time) {
                if ($suggested_time < now()) {
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Start Date Cant be less than present time");
                }
            }

            if ($inspection->schedule) {
                if ($inspection->schedule->is_accepted) {
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
                }
            }

            InspectionSchedule::updateOrCreate(
                ['inspection_id' => $inspection->id],
                [
                    'inspection_id' => $inspection->id,
                    'recipient_user_id' => auth()->user()->id,
                    'suggested_time' => $suggested_time,
                    'status' => request('status'),
                    'accepted_time' => request('status') == 'accepted' ? $inspection->schedule->propose_time : null,
                    'comment' => request('comment') ? request('comment') : $inspection->schedule->comment,
                ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Inspection Schedule Sent to organization successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function show(Inspection $inspection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function edit(Inspection $inspection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $data = $request->validate([
            'inspection_id' => ['required', 'string'],
            // 'selectedType' => ['required', 'string'],
            'name' => ['required', 'string'],
            'location' => ['required', 'string'],
            'description' => ['required', 'string'],
            'objective' => ['required', 'string'],
            'ppe' => ['required', 'string'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            $whoShouldUpdate = null;

            if ($inspection->recipient_organization_id == $inspection->organization_id) {
                $whoShouldUpdate = 'inspector';
            } else {
                $whoShouldUpdate = 'representative';
            }

            $canUpdate = false;

            if ($whoShouldUpdate == 'inspector' && auth()->user()->id == $inspection->lead_inspector->user_id) {
                $canUpdate = true;
            }

            if ($whoShouldUpdate == 'representative' && auth()->user()->id == $inspection->lead_representative->user_id) {
                $canUpdate = true;
            }
            // logger(!$canUpdate);
            // logger($inspection->can_proceed);

            if (!$canUpdate || $inspection->can_proceed) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You cant update this information");
            }

            $inspection->facility_name = $data['name'];
            $inspection->location = $data['location'];
            $inspection->description = $data['description'];
            $inspection->objective = $data['objective'];
            $inspection->ppe = $data['ppe'];
            $inspection->save();

            logAction(auth()->user()->email, "$whoShouldUpdate updated Inspection ", 'Update Inspection', $request->ip());

            return successResponse('Inspection updated Successfully', $inspection);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    public function actionSendResponse(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'inspection_question_id' => ['required'],
            'response' => ['required', 'in:yes,na,nc_minor,nc_major'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'ongoing')->where('declare_proceed', 'yes')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_inspector->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$inspection_template = InspectionTemplate::where('id', $inspection->inspection_template_id)->where('inspection_template_type_id', $inspection->inspection_type_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            if (!$inspection_question = InspectionTemplateQuestions::where('id', $data['inspection_question_id'])->where('inspection_template_id', $inspection_template->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Question");
            }

            InspectionQuestionResponse::updateOrCreate(
                ['inspection_id' => $inspection->id, 'inspection_question_id' => $inspection_question->id],
                [
                    'recipient_organization_id' => $inspection->recipient_organization_id,
                    'organization_id' => $organization->id,
                    'inspection_id' => $inspection->id,
                    'inspection_question_id' => $inspection_question->id,
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
            'inspection_id' => ['required', 'string'],
            'inspection_question_id' => ['required'],
            'comment' => ['required'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'ongoing')->where('declare_proceed', 'yes')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_inspector->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$inspection_template = InspectionTemplate::where('id', $inspection->inspection_template_id)->where('inspection_template_type_id', $inspection->inspection_type_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            if (!$inspection_question = InspectionTemplateQuestions::where('id', $data['inspection_question_id'])->where('inspection_template_id', $inspection_template->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Question");
            }

            InspectionQuestionResponse::updateOrCreate(
                ['inspection_id' => $inspection->id, 'inspection_question_id' => $inspection_question->id],
                [
                    'recipient_organization_id' => $inspection->recipient_organization_id,
                    'organization_id' => $organization->id,
                    'inspection_id' => $inspection->id,
                    'inspection_question_id' => $inspection_question->id,
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

    public function sendFinding(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'detail' => ['required', 'string'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'ongoing')->where('declare_proceed', 'yes')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_inspector->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            InspectionFinding::updateOrCreate(
                ['inspection_id' => $inspection->id], [
                    'user_id' => auth()->user()->id,
                    'inspection_id' => $inspection->id,
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
    public function sendRecommendation(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'assignee_id' => ['required', 'string'],
            'priority_id' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'ongoing')->where('declare_proceed', 'yes')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit");
            }

            if (!$organization = Organisation::where('id', $inspection->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($inspection->lead_inspector->user_id != auth()->user()->id) {
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
                'inspection_id' => $inspection->id,
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
            'inspection_id' => ['required', 'string'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'ongoing')->where('declare_proceed', 'yes')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $inspection->organization_id || $inspection->lead_inspector->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (!$inspection->is_completed()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You can't close inspection until all steps are completed");
            }

            $inspection->end_date = now();
            $inspection->status = 'completed';
            $inspection->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Inspection Closed Successfully', []);

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
    public function actionInspection(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'inspection_id' => ['required', 'string'],
            'action' => ['required', 'in:accepted,rejected'],
            'reason' => ['nullable', 'string'],
        ]);

        try {

            if (!$inspection = Inspection::where('uuid', $data['inspection_id'])->where('end_date', null)->where('status', 'pending')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->id != $inspection->recipient_organization_id || Organisation::owner($organization)->id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unauthorized Action");
            }

            if (request('action') == 'rejected' && !request('reason')) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Reason is Required");
            }

            $inspection->status = request('action');
            $inspection->status_reason = request('reason') ?? null;
            $inspection->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Action Sent to organization successfully', []);

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
    public function completed()
    {
        try {

            $stats = [];

            if (!$inspection = Inspection::where('uuid', request('inspection_id'))->where('end_date', '!=', null)->where('status', 'completed')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Inspection");
            }
            $total_questions = 0;
            $number_of_yeses = 0;
            $number_of_nc_minor = 0;
            $number_of_nc_major = 0;
            $number_of_na = 0;

            $questions = $inspection->questions;

            foreach ($questions as $title_question) {
                $title_questions = $title_question->questions;
                foreach ($title_questions as $question) {
                    ++$total_questions;

                    if ($question->response->answer == 'yes') {
                        ++$number_of_yeses;
                    }

                    if ($question->response->answer == 'nc_minor') {
                        ++$number_of_nc_minor;
                    }

                    if ($question->response->answer == 'nc_major') {
                        ++$number_of_nc_major;
                    }

                    if ($question->response->answer == 'na') {
                        ++$number_of_na;
                    }

                }

            }

            $stats['inspection'] = $inspection;
            $stats['stats'] = [];
            $stats['stats']['total_questions'] = $total_questions;
            $stats['stats']['number_of_yeses'] = $number_of_yeses;
            $stats['stats']['number_of_nc_minor'] = $number_of_nc_minor;
            $stats['stats']['number_of_nc_major'] = $number_of_nc_major;
            $stats['stats']['number_of_na'] = $number_of_na;
            $stats['stats']['maximum_score'] = $number_of_yeses + $number_of_nc_minor + $number_of_nc_major;
            $stats['stats']['inspection_score'] = round(((($number_of_yeses - ($number_of_nc_minor + $number_of_nc_major)) * 100) / ($number_of_yeses + $number_of_nc_minor + $number_of_nc_major)), 2);
            if ($stats['stats']['inspection_score'] < 59) {
                $stats['stats']['inspection_rate'] = 'Fail';
            }
            if ($stats['stats']['inspection_score'] < 69) {
                $stats['stats']['inspection_rate'] = 'Poor';
            }
            if ($stats['stats']['inspection_score'] > 69) {
                $stats['stats']['inspection_rate'] = 'Good';
            }
            if ($stats['stats']['inspection_score'] > 79) {
                $stats['stats']['inspection_rate'] = 'Excellent';
            }

            $stats['stats']['start_onsite'] = 32323;

            return successResponse('inspection Fetched Successfully', $stats);

        } catch (Exception $error) {

            return successResponse('inspection Fetched Successfully', []);

        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inspection $inspection)
    {
        //
    }
}
