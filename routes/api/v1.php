<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::get('users', 'UserController@index');
    Route::get('users/me', 'LoggedUserController@show');
    Route::get('users/{id}', 'UserController@show');

    Route::get('urls', 'UrlController@index');
    Route::post('urls', 'UrlController@create');
    Route::get('urls/{path}', 'UrlController@show');
    Route::post('urls/{id}', 'UrlController@update');
    Route::delete('urls/{id}', 'UrlController@delete');

    Route::get('stats/{id}/clicks', 'StatsController@clicks');
    Route::get('stats/{id}/{column}', 'StatsController@data');
});
