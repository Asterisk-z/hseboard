<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\InspectionTemplate;
use App\Models\InspectionTemplateQuestions;
use App\Models\InspectionTemplateType;
use App\Models\Organisation;
use Illuminate\Http\Request;

class InspectionTemplateQuestionsController extends Controller
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
// logger(request('questions'));
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'questions' => ['required', 'array'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', auth()->user()->active_organization)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$audit_type = InspectionTemplateType::where('is_del', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Template");
            }

            $template = InspectionTemplate::create([
                'organization_id' => $organization->id,
                'user_id' => auth()->user()->id,
                'title' => request('title'),
                'description' => request('description'),
                'inspection_template_type_id' => $audit_type->id,
                'file_id' => 1,
            ]);

            $group = null;
            $header = null;
            $topic = null;
            $questions = request('questions');
            foreach ($questions as $question) {

                if (strstr($question, '[GROUP]')) {
                    if (InspectionTemplateQuestions::where('title', str_replace('[GROUP]', '', $question))->where('inspection_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $group = InspectionTemplateQuestions::create([
                        'question' => null,
                        'title' => str_replace('[GROUP]', '', $question),
                        'inspection_template_id' => $template->id,
                        'group_id' => null,
                        'header_id' => null,
                        'topic_id' => null,
                    ]);
                } else if (strstr($question, '[HEADER]')) {
                    if (InspectionTemplateQuestions::where('title', str_replace('[HEADER]', '', $question))->where('inspection_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $header = InspectionTemplateQuestions::create([
                        'question' => null,
                        'title' => str_replace('[HEADER]', '', $question),
                        'inspection_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => null,
                        'topic_id' => null,
                    ]);
                } else if (strstr($question, '[TOPIC]')) {
                    if (InspectionTemplateQuestions::where('title', str_replace('[TOPIC]', '', $question))->where('inspection_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $topic = InspectionTemplateQuestions::create([
                        'question' => null,
                        'title' => str_replace('[TOPIC]', '', $question),
                        'inspection_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => $header ? $header->id : null,
                        'topic_id' => null,
                    ]);
                } else {
                    if (InspectionTemplateQuestions::where('question', $question)->where('inspection_template_id', $template->id)->exists()) {
                        continue;
                    }
                    InspectionTemplateQuestions::create([
                        'question' => $question,
                        'title' => null,
                        'inspection_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => $header ? $header->id : null,
                        'topic_id' => $topic ? $topic->id : null,
                    ]);
                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Inspection Document started Successfully', []);

        } catch (\Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditTemplate  $auditTemplate
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {

        $data = $request->validate([
            'template_id' => ['required'],
        ]);

        try {

            if (!$action = InspectionTemplate::where('id', $data['template_id'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            $action->is_del = 'yes';
            $action->save();

            logAction(auth()->user()->email, 'You deleted a Template ', 'Template Delete', $request->ip());
            // SENDMAIl

            return successResponse('Template Deleted Successfully', []);

        } catch (\Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InspectionTemplateQuestions  $inspectionTemplateQuestions
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionTemplateQuestions $inspectionTemplateQuestions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InspectionTemplateQuestions  $inspectionTemplateQuestions
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionTemplateQuestions $inspectionTemplateQuestions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InspectionTemplateQuestions  $inspectionTemplateQuestions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InspectionTemplateQuestions $inspectionTemplateQuestions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InspectionTemplateQuestions  $inspectionTemplateQuestions
     * @return \Illuminate\Http\Response
     */
    public function destroy(InspectionTemplateQuestions $inspectionTemplateQuestions)
    {
        //
    }
}
