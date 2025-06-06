<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Step2AddressRequest extends FormRequest
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
            'country_of_residence' => 'required|string|size:2',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'apartment_name' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:100',
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
