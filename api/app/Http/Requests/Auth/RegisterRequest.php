<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "accountType" => ["required", Rule::in(['individual', 'corporate'])],
            "firstName" => "required|string",
            "lastName" => "required|string",
            "emailAddress" => "required|string",
            "phoneNumber" => "required|string",
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->symbols()],
            "password_confirmation" => "required",

            "orgName" => "required_if:accountType,corporate|string",
            "orgBio" => "required_if:accountType,corporate|string",
            "orgAddress" => "required_if:accountType,corporate|string",
            "country" => "required_if:accountType,corporate|integer",
            "industry" => "required_if:accountType,corporate|integer",
        ];
    }
}
