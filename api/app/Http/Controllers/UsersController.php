<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\AccountRole;
use App\Models\AccountType;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
    public function indexExcept()
    {
        $organization = Organisation::where('uuid', request('organization_id'))->first();
        $organization_users = Organisation::all_users_except_active_user($organization);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'emailAddress' => ['required', 'string', 'email', 'unique:users,email'],
            'phoneNumber' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->symbols()],
            'organization_id' => ['required', 'string'],
            'password_confirmation' => ['required', 'string'],
            'accountRole' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            $createUser = User::create([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['emailAddress'],
                'phone' => $data['phoneNumber'],
                'account_type_id' => (AccountType::where('name', 'individual')->first())->id,
                'account_role_id' => (AccountRole::where('name', AccountRole::role[$data['accountRole']])->first())->id,
                'password' => Hash::make($data['password']),
            ]);

            $organization = Organisation::where('uuid', $data['organization_id'])->first();

            OrganisationUser::create([
                'user_id' => $createUser->id,
                'organization_id' => $organization->id,
            ]);

            logAction($createUser->email, 'Your Account was created and added to organization ', 'Register User', $request->ip());
            logAction(auth()->user()->email, 'You create a user to your organization ', 'Add Team Member', $request->ip());

            return successResponse('Team Member Created Successfully', []);

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
    public function update(Request $request)
    {
        $data = $request->validate([
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'user_id' => ['required', 'string', 'exists:users,uuid'],
            'emailAddress' => ['required', 'string', 'email'],
            'phoneNumber' => ['required', 'string'],
            'organization_id' => ['required', 'string'],
            'accountRole' => ['required', 'string'],
        ]);

        try {

            if (!$user = User::where('uuid', $data['user_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find user");
            }

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            $user->firstName = $data['firstName'];
            $user->lastName = $data['lastName'];
            $user->email = $data['emailAddress'];
            $user->phone = $data['phoneNumber'];
            $user->account_role_id = (AccountRole::where('name', AccountRole::role[$data['accountRole']])->first())->id;
            $user->save();

            $organization = Organisation::where('uuid', $data['organization_id'])->first();

            logAction($user->email, 'Your Account was edited ', 'Team Edit User', $request->ip());
            logAction(auth()->user()->email, 'You edited a user in your organization ', 'Edit Team Member', $request->ip());

            return successResponse('Team Member Edit Successfully', []);

        } catch (Exception $e) {

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
}
