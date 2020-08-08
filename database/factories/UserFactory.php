<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Devpri\Tinre\Models\User::class, function (Faker $faker) {
    return [
        'active' => 1,
        'name' => $faker->name(),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'remember_token' => Str::random(10),
    ];
});

$factory->state(Devpri\Tinre\Models\User::class, 'disabled', function () {
    return [
        'active' => 0,
    ];
});

$factory->state(Devpri\Tinre\Models\User::class, 'user', function () {
    return [
        'role' => 'user',
    ];
});

$factory->state(Devpri\Tinre\Models\User::class, 'editor', function () {
    return [
        'role' => 'editor',
    ];
});

$factory->state(Devpri\Tinre\Models\User::class, 'administrator', function () {
    return [
        'role' => 'administrator',
    ];
});
