<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::group(['prefix' => 'v1'], function () {
    Route::post('signup','AuthController@signup');
    Route::post('login','AuthController@login');
    Route::post('login-dev','AuthController@login_dev');

    Route::group(['middleware' => ['auth:api','scopes:system-token,user-token']], function () {

        Route::post('/user',function(){
            return response()->json(request()->user());
        });

        Route::group(['prefix' => 'user'], function () {
            Route::post('/update','AuthController@update');
        });


    });

    Route::group(['middleware' => ['auth:api','scope:system-token']], function () {



    });

});


