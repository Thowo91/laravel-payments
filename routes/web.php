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

Route::prefix('klarna')
    ->group(function() {
        Route::get('/checkout', 'KlarnaController@checkout')->name('klarna.checkout');
        Route::get('/return/confirmation', 'KlarnaController@returnConfirmation')->name('klarna.return.confirmation');
    });

Route::prefix('paypalplus')
    ->group(function() {
        Route::get('/', 'PaypalPlusController@test')->name('paypalplus');
        Route::get('/return', 'PaypalPlusController@returnUrl')->name('paypalplus.return');
        Route::get('/cancel', 'PaypalPlusController@cancelUrl')->name('paypalplus.cancel');
        Route::get('/paymentInfo/{paymentId}', 'PaypalPlusController@paymentInfo')->name('paypalplus.paymentinfo');
        Route::get('/paymentAll', 'PaypalPlusController@paymentAll')->name('paypalplus.paymentall');
    });

Route::prefix('stripe')
    ->group(function() {
        Route::get('/', 'StripeController@index')->name('stripe');
        Route::get('/sucess', 'StripeController@sucessUrl')->name('stripe.sucess');
        Route::get('/cancel', 'StripeController@cancelUrl')->name('stripe.cancel');
    });
