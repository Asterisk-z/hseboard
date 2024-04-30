<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setOrganization(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
        ]);
        try {
            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");

            }

            if (!$user = Organisation::find_user($organization, auth()->user()->email)) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User ");

            }

            $user->active_organization = $organization->uuid;
            $user->save();

            return successResponse('Organizations Set Successfully', $organization);

        } catch (Exception $e) {

        }

    }

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
