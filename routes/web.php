<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('users', 'UserController@index');
    Route::get('users/me', 'LoggedUserController@show');
    Route::post('users/me', 'LoggedUserController@update');
    Route::get('users/{id}', 'UserController@show');
    Route::post('users', 'UserController@create');
    Route::post('users/{id}', 'UserController@update');
    Route::delete('users/{id}', 'UserController@delete');

    Route::get('urls', 'UrlController@index');
    Route::get('urls/{path}', 'UrlController@show');
    Route::post('urls/{id}', 'UrlController@update');
    Route::delete('urls/{id}', 'UrlController@delete');

    Route::get('stats/{id}/clicks', 'StatsController@clicks');
    Route::get('stats/{id}/{column}', 'StatsController@data');

    Route::get('access-tokens', 'AccessTokenController@index');
    Route::post('access-tokens', 'AccessTokenController@create');
    Route::get('access-tokens/{id}', 'AccessTokenController@show');
    Route::post('access-tokens/{id}', 'AccessTokenController@update');
    Route::delete('access-tokens/{id}', 'AccessTokenController@delete');
});

Route::post('urls', 'UrlController@create')->middleware(config('tinre.guest_form') ? [] : ['auth']);
