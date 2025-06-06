<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Step4PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registration_id' => 'required|uuid|exists:temporary_users,id',
            'password' => [
                'required',
                'confirmed',       // checks password_confirmation
                'min:8',           // minimum 8 characters
                'regex:/[a-z]/',   // at least one lowercase
                'regex:/[A-Z]/',   // at least one uppercase
                'regex:/[0-9]/',   // at least one digit
                'regex:/[@$!%*#?&]/', // at least one special character
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password.regex:/[a-z]/' => 'Password must include at least one lowercase letter.',
            'password.regex:/[A-Z]/' => 'Password must include at least one uppercase letter.',
            'password.regex:/[0-9]/' => 'Password must include at least one number.',
            'password.regex:/[@$!%*#?&]/' => 'Password must include at least one special character (@$!%*#?&).',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
