<?php

namespace App\Http\Controllers;

use App\Models\AuditType;
use Exception;

class AuditTypeController extends Controller
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
}
