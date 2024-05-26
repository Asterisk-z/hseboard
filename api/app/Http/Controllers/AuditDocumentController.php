<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\AuditDocument;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class AuditDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $stats = AuditDocument::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Audit Document Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);
            return successResponse('Audit Document Fetched Successfully', []);

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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'file' => ['required'],
            'organization_id' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            $audit_document = AuditDocument::create([
                'organization_id' => $organization->id,
                'user_id' => auth()->user()->id,
                'title' => request('title'),
                'description' => request('description'),
                'file_id' => 1,
            ]);

            storeMedia(request('file'), AuditDocument::class, $audit_document->id, 'audit_documents', $audit_document->media ? $audit_document->media->id : null);

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
     * @param  \App\Models\AuditDocument  $auditDocument
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {

        $data = $request->validate([
            'document_id' => ['required'],
        ]);

        try {

            if (!$action = AuditDocument::where('id', $data['document_id'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Document");
            }

            $action->is_del = 'yes';
            $action->save();

            logAction(auth()->user()->email, 'You deleted a document ', 'Document Delete', $request->ip());
            // SENDMAIl

            return successResponse('Document Deleted Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AuditDocument  $auditDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(AuditDocument $auditDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditDocument  $auditDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AuditDocument $auditDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditDocument  $auditDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuditDocument $auditDocument)
    {
        //
    }
}
