<?php

use Illuminate\Support\Facades\Route;

Route::post('usuario', 'App\Http\Controllers\UsuarioController@insert');

Route::post('reset', 'App\Http\Controllers\UsuarioController@resetPassword');

Route::group(['middleware' => ['api']], function(){
    Route::post('session', 'App\Http\Controllers\SessionController@login');
    Route::delete('session', 'App\Http\Controllers\SessionController@logout');

    Route::group(['middleware' => ['authUser']], function(){
        Route::put('usuario/{id}', 'App\Http\Controllers\UsuarioController@update');
        Route::get('usuario', 'App\Http\Controllers\UsuarioController@select');

        Route::post('avatar', 'App\Http\Controllers\UsuarioController@avatarUpload');

        Route::get('planos', 'App\Http\Controllers\PlanoController@selectAll');
        Route::get('plano/{id}', 'App\Http\Controllers\PlanoController@select');

        Route::post('assinatura/{id}' , 'App\Http\Controllers\AssinaturaController@insert');
        Route::get('assinatura/{id}' , 'App\Http\Controllers\AssinaturaController@select');
        Route::post('cancel/{id}' , 'App\Http\Controllers\AssinaturaController@cancel');
        
        Route::group(['middleware' => ['authAdm']], function(){
            Route::delete('usuario/{id}', 'App\Http\Controllers\UsuarioController@delete');

            Route::post('plano', 'App\Http\Controllers\PlanoController@insert');
            Route::put('plano/{id}', 'App\Http\Controllers\PlanoController@update');
        });
    });
});
