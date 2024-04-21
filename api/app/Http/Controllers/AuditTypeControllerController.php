<?php

namespace App\Http\Controllers;

use App\Models\AuditType;
use App\Models\AuditTypeController;
use Exception;
use Illuminate\Http\Request;

class AuditTypeControllerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = AuditType::where('is_del', 'no')->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Audit Type Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Audit Type Fetched Successfully', []);

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditTypeController  $auditTypeController
     * @return \Illuminate\Http\Response
     */
    public function show(AuditTypeController $auditTypeController)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AuditTypeController  $auditTypeController
     * @return \Illuminate\Http\Response
     */
    public function edit(AuditTypeController $auditTypeController)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditTypeController  $auditTypeController
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AuditTypeController $auditTypeController)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditTypeController  $auditTypeController
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuditTypeController $auditTypeController)
    {
        //
    }
}
