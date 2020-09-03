<?php

use Faker\Generator as Faker;

$factory->define(Devpri\Tinre\Models\Click::class, function (Faker $faker) {
    return [
        'country' => $faker->countryCode,
        'region' => $faker->state,
        'city' => $faker->city,
        'device_type' => $faker->word,
        'device_brand' => $faker->word,
        'device_model' => $faker->word,
        'os' => $faker->word,
        'browser' => $faker->word,
        'referer' => $faker->url,
        'referer_host' => $faker->domainName,
        'user_agent' => $faker->userAgent,
    ];
});
