<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use App\Models\User;
use Exception;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = Organisation::where('uuid', request('organization_id'))->first();
        $organization_users = Organisation::all_users($organization);

        $converted_organization = arrayKeysToCamelCase($organization_users);

        return successResponse('Users Fetched Successfully', $converted_organization);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try {

            $organization = Organisation::where('uuid', request('organization_id'))->first();
            $user = User::where('uuid', request('user_id'))->first();
            $organization_user = Organisation::find_user($organization, $user->email);

            return successResponse('User Fetched Successfully', $organization_user);

        } catch (Exception $e) {

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
}
