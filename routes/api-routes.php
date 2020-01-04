<?php

use Illuminate\Support\Facades\Route;

Route::middleware(
    array_merge(
        config('looden.auth.api') ? ['auth:api', 'api'] : ['api'],
        config('looden.auth.middlewares.api') ?: []
    ))->group(function (){
    Route::group(['prefix' => 'api'], function (){
        Route::get('describe/{model?}', '\Looden\Framework\Controller\APIController@describe');
        Route::get('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@show');
        Route::delete('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@destroy');
        Route::delete('{slug}/{field?}', '\Looden\Framework\Controller\APIController@destroyCollection');
        Route::get('{slug}', '\Looden\Framework\Controller\APIController@index');
        Route::post('{slug}', '\Looden\Framework\Controller\APIController@store');
        Route::post('{slug}/raw', '\Looden\Framework\Controller\APIController@storeRaw');
        Route::post('{slug}/collection', '\Looden\Framework\Controller\APIController@storeColl');
        Route::post('{slug}/collection/raw', '\Looden\Framework\Controller\APIController@storeCollRaw');
        Route::put('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@update');
        Route::put('{slug}/raw/{id}/{field?}', '\Looden\Framework\Controller\APIController@updateRaw');
        Route::put('{slug}/collection/{field?}', '\Looden\Framework\Controller\APIController@updateColl');
        Route::put('{slug}/collection/raw/{field?}', '\Looden\Framework\Controller\APIController@updateCollRaw');
        Route::post('{slug}/validate/{id?}/{field?}', '\Looden\Framework\Controller\APIController@runValidation');
    });
});

Route::middleware(    array_merge(
    config('looden.auth.web') ? ['web', 'auth'] : ['web'],
    config('looden.auth.middlewares.web') ?: []
))->group(function (){
    Route::group(['prefix' => 'webapi'], function (){
        Route::get('describe/{model?}', '\Looden\Framework\Controller\APIController@describe');
        Route::get('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@show');
        Route::delete('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@destroy');
        Route::delete('{slug}/{field?}', '\Looden\Framework\Controller\APIController@destroyCollection');
        Route::get('{slug}', '\Looden\Framework\Controller\APIController@index');
        Route::post('{slug}', '\Looden\Framework\Controller\APIController@store');
        Route::post('{slug}/raw', '\Looden\Framework\Controller\APIController@storeRaw');
        Route::post('{slug}/collection', '\Looden\Framework\Controller\APIController@storeColl');
        Route::post('{slug}/collection/raw', '\Looden\Framework\Controller\APIController@storeCollRaw');
        Route::put('{slug}/{id}/{field?}', '\Looden\Framework\Controller\APIController@update');
        Route::put('{slug}/raw/{id}/{field?}', '\Looden\Framework\Controller\APIController@updateRaw');
        Route::put('{slug}/collection/{field?}', '\Looden\Framework\Controller\APIController@updateColl');
        Route::put('{slug}/collection/raw/{field?}', '\Looden\Framework\Controller\APIController@updateCollRaw');
        Route::post('{slug}/validate/{id?}/{field?}', '\Looden\Framework\Controller\APIController@runValidation');
    });
});