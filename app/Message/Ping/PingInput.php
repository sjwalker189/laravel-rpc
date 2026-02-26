<?php

namespace App\Message\Ping;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PingInput extends Data
{
    public function __construct(
        #[Required, Min(6), Max(100)]
        public string $message,
    ) {}

    // public static function rules(): array
    // {
    //     return [
    //         'message' => ['required', 'string', 'min:6', 'max:100'],
    //     ];
    // }
}
