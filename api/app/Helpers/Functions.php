<?php

use App\Helpers\ResponseStatusCodes;
use App\Models\ActionToken;
use App\Models\Audit;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTGuard;

if (!function_exists('arrayKeysToCamelCase')) {

    function arrayKeysToCamelCase($array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = Str::camel($key);
            if (is_array($value)) {
                $value = arrayKeysToCamelCase($value);
            }
            $result[$key] = $value;
        }
        return $result;

    }
}

if (!function_exists('loginAuth')) {
    /**
     * The actual auth service provider
     */
    function loginAuth(): JWTGuard
    {
        /**@var JWTGuard */

        $auth = auth('api');

        $auth->Manager()->getJWTProvider()->setSecret(config('jwt.secret'));

        return $auth;
    }
}

if (!function_exists('passwordReset')) {
    /**
     * The actual auth service provider
     */
    function passwordReset(): JWTGuard
    {
        /**@var JWTGuard */
        $auth = auth('api');

        $auth->Manager()->getJWTProvider()->setSecret(config('jwt.reset_secret'));

        return $auth;
    }
}

if (!function_exists('successResponse')) {
    function successResponse($message = "Successful.", $data = [])
    {
        return response()->json([
            "status" => true,
            "statusCode" => ResponseStatusCodes::OK,
            "message" => $message,
            "data" => $data,
        ], Response::HTTP_OK);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(int $statusCode = ResponseStatusCodes::BAD_REQUEST, string $message = "An error occurred.", $data = [], $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response()->json([
            "status" => false,
            "statusCode" => $statusCode,
            "message" => $message,
            "data" => $data,
        ], $code);
    }
}

if (!function_exists('logAction')) {
    function logAction($user, $action, $description, $ipAddress = null)
    {
        Audit::create([
            'user' => $user,
            'action_performed' => $action,
            'description' => $description,
            'ip_address' => $ipAddress,
        ]);
    }
}

if (!function_exists('generateReference')) {
    function generateReference()
    {
        $dateTime = now();
        $uniqueId = Str::random(8); // Using Laravel's Str::random for simplicity
        $paymentReference = $dateTime->format('YmdHis') . $uniqueId;

        return $paymentReference;
    }
}
if (!function_exists('generateToken')) {
    function generateToken()
    {
        return random_int(100000, 999999);
    }
}

if (!function_exists('getAdminUsers')) {
    function getAdminUsers()
    {
        return User::where('is_admin', 'yes')->get();
    }
}

if (!function_exists('formatDate')) {
    function formatDate($inputDate)
    {
        if (!$inputDate) {
            return '';
        }

        try {
            $dateTime = new DateTime($inputDate);
            return $dateTime->format('M. j, Y');
        } catch (\Throwable $th) {
            return $inputDate;
        }
    }
}

if (!function_exists('createUuid')) {
    function createUuid($model)
    {
        $uuid = Str::uuid()->toString();
        return checkUuid($model, $uuid);

    }
}

if (!function_exists('checkUuid')) {
    function checkUuid($model, $uuid)
    {
        if (!$model::where('uuid', $uuid)->exists()) {
            return $uuid;
        }

        createUuid($model);

    }
}

if (!function_exists('createActionToken')) {
    function createActionToken($email, $type, $token = false)
    {
        $signature = Str::random(50);
        $token = $token ? generateToken() : null;

        return ActionToken::create([
            "email" => $email,
            "signature" => $signature,
            "token" => $token,
            'type' => ActionToken::types[$type],
        ]);

    }
}

if (!function_exists('updateActionToken')) {
    function updateActionToken($model, $status)
    {

        $model->status = $status;
        $model->save();

        return true;

    }
}

if (!function_exists('checkOrganizationOwner')) {
    function checkOrganizationOwner($organization_uuid)
    {

        if (!$organization = Organisation::where('uuid', $organization_uuid)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
        }

        if ($organization->user_id != auth()->user()->id) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
        }

        return $organization;

    }
}

if (!function_exists('findUser')) {
    function findUser($user_uuid)
    {

        if (!$user = User::where('uuid', $user_uuid)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User");
        }

        return $user;

    }
}
