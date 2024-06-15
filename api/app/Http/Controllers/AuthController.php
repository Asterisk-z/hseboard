<?php

namespace App\Http\Controllers;

use App\Helpers\MailContents;
use App\Helpers\ResponseStatusCodes;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AccountRole;
use App\Models\AccountType;
use App\Models\ActionToken;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use App\Notifications\InfoNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {

        $data = $request->validated();

        DB::beginTransaction();

        try {
            $createUser = User::create([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['emailAddress'],
                'phone' => $data['phoneNumber'],
                'account_type_id' => (AccountType::where('name', $data['accountType'])->first())->id,
                'account_role_id' => (AccountRole::where('name', AccountRole::roles['MEM'])->first())->id,
                'password' => Hash::make($data['password']),
            ]);

            if (AccountType::isCorporate($data['accountType'])) {

                if (Organisation::where('rc_number', $data['rcNumber'])->orWhere('name', $data['orgName'])->count() > 0) {
                    DB::rollBack();
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid signature.");
                }

                $organization = Organisation::create([
                    'name' => strtolower($data['orgName']),
                    'rc_number' => $data['rcNumber'],
                    'bio' => $data['orgBio'],
                    'address' => $data['orgAddress'],
                    'country_id' => $data['country'],
                    'industry_id' => $data['industry'],
                    'user_id' => $createUser->id,
                ]);

                $createUser->account_role_id = (AccountRole::where('name', AccountRole::roles['MAN'])->first())->id;
                $createUser->save();

                OrganisationUser::create([
                    'user_id' => $createUser->id,
                    'organization_id' => $organization->id,
                ]);

                $createUser->active_organization = $organization->uuid;
                $createUser->save();
            }

            $signature = Str::random(50);

            ActionToken::create([
                "email" => $createUser->email,
                "signature" => $signature,
                'type' => ActionToken::types['EV'],
            ]);

            DB::commit();

        } catch (\Throwable $th) {

            DB::rollBack();

        }

        logAction($createUser->email, 'Successful User Registration', 'Registration Successful', $request->ip());

        $createUser->notify(new InfoNotification(MailContents::signupMail($createUser->email, $createUser->created_at->format('Y-m-d'), Crypt::encrypt($signature)), MailContents::signupMailSubject()));

        $admins = getAdminUsers();
        // logger($admins);
        if (count($admins)) {
            Notification::send($admins, new InfoNotification(MailContents::newMembershipSignupMail($createUser->lastName . " " . $createUser->firstName, $request->accountType), MailContents::newMembershipSignupSubject()));
        }

        return successResponse('Account Created Successfully, Check mail to verify mail', $createUser);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = request(['email', 'password']);
        // logger(auth()->attempt($credentials));
        // $token = auth()->setTTL(3000)->attempt($credentials)
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Login details'], 401);
        }

        if (count(auth()->user()->organizations) > 0 && !auth()->user()->active_organization) {
            $user = User::where('id', auth()->user()->id)->first();
            $user->active_organization = auth()->user()->organizations[0]->uuid;
            $user->save();
        }

        // if (!auth()->user()->is_email_verity) {
        //     auth()->logout();
        //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Account Not Verified, Contact Admin info@hseboard.com.");
        // }

        logAction(auth()->user()->email, auth()->user()->full_name . ' Login successful', 'Login Successful', $request->ip());

        return $this->respondWithToken($token);

    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        // return $this->respondWithToken(auth()->refresh());
        return $this->respondWithToken(auth()->setTTL(30000)->refresh());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function validateAccount(Request $request)
    {
        $request->validate([
            "signature" => "required|string",
            "email" => "required|string|email",
            "type" => 'required|string',
        ]);

        $signature = Crypt::decrypt($request->signature);

        if (!$actionToken = ActionToken::where('signature', $signature)->where('type', ActionToken::types[$request->type])->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid signature.");
        }

        if ($actionToken->status == "completed") {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "You have initially completed this process. Kindly proceed to login.");
        }

        if ($actionToken->email != $request->email) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid Email Address.");
        }

        $actionToken->status = "completed";
        $actionToken->save();

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        // return successResponse("You have successfully validated your account. Kindly login with your new credentials.");
        return redirect(config('app.web_url') . '/auth/login');
    }

    protected function respondWithToken($token)
    {
        $user = auth()->user();
        // logger($user);
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'message' => "Login Successful",
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        try {
            $token = generateToken();
            $signature = Str::random(50);

            $saveToken = ActionToken::create([
                "email" => $request->email,
                "signature" => $signature,
                "token" => $token,
                'type' => ActionToken::types['PR'],
            ]);

            if ($saveToken) {

                $user->notify(new InfoNotification(MailContents::forgotPasswordMail($user->email, Crypt::encrypt($signature), $token), MailContents::forgotPasswordSubject()));

                logAction($user->email, 'OTP Generation successful', 'OTP Generation successful', $request->ip());

                return successResponse('Enter the OTP sent to your email address');

            } else {
                logAction($user->email, 'OTP Generation failed', 'Unable to complete your request', $request->ip());
                return errorResponse();
            }
        } catch (\Throwable $th) {
            logger($th);
            logAction($user->email, 'OTP Generation failed', 'Unable to complete your request', $request->ip());
            return errorResponse();
        }

    }

    public function validateForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:4|max:6',
            'email' => 'required|email|exists:users,email',
            "signature" => "required|string",
            "type" => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        $signature = Crypt::decrypt($request->signature);

        if (!$actionToken = ActionToken::where('signature', $signature)
            ->where('token', $request->otp)
            ->where('status', 'pending')
            ->where('type', ActionToken::types[$request->type])
            ->whereBetween('created_at', [now()->subMinutes(15), now()])->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid OTP.");
        }

        $response = $actionToken;

        if ($response) {
            $data = [
                "reset" => [
                    "type" => "bearer",
                    "reset_token" => passwordReset()->fromUser($user),
                    "email" => $user->email,
                ],
            ];

            $actionToken->status = "completed";
            $actionToken->save();

            logAction($user->email, 'OTP verification successful', 'OTP verification successful', $request->ip());

            return successResponse("Otp verified. Proceed to change your password.", $data);

        } else {
            logAction($user->email, 'OTP verification failed', 'Unable to complete your request', $request->ip());
            return errorResponse();
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ]);

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->input('password'));
        $user->save();

        logAction($request->input('email'), 'Reset Password successful', 'Reset Password successful', $request->ip());
        return successResponse("Your password has been reset successfully.");
    }

    public function resetToken()
    {

        if (!$organization = Organisation::where('uuid', auth()->user()->active_organization)->where('user_id', auth()->user()->id)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Can not update organization token.");
        }

        $organization->token = $organization->refreshToken();

        $organization->save();

        logAction(auth()->user()->email, 'Organization Token Update successful', 'Organization Token update successful', request()->ip());
        return successResponse('Organization Token Update successful');
    }

    public function updateDetail(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'ispon' => 'nullable|string',
            'phone' => 'required|string',
        ]);

        $user = User::where('id', auth()->user()->id)->first();
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        // $user->email = $request->email;
        $user->ispon = $request->ispon ? $request->ispon : $user->ispon;
        $user->phone = $request->phone;
        $user->save();

        logAction($request->input('email'), 'Update User Details successful', 'Data successful', $request->ip());
        return successResponse("Updated successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file'],
        ]);

        try {

            storeMedia(request('image'), User::class, auth()->user()->id, 'profile_photo', auth()->user()->media ? auth()->user()->media->id : null);

            logAction(auth()->user()->email, 'User Photo updated successfully ', 'User Photo', $request->ip());

            return successResponse('User Photo updated successful ', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
}
