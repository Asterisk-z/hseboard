<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\JobHazardAnalysis;
use App\Models\Organisation;
use App\Models\PermitRequestType;
use App\Models\PermitToWork;
use App\Models\PermitToWorkActionRequest;
use App\Models\PermitToWorkMember;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PermitToWorkController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createPermit(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'recipient_organization_id' => ['required', 'string'],
            'permit_request_id' => ['required', 'integer'],
            'job_code' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'location_id' => ['required', 'string'],
            'contractor_name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'request_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!Organisation::find_user($organization, auth()->user()->email)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$recipient_organization = Organisation::where('uuid', $data['recipient_organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Recipient Organization");
            }

            if (!$request_type = PermitRequestType::where('id', $data['permit_request_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Type");
            }

            $permit = PermitToWork::create([
                'issuer_organization_id' => $recipient_organization->id,
                'issuer_id' => $recipient_organization->user_id,
                'holder_organization_id' => $organization->id,
                'holder_id' => auth()->user()->id,
                'permit_to_work_request_type_id' => $request_type->id,
                'job_code' => $data['job_code'],
                'contractor_name' => $data['contractor_name'],
                'job_title' => $data['job_title'],
                'description_of_work' => $data['description'],
                'location_name' => $data['location'],
                'location_id_no' => $data['location_id'],
                'work_start_time' => Carbon::create($data['start_date']),
                'work_end_time' => Carbon::create($data['end_date']),
                'requested_date' => Carbon::create($data['request_date']),
                'request_status' => $organization->id == $recipient_organization->id ? 'accepted' : 'pending',

            ]);

            logAction(auth()->user()->email, 'You started a Permit ', 'Start Permit', $request->ip());

            return successResponse('Permit started Successfully', $permit);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePermit(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'job_code' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'location_id' => ['required', 'string'],
            'contractor_name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'request_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!$permit = PermitToWork::where('uuid', $data['permit_id'])->where('holder_organization_id', $organization->id)->where('process_status', 'ongoing')->where('request_status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            $permit->job_code = $data['job_code'];
            $permit->contractor_name = $data['contractor_name'];
            $permit->job_title = $data['job_title'];
            $permit->description_of_work = $data['description'];
            $permit->location_name = $data['location'];
            $permit->location_id_no = $data['location_id'];
            $permit->work_start_time = Carbon::create($data['start_date']);
            $permit->work_end_time = Carbon::create($data['end_date']);
            $permit->requested_date = Carbon::create($data['request_date']);
            $permit->save();

            logAction(auth()->user()->email, 'You started a Permit ', 'Start Permit', $request->ip());

            return successResponse('Permit started Successfully', $permit);

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
    public function holdings()
    {
        if (!$permit = PermitToWork::where('uuid', request('permit_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
        }

        $organization = Organisation::where('id', $permit->holder_organization_id)->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);
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

            if (!$permit = PermitToWork::where('uuid', request('permit_id'))->where('request_status', 'accepted')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find permit");
            }

            return successResponse('Permit Fetched Successfully', $permit);

        } catch (Exception $error) {

            return successResponse('Permit Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setMembers(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'supervisor' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->holder_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if ($permit_to_work->request_type->is_confined_space) {

                $data = $request->validate([
                    'organization_id' => ['required', 'string'],
                    'permit_id' => ['required', 'string'],
                    'supervisor' => ['required'],
                    'entrants' => ['required', 'array'],
                    'entrants.*' => ['required'],
                    'attendants' => ['required', 'array'],
                    'attendants.*' => ['required'],
                ]);

                if ($entry_supervisor = User::where('id', $data['supervisor'])->first()) {

                    PermitToWorkMember::updateOrCreate(
                        ['permit_to_work_id' => $permit_to_work->id, 'position' => 'ENTRY_SUPERVISOR'],
                        [
                            // 'organization_id' => $organization->id,
                            'permit_to_work_id' => $permit_to_work->id,
                            'member_id' => $entry_supervisor->id,
                            'position' => 'ENTRY_SUPERVISOR',
                        ]);

                    if ($team_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['ENTRANT', 'ATTENDANT'])->where('member_id', $entry_supervisor->id)->first()) {

                        $team_member->delete();

                    }

                }
                $entrants = request('entrants');

                foreach ($entrants as $team_member) {

                    if ($member = User::where('id', $team_member)->first()) {

                        PermitToWorkMember::updateOrCreate(
                            ['permit_to_work_id' => $permit_to_work->id, 'position' => 'ENTRANT', 'member_id' => $member->id],
                            [
                                // 'organization_id' => $organization->id,
                                'permit_to_work_id' => $permit_to_work->id,
                                'member_id' => $member->id,
                                'position' => 'ENTRANT',
                            ]);

                        if ($lean_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['ENTRY_SUPERVISOR', 'ATTENDANT'])->where('member_id', $member->id)->first()) {

                            $lean_member->delete();

                        }

                    }

                }

                $attendants = request('attendants');

                foreach ($attendants as $team_member) {

                    if ($member = User::where('id', $team_member)->first()) {

                        PermitToWorkMember::updateOrCreate(
                            ['permit_to_work_id' => $permit_to_work->id, 'position' => 'ATTENDANT', 'member_id' => $member->id],
                            [
                                // 'organization_id' => $organization->id,
                                'permit_to_work_id' => $permit_to_work->id,
                                'member_id' => $member->id,
                                'position' => 'ATTENDANT',
                            ]);

                        if ($lean_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['ENTRY_SUPERVISOR', 'ENTRANT'])->where('member_id', $member->id)->first()) {

                            $lean_member->delete();

                        }

                    }

                }

            } else {

                $data = $request->validate([
                    'organization_id' => ['required', 'string'],
                    'permit_id' => ['required', 'string'],
                    'supervisor' => ['required'],
                    'officers' => ['required', 'array'],
                    'officers.*' => ['required'],
                    'nurses' => ['sometimes', 'array'],
                    'nurses.*' => ['sometimes'],
                    'workers' => ['required', 'array'],
                    'workers.*' => ['required'],
                ]);

                if ($supervisor = User::where('id', $data['supervisor'])->first()) {

                    PermitToWorkMember::updateOrCreate(
                        ['permit_to_work_id' => $permit_to_work->id, 'position' => 'SUPERVISOR'],
                        [
                            // 'organization_id' => $organization->id,
                            'permit_to_work_id' => $permit_to_work->id,
                            'member_id' => $supervisor->id,
                            'position' => 'SUPERVISOR',
                        ]);

                    if ($team_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['HSE_OFFICER', 'SITE_NURSE', 'WORKER'])->where('member_id', $supervisor->id)->first()) {

                        $team_member->delete();

                    }

                }

                $officers = request('officers');

                foreach ($officers as $team_member) {

                    if ($member = User::where('id', $team_member)->first()) {

                        PermitToWorkMember::updateOrCreate(
                            ['permit_to_work_id' => $permit_to_work->id, 'position' => 'HSE_OFFICER', 'member_id' => $member->id],
                            [
                                // 'organization_id' => $organization->id,
                                'permit_to_work_id' => $permit_to_work->id,
                                'member_id' => $member->id,
                                'position' => 'HSE_OFFICER',
                            ]);

                        if ($lean_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['SUPERVISOR', 'SITE_NURSE', 'WORKER'])->where('member_id', $member->id)->first()) {

                            $lean_member->delete();

                        }

                    }

                }

                $nurses = request('nurses');

                foreach ($nurses as $team_member) {

                    if ($member = User::where('id', $team_member)->first()) {

                        PermitToWorkMember::updateOrCreate(
                            ['permit_to_work_id' => $permit_to_work->id, 'position' => 'SITE_NURSE', 'member_id' => $member->id],
                            [
                                // 'organization_id' => $organization->id,
                                'permit_to_work_id' => $permit_to_work->id,
                                'member_id' => $member->id,
                                'position' => 'SITE_NURSE',
                            ]);

                        if ($lean_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['SUPERVISOR', 'HSE_OFFICER', 'WORKER'])->where('member_id', $member->id)->first()) {

                            $lean_member->delete();

                        }

                    }

                }

                $workers = request('workers');

                foreach ($workers as $team_member) {

                    if ($member = User::where('id', $team_member)->first()) {

                        PermitToWorkMember::updateOrCreate(
                            ['permit_to_work_id' => $permit_to_work->id, 'position' => 'WORKER', 'member_id' => $member->id],
                            [
                                // 'organization_id' => $organization->id,
                                'permit_to_work_id' => $permit_to_work->id,
                                'member_id' => $member->id,
                                'position' => 'WORKER',
                            ]);

                        if ($lean_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->whereIn('position', ['SUPERVISOR', 'SITE_NURSE', 'HSE_OFFICER'])->where('member_id', $member->id)->first()) {

                            $lean_member->delete();

                        }

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeMember(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'team_member' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->holder_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            // ERROR ANY OWNER OF ORGANIZATION CAN DELETE A MEMBER
            // IT IS SUPPOSE TO BE FOR THEIR INDIVIDUAL ORGANIZATION
            if ($member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->where('member_id', request('team_member'))->first()) {

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
    public function index()
    {

        try {

            $stats = PermitToWork::query();

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('holder_id', auth()->user()->id);
                $query->orWhere('issuer_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('holder_organization_id', $organization->id);
                    $query->orWhere('issuer_organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Permit Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Permit Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeJHA()
    {

        try {

            $stats = JobHazardAnalysis::where('is_del', 'no')->where('status', 'approved');

            $stats = $stats->where(function ($query) {

                $query->orWhere('prepared_by', auth()->user()->id);

            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Job Hazard Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Job Hazard Fetched Successfully', []);

        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendJHA(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'jha_id' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->holder_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if (!$jha = JobHazardAnalysis::where('prepared_by', auth()->user()->id)->where('status', 'approved')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find JHA");
            }

            if ($permit_to_work->jha_status == 'accepted') {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Your Job hazard Analysis is already approved");

            }

            $permit_to_work->jha_status = 'pending';
            $permit_to_work->jha_id = $jha->id;
            $permit_to_work->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('JHA Sent Successfully', []);

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
    public function actionJHA(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'action' => ['required'],
            'comment' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if (!$jha = JobHazardAnalysis::where('prepared_by', auth()->user()->id)->where('status', 'approved')) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find JHA");
            }

            if ($permit_to_work->jha_status == 'accepted') {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Job hazard Analysis is already approved");
            }

            if (request('action') == 'accepted') {

                $permit_to_work->jha_status = 'accepted';
                $permit_to_work->jha_comment = request('comment');
                $permit_to_work->save();

            }
            if (request('action') == 'declined') {

                $permit_to_work->jha_status = 'declined';
                $permit_to_work->jha_comment = request('comment');
                $permit_to_work->save();

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('JHA Updated Successfully', []);

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
    public function actionRequestForm(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'action' => ['required'],
            'comment' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if ($permit_to_work->request_form_status == 'accepted') {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Request Form is already approved");
            }

            $permit_to_work->request_form_status = request('action');
            $permit_to_work->request_form_comment = request('comment');
            $permit_to_work->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Request form status Updated Successfully', []);

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
    public function sendForDeclaration(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'ongoing')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if ($permit_to_work->is_rf_accepted && $permit_to_work->is_jha_accepted) {

                $permit_to_work->process_status = 'declaration';
                $permit_to_work->send_for_declaration = 'yes';
                $permit_to_work->save();

                // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

                return successResponse('Request Available for Declaration Successfully', []);
            }
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Permit is not already declaration, Check JHA and Request");

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
    public function sendDeclaration(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'declaration')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!$team_member = PermitToWorkMember::where('permit_to_work_id', $permit_to_work->id)->where('member_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if (!$team_member->is_declared) {

                $team_member->declaration = 'yes';
                $team_member->save();

                // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

                return successResponse('Declaration Sent Successfully', []);
            }
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Permit is not already declaration, Check JHA and Request");

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
    public function issuePermit(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'declaration')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }
            $start_date = Carbon::create(request('start_date'));
            $end_date = Carbon::create(request('end_date'));

            $days = Carbon::create($start_date)->diffInDays(Carbon::create($end_date));
            if ($days > 14) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Maximum of 14 Days");

            }

            $permit_to_work->process_status = 'issued';
            $permit_to_work->status = 'active';
            $permit_to_work->alloted_work_start_time = $start_date;
            $permit_to_work->alloted_work_end_time = $end_date;

            $permit_to_work->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Issued Successfully', []);

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
    public function suspendPermit(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'comment' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'issued')->where('status', 'active')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            $permit_to_work->status = 'suspended';
            $permit_to_work->comment = request('comment');
            $permit_to_work->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Suspended Successfully', []);

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
    public function reactivatePermit(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'comment' => ['required'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'issued')->whereIn('status', ['cancelled', 'suspended', 'closed'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            $permit_to_work->status = 'active';
            $permit_to_work->comment = request('comment');
            $permit_to_work->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Activated Successfully', []);

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
    public function sendPermitRequest(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'comment' => ['required'],
            'type' => ['required', 'in:reactivation,extension'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'issued')->whereIn('status', ['cancelled', 'suspended', 'closed'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->holder_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if (PermitToWorkActionRequest::where('type', request('type'))->whereIn('status', ['pending'])->exists()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You have an unresolved request for that type");

            }

            $permit_request = PermitToWorkActionRequest::create([
                'permit_to_work_id' => $permit_to_work->id,
                'type' => request('type'),
                'status' => 'pending',
                'holder_comment' => request('comment'),
            ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Request Sent Successfully', []);

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
    public function markAsCompleteRequest(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'comment' => ['required'],
            'type' => ['required', 'in:completion'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'issued')->whereIn('status', ['active'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->holder_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->holder_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if (PermitToWorkActionRequest::where('type', 'completion')->whereIn('status', ['pending', 'accepted'])->exists()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You have an unresolved completion request");
            }

            $permit_request = PermitToWorkActionRequest::create([
                'permit_to_work_id' => $permit_to_work->id,
                'type' => 'completion',
                'status' => 'pending',
                'holder_comment' => request('comment'),
            ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Activated Successfully', []);

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
    public function actionPermitRequest(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'permit_id' => ['required', 'string'],
            'daysAdded' => ['sometimes', 'integer'],
            'comment' => ['required'],
            'type' => ['required', 'in:reactivation,extension,completion'],
            'status' => ['required', 'in:accepted,declined'],
        ]);

        try {

            if (!$permit_to_work = PermitToWork::where('uuid', $data['permit_id'])->where('request_status', 'accepted')->where('process_status', 'issued')->whereIn('status', ['cancelled', 'suspended', 'closed'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Permit");
            }

            if (!$organization = Organisation::where('id', $permit_to_work->issuer_organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform action");
            }

            if (!(auth()->user()->id == $permit_to_work->issuer_id)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to perform actions");
            }

            if ($permit_request = PermitToWorkActionRequest::where('type', request('type'))->whereIn('status', ['pending'])->first()) {

                // if (request('type') == 'reactivation') {

                // }

                $permit_request->permit_to_work_id = $permit_to_work->id;
                $permit_request->type = request('type');
                $permit_request->status = request('status');
                $permit_request->issuer_comment = request('comment');
                $permit_request->save();

                if (request('type') == 'completion' && request('status') == 'accepted') {
                    $permit_to_work->status = 'completed';
                    $permit_to_work->final_comment = request('comment');
                    $permit_to_work->save();
                }

                if (request('type') == 'extension' && request('status') == 'accepted') {
                    $data = $request->validate([
                        'daysAdded' => ['required', 'integer'],
                    ]);

                    $permit_to_work->status = 'active';
                    $permit_to_work->work_end_time = (Carbon::create($permit_to_work->work_end_time))->addDays(request('daysAdded'));
                    $permit_to_work->save();

                }

                if (request('type') == 'reactivation' && request('status') == 'accepted') {
                    $permit_to_work->status = 'active';
                    $permit_to_work->comment = request('comment');
                    $permit_to_work->save();
                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Permit Request Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PermitToWork  $permitToWork
     * @return \Illuminate\Http\Response
     */
    public function show(PermitToWork $permitToWork)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PermitToWork  $permitToWork
     * @return \Illuminate\Http\Response
     */
    public function edit(PermitToWork $permitToWork)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PermitToWork  $permitToWork
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PermitToWork $permitToWork)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PermitToWork  $permitToWork
     * @return \Illuminate\Http\Response
     */
    public function destroy(PermitToWork $permitToWork)
    {
        //
    }
}
