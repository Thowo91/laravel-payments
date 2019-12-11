<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/klarna/payment', 'KlarnaController@test');
Route::get('/klarna/checkout', 'KlarnaController@checkout');

Route::get('/paypalplus', 'PaypalPlusController@test');
Route::get('/paypalplus/return', 'PaypalPlusController@returnUrl')->name('paypalplus.return');
Route::get('/paypalplus/cancel', 'PaypalPlusController@cancelUrl')->name('paypalplus.cancel');
