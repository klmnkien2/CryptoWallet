<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Wallet
 */
Route::get('/wallets', 'WalletController@index');
Route::post('/wallets/create', 'WalletController@create');
Route::delete('/wallets/destroy/{id}', 'WalletController@destroy');
Route::get('/wallets/reload/{address}', 'WalletController@reload');
Route::post('/wallets/transaction/prepare', 'WalletController@prepareTransaction');
Route::post('/wallets/transaction/send', 'WalletController@sendTransaction');
