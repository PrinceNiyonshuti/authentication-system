<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        //
        public string $country_of_residence,
        public string $city,
        public string $postal_code,
        public ?string $apartment_name = null,
        public ?string $room_number = null,

    ) {}
}
