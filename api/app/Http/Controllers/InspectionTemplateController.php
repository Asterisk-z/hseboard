<?php

namespace App\Http\Controllers;

use App\Models\InspectionTemplate;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class InspectionTemplateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_type()
    {
        try {

            $stats = InspectionTemplate::where('is_del', 'no');
            // $stats = InspectionTemplate::where('is_del', 'no')->where('inspection_template_type_id', request('type_id'));

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {

                $query->orWhere('organization_id', $organization->id);
                $query->orWhere('organization_id', null);

            });
            $stats = $stats->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Inspection Template Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Inspection Template Fetched Successfully', []);

        }

    }

}
