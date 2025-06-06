<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TemporaryUser;
use App\Data\PersonalInfoData;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
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
}
