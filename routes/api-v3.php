<?php
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => '/auth', 'middleware' => ['app_language']], function() {
    Route::post('signup', 'App\Http\Controllers\Api\V3\AuthController@signup');
});
