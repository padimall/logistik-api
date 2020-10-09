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

        Route::group(['prefix' => 'dashboard'], function () {
            Route::post('/data','PackageController@package_service');
        });

        Route::group(['prefix' => 'package'], function () {
            Route::post('/all','PackageController@showAll');
            Route::post('/limit','PackageController@showLimit');
            Route::post('/detail','PackageController@show');
            Route::post('/store','PackageController@store');
            Route::post('/update','PackageController@update');
        });

        Route::group(['prefix' => 'service'], function () {
            Route::post('/all','ServiceController@showAll');
            Route::post('/limit','ServiceController@showLimit');
            Route::post('/detail','ServiceController@show');
            Route::post('/store','ServiceController@store');
            Route::post('/update','ServiceController@update');
        });

        Route::group(['prefix' => 'tracking'], function () {
            Route::post('/send','TrackingController@send');
            Route::post('/mutiple-send','TrackingController@multiplesend');
            Route::post('/receive','TrackingController@receive');
            Route::post('/package','TrackingController@package');
            Route::post('/delete','TrackingController@delete');
        });




    });

    Route::group(['middleware' => ['auth:api','scope:system-token']], function () {



    });

});


