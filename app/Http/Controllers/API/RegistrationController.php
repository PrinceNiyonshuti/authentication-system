<?php

namespace App\Http\Controllers\API;

use App\Data\AddressData;
use App\Models\TemporaryUser;
use App\Data\PersonalInfoData;
use libphonenumber\PhoneNumberUtil;
use App\Http\Controllers\Controller;
use libphonenumber\PhoneNumberFormat;
use App\Http\Requests\Step2AddressRequest;
use App\Http\Requests\Step1PersonalInfoRequest;

class RegistrationController extends Controller
{
    //
    public function step1(Step1PersonalInfoRequest $request)
    {
        $dto = PersonalInfoData::from($request->validated());

        if (!$dto->honorific_title) {
            $dto->honorific_title = $dto->gender === 'male' ? 'Mr.' : 'Ms.';
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        $parsed = $phoneUtil->parse($dto->phone_number, strtoupper($dto->nationality));
        $phoneInternational = $phoneUtil->format($parsed, PhoneNumberFormat::E164);

        $picturePath = null;
        if ($request->hasFile('profile_picture')) {
            $picturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user = TemporaryUser::create([
            'honorific_title' => $dto->honorific_title,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'gender' => $dto->gender,
            'date_of_birth' => $dto->date_of_birth,
            'email' => $dto->email,
            'nationality' => $dto->nationality,
            'phone_number' => $phoneInternational,
            'profile_picture' => $picturePath,
        ]);

        return response()->json([
            'message' => 'Step 1 completed',
            'registration_id' => $user->id,
            'current_step' => $user->current_step,
        ], 201);
    }

    public function step2(Step2AddressRequest $request)
    {
        $dto = AddressData::from($request->validated());

        $user = TemporaryUser::findOrFail($request->registration_id);

        // Ensure step 1 was completed
        if ($user->current_step !== 1) {
            return response()->json([
                'message' => 'You must complete Step 1 before proceeding.'
            ], 400);
        }

        $isExpat = strtoupper($dto->country_of_residence) !== strtoupper($user->nationality);

        $user->update([
            'country_of_residence' => $dto->country_of_residence,
            'city' => $dto->city,
            'city' => $dto->city,
            'postal_code' => $dto->postal_code,
            'apartment_name' => $dto->apartment_name,
            'room_number' => $dto->room_number,
            'is_expatriate' => $isExpat,
            'current_step' => 2,
        ]);

        return response()->json([
            'message' => 'Step 2 completed',
            'registration_id' => $user->id,
            'current_step' => $user->current_step,
            'is_expatriate' => $user->is_expatriate,
        ], 200);
    }
}
