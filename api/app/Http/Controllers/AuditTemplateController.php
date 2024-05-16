<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\AuditTemplate;
use App\Models\AuditTemplateQuestion;
use App\Models\AuditType;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class AuditTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = AuditTemplate::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            });
            $stats = $stats->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Audit Template Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Audit Template Fetched Successfully', []);

        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function single($template_id)
    {

        try {

            $stats = AuditTemplate::where('is_del', 'no')->where('audit_type_id', request('type_id'));

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {

                $query->orWhere('organization_id', $organization->id);
                $query->orWhere('organization_id', null);

            });
            // $stats = $stats->whereIn('organization_id', [null, $organization->id]);
            $stats = $stats->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Audit Template Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Audit Template Fetched Successfully', []);

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
        // logger(request('questions'));
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'audit_type_id' => ['required'],
            // 'file' => ['required', 'mimes:xls,xlsx', 'max:5000'],
            'questions' => ['required', 'array'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$organization = AuditType::where('id', $data['audit_type_id'])->where('is_del', 'no')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Audit Template");
            }

            $template = AuditTemplate::create([
                'organization_id' => $organization->id,
                'user_id' => auth()->user()->id,
                'title' => request('title'),
                'description' => request('description'),
                'audit_type_id' => request('audit_type_id'),
                'file_id' => 1,
            ]);

            $group = null;
            $header = null;
            $topic = null;
            $questions = request('questions');
            foreach ($questions as $question) {

                if (strstr($question, '[GROUP]')) {
                    if (AuditTemplateQuestion::where('title', str_replace('[GROUP]', '', $question))->where('audit_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $group = AuditTemplateQuestion::create([
                        'question' => null,
                        'title' => str_replace('[GROUP]', '', $question),
                        'audit_template_id' => $template->id,
                        'group_id' => null,
                        'header_id' => null,
                        'topic_id' => null,
                    ]);
                } else if (strstr($question, '[HEADER]')) {
                    if (AuditTemplateQuestion::where('title', str_replace('[HEADER]', '', $question))->where('audit_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $header = AuditTemplateQuestion::create([
                        'question' => null,
                        'title' => str_replace('[HEADER]', '', $question),
                        'audit_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => null,
                        'topic_id' => null,
                    ]);
                } else if (strstr($question, '[TOPIC]')) {
                    if (AuditTemplateQuestion::where('title', str_replace('[TOPIC]', '', $question))->where('audit_template_id', $template->id)->exists()) {
                        continue;
                    }
                    $topic = AuditTemplateQuestion::create([
                        'question' => null,
                        'title' => str_replace('[TOPIC]', '', $question),
                        'audit_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => $header ? $header->id : null,
                        'topic_id' => null,
                    ]);
                } else {
                    if (AuditTemplateQuestion::where('question', $question)->where('audit_template_id', $template->id)->exists()) {
                        continue;
                    }
                    AuditTemplateQuestion::create([
                        'question' => $question,
                        'title' => null,
                        'audit_template_id' => $template->id,
                        'group_id' => $group ? $group->id : null,
                        'header_id' => $header ? $header->id : null,
                        'topic_id' => $topic ? $topic->id : null,
                    ]);
                }

            }

            // logAction(auth()->user()->email, 'You started an investigation ', 'Start Investigation', $request->ip());

            return successResponse('Audit Document started Successfully', []);

        } catch (Exception $e) {
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

            if (!$action = AuditTemplate::where('id', $data['template_id'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Template");
            }

            $action->is_del = 'yes';
            $action->save();

            logAction(auth()->user()->email, 'You deleted a Template ', 'Template Delete', $request->ip());
            // SENDMAIl

            return successResponse('Template Deleted Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AuditTemplate  $auditTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(AuditTemplate $auditTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditTemplate  $auditTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AuditTemplate $auditTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditTemplate  $auditTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuditTemplate $auditTemplate)
    {
        //
    }
}
