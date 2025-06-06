<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PersonalInfoData extends Data
{
    public function __construct(
        //
        public ?string $honorific_title,
        public string $first_name,
        public string $last_name,
        public string $gender,
        public string $date_of_birth,
        public string $email,
        public string $phone_number,
        public string $nationality,
        public ?string $profile_picture = null,
    ) {}
}
