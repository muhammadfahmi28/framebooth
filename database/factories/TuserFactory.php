<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tusers>
 */
class TuserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uid' => Str::random(6) . time() . Str::random(2),
            'code' => md5(Hash::make( '' . time() . Str::random(4))),
            'max_photos' => env("VAR_DEFAULT_MAX_PHOTOS", 3),
            'valid_until' => Carbon::today()->addDays(env("VAR_DEFAULT_DAYS_VALID", 60))
        ];
    }
}
