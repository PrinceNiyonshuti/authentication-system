<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use App\Models\TemporaryUser;
use League\ISO3166\ISO3166;

class Step1PersonalInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'honorific_title' => 'nullable|in:Mr.,Mrs.,Miss,Ms.,Dr.,Prof.,Hon.',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'email' => [
                'required',
                'email',
                'unique:temporary_users,email',
                'regex:/^(?!.*@(tempmail|10minutemail|yopmail|mailinator|guerrillamail|trashmail|fakeinbox|discardmail)\.)[^\s@]+@[^\s@]+\.[^\s@]+$/i'
            ],
            'phone_number' => 'required|string',
            'nationality' => 'required|string|size:2',
            'profile_picture' => 'nullable|file|mimes:png|max:2048',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $this->validatePhoneWithNationality($validator);
            $this->validateHonorificTitleByGender($validator);
        });
    }

    protected function validatePhoneWithNationality($validator)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $regionCode = strtoupper($this->input('nationality'));
            $rawPhone = $this->input('phone_number');

            // Step 0: Require '+' prefix
            if (!str_starts_with($rawPhone, '+')) {
                $validator->errors()->add('phone_number', 'Phone number must start with "+" and include the country code.');
                return;
            }

            // Step 1: Parse strictly as international number
            $parsed = $phoneUtil->parse($rawPhone, null);

            // Step 2: Check global validity
            if (!$phoneUtil->isValidNumber($parsed)) {
                $validator->errors()->add('phone_number', 'Phone number format is invalid.');
                return;
            }

            // Step 3: Ensure actual region matches nationality
            $actualRegion = $phoneUtil->getRegionCodeForNumber($parsed);
            if ($actualRegion !== $regionCode) {
                $validator->errors()->add(
                    'phone_number',
                    'Phone number belongs to ' . $this->getCountryName($actualRegion) .
                    ', but you selected ' . $this->getCountryName($regionCode) . ' as nationality.'
                );
                return;
            }

            // Step 4: Normalize phone
            $formatted = $phoneUtil->format($parsed, \libphonenumber\PhoneNumberFormat::E164);

            // Step 5: Uniqueness check
            if (TemporaryUser::where('phone_number', $formatted)->exists()) {
                $validator->errors()->add('phone_number', 'This phone number is already in use.');
                return;
            }

            // Step 6: Inject normalized phone back into request
            $this->merge(['phone_number' => $formatted]);

        } catch (NumberParseException $e) {
            $validator->errors()->add('phone_number', 'Could not parse phone number.');
        }
    }

    protected function validateHonorificTitleByGender($validator)
    {
        $gender = strtolower($this->input('gender'));
        $title = $this->input('honorific_title');

        if (!$title) return; // skip if null, we auto-assign in controller

        $validTitles = [
            'male' => ['Mr.', 'Dr.', 'Prof.', 'Hon.'],
            'female' => ['Mrs.', 'Miss', 'Ms.', 'Dr.', 'Prof.', 'Hon.'],
        ];

        if (!isset($validTitles[$gender])) return;

        if (!in_array($title, $validTitles[$gender])) {
            $validator->errors()->add('honorific_title', "Invalid honorific title for gender '$gender'.");
        }
    }


    protected function getCountryName(string $alpha2): string
    {
        try {
            return (new ISO3166)->alpha2($alpha2)['name'];
        } catch (\Throwable $e) {
            return $alpha2; // fallback
        }
    }

    protected function failedValidation(ValidatorContract $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
