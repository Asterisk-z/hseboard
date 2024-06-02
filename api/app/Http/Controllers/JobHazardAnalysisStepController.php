<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\JobHazardAnalysis;
use App\Models\JobHazardAnalysisStep;
use App\Models\JobHazardAnalysisStepActionParty;
use App\Models\JobHazardAnalysisStepConsequence;
use App\Models\JobHazardAnalysisStepHazardSource;
use App\Models\JobHazardAnalysisStepPreventiveAction;
use App\Models\JobHazardAnalysisStepRcp;
use App\Models\JobHazardAnalysisStepRecoveryMeasure;
use App\Models\JobHazardAnalysisStepTarget;
use App\Models\JobHazardAnalysisStepTopEvent;
use App\Models\Organisation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class JobHazardAnalysisStepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addStep(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
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

            $main_audit = JobHazardAnalysisStep::create([
                'title' => request('title'),
                'job_hazard_analysis_id' => $job_hazard->id,
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
    public function removeStep(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('id', $data['job_hazard_step_id'])->where('job_hazard_analysis_id', $job_hazard->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Step");
            }

            $job_hazard_step->is_del = 'yes';
            $job_hazard_step->save();

            // logAction(auth()->user()->email, 'You started a Job Hazard Analysis ', 'Start Job Hazard analysis', $request->ip());

            return successResponse('Job Removed Successfully', []);

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
    public function topEvent(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepTopEvent::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function hazardSource(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepHazardSource::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function target(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepTarget::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function consequence(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepConsequence::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function preventiveAction(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepPreventiveAction::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function rcp(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepRcp::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function recoveryMeasure(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'description' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepRecoveryMeasure::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'code' => request('code'),
                    'description' => request('description'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function actionParty(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'designation' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['sometimes'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $main_audit = JobHazardAnalysisStepActionParty::updateOrCreate(
                ['id' => request('job_hazard_item_id')], [
                    'full_name' => request('name'),
                    'designation' => request('designation'),
                    'job_hazard_analysis_step_id' => $job_hazard_step->id,
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
    public function deleteItem(Request $request)
    {
        logger(request()->all());
        $data = $request->validate([
            'item' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'job_hazard_id' => ['required', 'string'],
            'job_hazard_step_id' => ['required', 'integer'],
            'job_hazard_item_id' => ['required'],
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

            if (!$job_hazard_step = JobHazardAnalysisStep::where('job_hazard_analysis_id', $job_hazard->id)->where('id', $data['job_hazard_step_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
            }

            $item = request('item');
            $job_hazard_step_item = null;

            switch ($item) {
                case 'top_events':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepTopEvent::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'sources':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepHazardSource::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'targets':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepTarget::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'consequences':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepConsequence::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'preventives':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepPreventiveAction::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'rcps':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepRcp::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'recoveries':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepRecoveryMeasure::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;
                case 'parties':
                    if (!$job_hazard_step_item = JobHazardAnalysisStepActionParty::where('id', $data['job_hazard_item_id'])->first()) {
                        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Job");
                    }
                    # code...
                    break;

                default:
                    # code...
                    break;
            }
            // logger($job_hazard_step_item);

            if ($job_hazard_step_item) {
                $job_hazard_step_item->is_del = 'yes';
                $job_hazard_step_item->save();
                logAction(auth()->user()->email, 'You started a Job Hazard Analysis ', 'Start Job Hazard analysis', $request->ip());

                return successResponse('Job Deleted Successfully', []);
            }

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Failed to delete Item");

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
     * @param  \App\Models\JobHazardAnalysisStep  $jobHazardAnalysisStep
     * @return \Illuminate\Http\Response
     */
    public function show(JobHazardAnalysisStep $jobHazardAnalysisStep)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobHazardAnalysisStep  $jobHazardAnalysisStep
     * @return \Illuminate\Http\Response
     */
    public function edit(JobHazardAnalysisStep $jobHazardAnalysisStep)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobHazardAnalysisStep  $jobHazardAnalysisStep
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobHazardAnalysisStep $jobHazardAnalysisStep)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobHazardAnalysisStep  $jobHazardAnalysisStep
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobHazardAnalysisStep $jobHazardAnalysisStep)
    {
        //
    }
}
