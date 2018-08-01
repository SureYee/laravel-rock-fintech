<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-01
 * Time: 17:50
 */

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'rock' ],function () {
    Route::post('callback')->name('rft-callback');
});