<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Action;
use App\Models\ChatConversion;
use App\Models\ChatRecipient;
use App\Models\Investigation;
use App\Models\InvestigationFinding;
use App\Models\InvestigationInterview;
use App\Models\InvestigationMember;
use App\Models\InvestigationQuestion;
use App\Models\InvestigationQuestionUser;
use App\Models\InvestigationReport;
use App\Models\Observation;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = Investigation::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Investigation Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Investigation Fetched Successfully', []);

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function witnessIndex()
    {

        try {

            $question_user = InvestigationQuestionUser::where('responder_id', auth()->user()->id)->pluck('investigation_id');

            $stats = Investigation::whereIn('id', $question_user)->where('is_del', 'no')->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Investigation Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Investigation Fetched Successfully', []);

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function witnessQuestions()
    {

        try {

            $investigation = Investigation::where('uuid', request('investigation_id'))->first();

            $question_user = InvestigationQuestionUser::where('responder_id', auth()->user()->id)->where('investigation_id', $investigation->id)->get();

            $converted_stats = arrayKeysToCamelCase($question_user);

            return successResponse('Investigation Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Investigation Fetched Successfully', []);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function witnessCompleted(Request $request)
    {

        $data = $request->validate([
            'investigation_id' => ['required'],
        ]);

        try {

            $investigation = Investigation::where('uuid', request('investigation_id'))->first();

            $questions = InvestigationQuestionUser::where('responder_id', auth()->user()->id)->where('investigation_id', $investigation->id)->get();
            DB::beginTransaction();
            if ($questions) {
                foreach ($questions as $question) {
                    if (!$question->response) {
                        DB::rollBack();
                        continue;
                    }

                    $question->is_completed = 'yes';
                    $question->save();

                }

                DB::commit();

                logAction(auth()->user()->email, 'You Answered all investigation Question ', 'All Response Sent', $request->ip());
                return successResponse('Response Sent Successfully', []);
            }

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
    public function witnessRespond(Request $request)
    {

        $data = $request->validate([
            'investigation_question_id' => ['required'],
            'investigation_id' => ['required'],
            'response' => ['required', 'string'],
        ]);

        try {

            $investigation = Investigation::where('id', request('investigation_id'))->first();

            $question_user = InvestigationQuestionUser::where('responder_id', auth()->user()->id)->where('investigation_id', $investigation->id)->where('id', request('investigation_question_id'))->first();

            if ($question_user) {
                $question_user->response = request('response');
                $question_user->responded_at = now();
                $question_user->save();
                logAction(auth()->user()->email, 'You Answered an investigation Question ', 'Response Sent', $request->ip());
                return successResponse('Response Sent Successfully', []);
            }

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
    public function start(Request $request)
    {

        $data = $request->validate([
            'observation_id' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }
            if (!$observation = Observation::where('uuid', $data['observation_id'])->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }

            if (Investigation::where('observation_id', $observation->id)->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is an ongoing investigation for this observation");
            }

            $investigation = Investigation::create([
                'organization_id' => $organization->id,
                'observation_id' => $observation->id,
                'start_date' => now(),
                'user_id' => auth()->user()->id,
            ]);

            $observation->status = Observation::status['BEI'];
            $observation->save();

            logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Investigation started Successfully', []);

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
    public function reinvestigate(Request $request)
    {

        $data = $request->validate([
            'observation_id' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }
            if (!$observation = Observation::where('uuid', $data['observation_id'])->where('organization_id', $organization->id)->where('status', Observation::status['DOI'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }

            if (Investigation::where('observation_id', $observation->id)->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is an ongoing investigation for this observation");
            }

            $investigation = Investigation::create([
                'organization_id' => $organization->id,
                'observation_id' => $observation->id,
                'start_date' => now(),
                'user_id' => auth()->user()->id,
            ]);

            $observation->status = Observation::status['REI'];
            $observation->save();

            logAction(auth()->user()->email, 'You started a Re-investigation ', 'Start Re-Investigation', $request->ip());

            return successResponse('Investigation started Successfully', []);

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
    public function getQuestions()
    {
        try {

            $stats = InvestigationQuestion::where('type', 'default');

            if (request('observation_id')) {
                if ($observation = Observation::where('uuid', request('observation_id'))->first()) {

                    if ($investigation = Investigation::where('observation_id', $observation->id)->first()) {
                        $stats = $stats->orWhere('investigation_id', $investigation->id);
                    }

                }
            }

            $stats = $stats->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Questions Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Questions Fetched Successfully', []);

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setQuestions(Request $request)
    {
        $data = $request->validate([
            'investigation_id' => ['required', 'string'],
            'question' => ['required', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            InvestigationQuestion::create([
                'investigator_id' => auth()->user()->id,
                'investigation_id' => $investigation->id,
                'question' => request('question'),
                'type' => 'optional',
            ]);

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Question Added Successfully', []);

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
    public function setTeamMembers(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'lead_investigator' => ['nullable'],
            'team_members' => ['nullable', 'array'],
            'team_members.*' => ['nullable'],
            'group_name' => ['required', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            if (request('group_name')) {

                $conversation = ChatConversion::updateOrCreate(
                    ['id' => $investigation->conversation_id],
                    [
                        'name' => request('group_name'),
                        'is_group' => true,
                    ]);

            }

            if ($lead_investigator = User::where('id', request('lead_investigator'))->first()) {
                $lead = $investigation->lead ? $investigation->lead->id : null;

                InvestigationMember::updateOrCreate(
                    ['investigation_id' => $investigation->id, 'position' => 'lead'],
                    [
                        'investigator_id' => auth()->user()->id,
                        'investigation_id' => $investigation->id,
                        'member_id' => $lead_investigator->id,
                        'position' => 'lead',
                    ]);
                if ($conversation) {
                    ChatRecipient::updateOrCreate(
                        ['conversation_id' => $conversation->id, 'user_id' => $lead_investigator->id],
                        [
                            'conversation_id' => $conversation->id,
                            'user_id' => $lead_investigator->id,
                        ]);

                }

                if ($lean_member = InvestigationMember::where('investigation_id', $investigation->id)->where('position', 'member')->where('member_id', $lead_investigator->id)->first()) {

                    $lean_member->delete();

                }

            }

            $team_members = request('team_members');

            foreach ($team_members as $team_member) {

                if ($member = User::where('id', $team_member)->first()) {

                    InvestigationMember::updateOrCreate(
                        ['investigation_id' => $investigation->id, 'member_id' => $member->id],
                        [
                            'investigator_id' => auth()->user()->id,
                            'investigation_id' => $investigation->id,
                            'member_id' => $member->id,
                            'position' => 'member',
                        ]);

                    if ($conversation) {
                        ChatRecipient::updateOrCreate(
                            ['conversation_id' => $conversation->id, 'user_id' => $member->id],
                            [
                                'conversation_id' => $conversation->id,
                                'user_id' => $member->id,
                            ]);
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
    public function setExternalTeamMembers(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'lead_investigator' => ['nullable'],
            'team_members' => ['nullable', 'array'],
            'team_members.*' => ['nullable'],
            'group_name' => ['required', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            if (request('group_name')) {

                $conversation = ChatConversion::updateOrCreate(
                    ['id' => $investigation->conversation_id],
                    [
                        'name' => request('group_name'),
                        'is_group' => true,
                    ]);

            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($lead_investigator = User::where('id', request('lead_investigator'))->first()) {
                $lead = $investigation->lead ? $investigation->lead->id : null;

                InvestigationMember::updateOrCreate(
                    ['investigation_id' => $investigation->id, 'position' => 'lead'],
                    [
                        'investigator_id' => auth()->user()->id,
                        'investigation_id' => $investigation->id,
                        'member_id' => $lead_investigator->id,
                        'position' => 'lead',
                    ]);
                if ($conversation) {
                    ChatRecipient::updateOrCreate(
                        ['conversation_id' => $conversation->id, 'user_id' => $lead_investigator->id],
                        [
                            'conversation_id' => $conversation->id,
                            'user_id' => $lead_investigator->id,
                        ]);

                }

                if ($lean_member = InvestigationMember::where('investigation_id', $investigation->id)->where('position', 'member')->where('member_id', $lead_investigator->id)->first()) {

                    $lean_member->delete();

                }

            }

            $team_members = request('team_members');

            foreach ($team_members as $team_member) {

                if ($member = User::where('id', $team_member)->first()) {
                    if ($member = Organisation::find_user($organization, $member->email)) {

                        InvestigationMember::updateOrCreate(
                            ['investigation_id' => $investigation->id, 'member_id' => $member->id],
                            [
                                'investigator_id' => auth()->user()->id,
                                'investigation_id' => $investigation->id,
                                'member_id' => $member->id,
                                'position' => 'member',
                            ]);
                        if ($conversation) {
                            ChatRecipient::updateOrCreate(
                                ['conversation_id' => $conversation->id, 'user_id' => $member->id],
                                [
                                    'conversation_id' => $conversation->id,
                                    'user_id' => $member->id,
                                ]);
                        }

                    }
                }

            }

            if (request('group_name')) {

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
            'investigation_id' => ['required', 'string'],
            'team_member' => ['required'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            if ($member = InvestigationMember::where('investigation_id', $investigation->id)->where('member_id', request('team_member'))->first()) {
                if ($conversation = ChatConversion::where('id', $investigation->conversation_id)->first()) {

                    if ($chat = ChatRecipient::where('conversation_id', $conversation->id)->where('user_id', request('team_member'))->first()) {

                        $chat->delete();

                    }

                }

                $member->delete();

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
    public function sendQuestions(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'questions' => ['required', 'array'],
            'questions.*' => ['required'],
            'members' => ['required', 'array'],
            'members.*' => ['required'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            $questions = request('questions');
            $members = request('members');

            foreach ($members as $team_member) {

                if ($member = User::where('id', $team_member)->first()) {

                    foreach ($questions as $question) {

                        if ($fQuestion = InvestigationQuestion::where('id', $question)->first()) {
                            InvestigationQuestionUser::create([
                                'user_id' => auth()->user()->id,
                                'responder_id' => $member->id,
                                'investigation_id' => $investigation->id,
                                'question_id' => $fQuestion->id,
                            ]);
                        }

                    }

                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Question sent Successfully', []);

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
    public function sendInvites(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'date_times' => ['required', 'array'],
            'date_times.*' => ['required'],
            'members' => ['required', 'array'],
            'members.*' => ['required'],
            'interview_type' => ['required', 'string'],
            'interview_link' => ['nullable', 'string'],
            'interview_location' => ['nullable', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            $members = request('members');
            $date_times = request('date_times');

            foreach ($members as $team_member) {

                if ($member = User::where('id', $team_member)->first()) {

                    InvestigationInterview::updateOrCreate(
                        ['investigation_id' => $investigation->id, 'user_id' => auth()->user()->id, 'invitee_id' => $member->id], [
                            'user_id' => auth()->user()->id,
                            'invitee_id' => $member->id,
                            'investigation_id' => $investigation->id,
                            'type' => request('interview_type'),
                            'date_1' => Carbon::create($date_times[0]),
                            'date_2' => Carbon::create($date_times[1]),
                            'date_3' => Carbon::create($date_times[2]),
                            'location' => request('interview_location'),
                            'link' => request('interview_link'),
                        ]);

                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Invites Sent Successfully', []);

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
    public function sendFindings(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'type' => ['required', 'string'],
            'detail' => ['required', 'string'],
            'files' => ['sometimes', 'array'],
            'files.*' => ['sometimes', 'file'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            $finding = InvestigationFinding::updateOrCreate(
                ['investigation_id' => $investigation->id, 'user_id' => auth()->user()->id, 'description' => request('detail')], [
                    'user_id' => auth()->user()->id,
                    'investigation_id' => $investigation->id,
                    'type' => request('type'),
                    'description' => request('detail'),
                ]);

            if (request('files')) {
                $files = request('files');
                foreach ($files as $file) {
                    storeMedia($file, InvestigationFinding::class, $finding->id, 'findings');

                }
            }

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
    public function recommendation(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'assignee_id' => ['required', 'string'],
            'priority_id' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Organization Not Found");
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
                'investigation_id' => $investigation->id,
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
    public function sendReport(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'investigation_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string'],
            'incident_date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'damages' => ['required', 'string'],
            'method' => ['required', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            InvestigationReport::updateOrCreate(
                ['investigation_id' => $investigation->id, 'user_id' => auth()->user()->id], [
                    'user_id' => auth()->user()->id,
                    'investigation_id' => $investigation->id,
                    'title' => request('title'),
                    'description' => request('description'),
                    'location' => request('location'),
                    'incident_date_time' => Carbon::create(request('incident_date_time')),
                    'damages' => request('damages'),
                ]);

            $investigation->method = request('method');
            $investigation->save();

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Report Sent Successfully', []);

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
            'investigation_id' => ['required', 'string'],
        ]);

        try {

            if (!$investigation = Investigation::where('uuid', $data['investigation_id'])->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Investigation");
            }

            if (!$observation = Observation::where('id', $investigation->observation_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }
            logger($investigation->action);
            logger($investigation->is_completed());
            if ($investigation->is_completed()) {

                $investigation->end_date = now();
                $investigation->save();

                $observation->status = Observation::status['DOI'];
                $observation->save();

                return successResponse('Report Sent Successfully', []);
            }

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

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
     * @param  \App\Models\Investigation  $investigation
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try {

            $stats = [];

            if (!$observation = Observation::where('uuid', request('observation_id'))->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }

            if (!$investigation = Investigation::where('observation_id', $observation->id)->where('end_date', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is no running investigation");
            }

            return successResponse('Investigation Fetched Successfully', $investigation);

        } catch (Exception $error) {

            return successResponse('Investigation Fetched Successfully', []);

        }

    }

    public function completed()
    {
        try {

            $stats = [];

            if (!$investigation = Investigation::where('uuid', request('investigation_id'))->where('end_date', '!=', null)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is no running investigation");
            }

            return successResponse('Investigation Fetched Successfully', $investigation);

        } catch (Exception $error) {

            return successResponse('Investigation Fetched Successfully', []);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return \Illuminate\Http\Response
     */
    public function edit(Investigation $investigation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Investigation  $investigation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Investigation $investigation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Investigation $investigation)
    {
        //
    }
}
