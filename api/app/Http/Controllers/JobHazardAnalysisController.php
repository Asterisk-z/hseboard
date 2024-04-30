<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\JobHazardAnalysis;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class JobHazardAnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $stats = JobHazardAnalysis::where('is_del', 'no');

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {

                $query->orWhere('organization_id', $organization->id);

            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Job Hazard Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Job Hazard Fetched Successfully', []);

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

            if (!$job = JobHazardAnalysis::where('uuid', request('job_id'))->whereIn('status', ['ongoing', 'declined'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find job");
            }

            return successResponse('Job Fetched Successfully', $job);

        } catch (Exception $error) {

            return successResponse('Job Fetched Successfully', []);

        }

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function review()
    {
        try {

            $stats = [];

            if (!$job = JobHazardAnalysis::where('uuid', request('job_id'))->whereIn('status', ['completed', 'approved'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find job");
            }

            return successResponse('Job Fetched Successfully', $job);

        } catch (Exception $error) {

            return successResponse('Job Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createJob(Request $request)
    {
        logger(request()->all());
        $data = $request->validate([
            'title' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!$user = Organisation::find_user($organization, auth()->user()->email)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User");
            }

            $main_audit = JobHazardAnalysis::create([
                'prepared_by' => auth()->user()->id,
                'organization_id' => $organization->id,
                'prepared_date' => now(),
                'title' => request('title'),
            ]);

            logAction(auth()->user()->email, 'You started a Job Hazard Analysis ', 'Start Job Hazard analysis', $request->ip());

            return successResponse('Job Created Successfully', $main_audit);

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
    public function complete(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!$job_hazard = JobHazardAnalysis::where('uuid', $data['job_hazard_id'])->whereIn('status', ["ongoing", "declined"])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            if ($job_hazard->prepared_by != auth()->user()->id || $job_hazard->organization_id != $organization->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User");
            }

            $job_hazard->status = 'completed';
            $job_hazard->save();

            // logAction(auth()->user()->email, 'You started a Job Hazard Analysis ', 'Start Job Hazard analysis', $request->ip());

            return successResponse('Job Completed Successfully', []);

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
    public function actionStatus(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'status' => ['required', 'in:approved,declined'],
            'reason' => ['sometimes'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if (!$job_hazard = JobHazardAnalysis::where('uuid', $data['job_hazard_id'])->whereIn('status', ["completed"])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            if ($job_hazard->prepared_by == auth()->user()->id || $job_hazard->organization_id != $organization->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User");
            }

            if (request('status') == 'declined') {
                $request->validate([
                    'reason' => ['required'],
                ]);
            }

            $job_hazard->status = request('status');
            $job_hazard->reviewed_by = auth()->user()->id;
            $job_hazard->reviewed_date = now();
            $job_hazard->status_message = request('reason');
            $job_hazard->save();

            // logAction(auth()->user()->email, 'You started a Job Hazard Analysis ', 'Start Job Hazard analysis', $request->ip());

            return successResponse('Job Actioned Successfully', []);

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
     * @param  \App\Models\JobHazardAnalysis  $jobHazardAnalysis
     * @return \Illuminate\Http\Response
     */
    public function show(JobHazardAnalysis $jobHazardAnalysis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobHazardAnalysis  $jobHazardAnalysis
     * @return \Illuminate\Http\Response
     */
    public function edit(JobHazardAnalysis $jobHazardAnalysis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobHazardAnalysis  $jobHazardAnalysis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobHazardAnalysis $jobHazardAnalysis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobHazardAnalysis  $jobHazardAnalysis
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobHazardAnalysis $jobHazardAnalysis)
    {
        //
    }
}
