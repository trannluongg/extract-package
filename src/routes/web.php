<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 23/2/2020
 * Time: 9:32 PM
 */
Route::group(['namespace' => 'Luongtv\Extract\Http\Controllers'], function (){
    Route::get('/extract', 'ExtractController@extract');
});