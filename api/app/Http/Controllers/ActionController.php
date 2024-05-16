<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Action;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $actions = Action::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $actions = $actions->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_actions = arrayKeysToCamelCase($actions);

            return successResponse('Action Fetched Successfully', $converted_actions);

        } catch (\Throwable $th) {
            logger($th);
            return successResponse('Action Fetched Successfully', []);

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
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'assignee_id' => ['required', 'string'],
            'priority_id' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Organization Not Found");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

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
            ]);

            logAction(auth()->user()->email, 'You created an action ', 'Action Create', $request->ip());
            // SENDMAIL

            logAction($user->email, 'An action was sent to you ', 'Create Action', $request->ip());
            // SENDMAIl

            return successResponse('Action Created Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function show(Action $action)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function edit(Action $action)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'action_id' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority_id' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Organization Not Found");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$action = Action::where('uuid', $data['action_id'])->where('status', 'pending')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Action Not Found");
            }

            $action->user_id = auth()->user()->id;
            $action->organization_id = $organization->id;
            $action->title = $request->title;
            $action->description = $request->description;
            $action->start_datetime = Carbon::create($request->start_date);
            $action->end_datetime = Carbon::create($request->end_date);
            $action->priority_id = $request->priority_id;
            $action->save();

            logAction(auth()->user()->email, 'You edited an action ', 'Action Update', $request->ip());
            // SENDMAIL

            return successResponse('Action Update Successfully', []);

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
    public function delete(Request $request)
    {
        $data = $request->validate([
            'action_id' => ['required', 'string'],
        ]);

        try {

            if (!$action = Action::where('uuid', $data['action_id'])->where('status', Action::status['PEN'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Action");
            }

            $action->is_del = 'yes';
            $action->save();

            logAction(auth()->user()->email, 'You deleted an action ', 'Action Delete', $request->ip());
            // SENDMAIl

            if ($organization = Organisation::where('id', $action->id)->first()) {
                $organization_owner = Organisation::owner($organization);
                logAction($organization_owner->email, 'A team member deleted a action on your organization ', 'Delete Action', $request->ip());
                // SENDMAIl

            }

            return successResponse('Action Deleted Successfully', []);

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
    public function changeStatus(Request $request)
    {
        $data = $request->validate([
            'action_id' => ['required', 'string'],
            'status' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        try {

            if (!$action = Action::where('uuid', $data['action_id'])->whereIn('status', [Action::status['PEN'], Action::status['ONG'], Action::status['ACC']])->where('is_del', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to complete Action");
            }

            $actionCanContinue = false;

            switch ($data['status']) {
                case Action::status['PEN']:

                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to complete Action");
                    break;

                case Action::status['ACC']:

                    if ($action->status == Action::status['PEN'] && ($action->assignee_id == auth()->user()->id) && !$action->can_start) {
                        $actionCanContinue = true;

                    }

                    break;

                case Action::status['REJ']:

                    if (($action->status == Action::status['PEN'] && $action->assignee_id == auth()->user()->id) && !$action->can_start) {
                        $actionCanContinue = true;
                    }

                    break;

                case Action::status['ONG']:

                    if (in_array($action->status, [Action::status['ACC'], Action::status['REJ']]) && ($action->assignee_id == auth()->user()->id) && $action->can_start) {
                        $actionCanContinue = true;
                    }

                    break;

                case Action::status['CAN']:
                    // Owner and Member
                    if (($action->user_id != auth()->user()->id) || (in_array($action->status, [Action::status['ACC'], Action::status['REJ']]) && ($action->assignee_id == auth()->user()->id) && $action->can_start)) {
                        $actionCanContinue = true;
                    }
                    break;

                case Action::status['COM']:

                    if ($action->status == Action::status['ONG'] && ($action->assignee_id == auth()->user()->id)) {
                        $actionCanContinue = true;
                    }

                    break;

                default:
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to complete Action");
                    break;
            }

            if ($actionCanContinue) {

                $action->status = request('status');
                $action->statusMessage = request('message');
                $action->save();

                logAction(auth()->user()->email, 'Action Status Updated ', 'Action Status', $request->ip());
                // SENDMAIl

                if ($organization = Organisation::where('id', $action->id)->first()) {
                    $organization_owner = Organisation::owner($organization);
                    logAction($organization_owner->email, 'A team member deleted a action on your organization ', 'Status Action', $request->ip());
                    // SENDMAIl

                }

                return successResponse('Action Status Updated Successfully', []);

            }

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function destroy(Action $action)
    {
        //
    }
}
