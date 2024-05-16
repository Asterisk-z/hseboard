<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Observation;
use App\Models\Organisation;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ObservationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $observations = Observation::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $observations = $observations->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();
            $converted_observations = arrayKeysToCamelCase($observations);

            return successResponse('Observation Fetched Successfully', $converted_observations);
        } catch (\Throwable $th) {
            logger($th);
            return successResponse('Observation Fetched Successfully', []);

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
        // logger("request");
        // logger($request);
        // logger(request()->all());
        $data = $request->validate([
            'organization_id' => ['nullable', 'string'],
            'observation_type' => ['required'],
            'description' => ['required', 'string'],
            'location_details' => ['required', 'string'],
            'address' => ['required', 'string'],
            'affected_workers' => ['nullable', 'string'],
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'severity' => ['required'],
        ]);

        try {
            $organization_id = null;
            if ($organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                $organization_id = $organization->id;
            }

            Observation::create([
                'user_id' => auth()->user()->id,
                'affected_workers' => $request->affected_workers ? $request->affected_workers : null,
                'description' => $request->description,
                'incident_datetime' => Carbon::create($request->date_time),
                'address' => $request->address,
                'location_details' => $request->location_details,
                'priority_id' => $request->severity,
                'observation_type_id' => $request->observation_type,
                'organization_id' => $organization_id,
            ]);

            logAction(auth()->user()->email, 'You created an observation ', 'Observation Create', $request->ip());
            // SENDMAIL
            if ($organization_id) {
                $organization_owner = Organisation::owner($organization);
                logAction($organization_owner->email, 'A team member created an observation for your organization ', 'Create Observation', $request->ip());
                // SENDMAIl
            }

            return successResponse('Observation Created Successfully', []);

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
    public function update(Request $request)
    {
        $data = $request->validate([
            'observation_id' => ['required'],
            'observation_type' => ['required'],
            'description' => ['required', 'string'],
            'location_details' => ['required', 'string'],
            'address' => ['required', 'string'],
            'affected_workers' => ['nullable'],
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'severity' => ['required'],
        ]);

        try {

            if (!$observation = Observation::where('uuid', $data['observation_id'])->where('status', Observation::status['PEI'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }

            $organization_id = null;
            if ($organization = Organisation::where('uuid', $observation->organization_id)->first()) {
                $organization_id = $organization->id;
            }

            $observation->affected_workers = $request->affected_workers;
            $observation->description = $request->description;
            $observation->incident_datetime = Carbon::create($request->date_time);
            $observation->address = $request->address;
            $observation->location_details = $request->location_details;
            $observation->priority_id = $request->severity;
            $observation->observation_type_id = $request->observation_type;
            $observation->save();

            logAction(auth()->user()->email, 'You updated a pending observation ', 'Observation Update', $request->ip());
            // SENDMAIL
            if ($organization_id) {
                $organization_owner = Organisation::owner($organization);
                logAction($organization_owner->email, 'A team member updated a observation for your organization ', 'Update Observation', $request->ip());
                // SENDMAIl
            }

            return successResponse('Observation Updated Successfully', []);

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
            'observation_id' => ['required', 'string'],
        ]);

        try {

            if (!$observation = Observation::where('uuid', $data['observation_id'])->where('status', Observation::status['PEI'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Observation");
            }

            $observation->is_del = 'yes';
            $observation->save();

            logAction(auth()->user()->email, 'You deleted an observation ', 'Observation Delete', $request->ip());
            // SENDMAIl

            if ($organization = Organisation::where('id', $observation->id)->first()) {
                $organization_owner = Organisation::owner($organization);
                logAction($organization_owner->email, 'A team member deleted a observation on your organization ', 'Delete Observation', $request->ip());
                // SENDMAIl

            }

            return successResponse('Observation Deleted Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function show(Observation $observation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function edit(Observation $observation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Observation $observation)
    {
        //
    }
}
