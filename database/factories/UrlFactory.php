<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Devpri\Tinre\Models\Url::class, function (Faker $faker) {
    return [
        'path' => Str::uuid(),
        'long_url' => $faker->url,
    ];
});

$factory->state(Devpri\Tinre\Models\Url::class, 'disabled', function () {
    return [
        'active' => 0,
    ];
});
