<?php

namespace App\Http\Controllers;

use App\Helpers\MailContents;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AccountType;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\PasswordSet;
use App\Models\User;
use App\Notifications\InfoNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {

        $data = $request->validated();

        $createUser = User::create([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['emailAddress'],
            'phone' => $data['phoneNumber'],
            'account_type_id' => (AccountType::where('name', $data['accountType'])->first())->id,
            'password' => Hash::make($data['password']),
        ]);

        if (AccountType::isCorporate($data['accountType'])) {

            $organization = Organisation::create([
                'name' => $data['orgName'],
                'bio' => $data['orgBio'],
                'address' => $data['orgAddress'],
                'country_id' => $data['country'],
                'industry_id' => $data['industry'],
                'user_id' => $createUser->id,
            ]);

            $relation = OrganisationUser::create([
                'user_id' => $createUser->id,
                'organization_id' => $organization->id,
            ]);

        }

        $signature = Str::random(50);

        PasswordSet::create([
            "email" => $createUser->email,
            "signature" => $signature,
        ]);

        logAction($createUser, 'Successful User Registration', 'Registration Successful', $request->ip());

        $createUser->notify(new InfoNotification(MailContents::signupMail($createUser->email, $createUser->created_at->format('Y-m-d'), Crypt::encrypt($signature)), MailContents::signupMailSubject()));

        $admins = getAdminUsers();

        if (count($admins)) {
            Notification::send($admins, new InfoNotification(MailContents::newMembershipSignupMail($createUser->lastName . " " . $createUser->firstName, $request->accountType), MailContents::newMembershipSignupSubject()));
        }

        //Send Email to verify user

        return successResponse('Account Created Successfully', $createUser);

    }

    public function login(Request $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);

    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
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
