<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 23/2/2020
 * Time: 9:32 PM
 */
Route::group(['namespace' => 'WorkableCV\Extract\Http\Controllers'], function ()
{
    Route::get('/extract', 'ExtractController@extract');
    Route::get('/extract-wkh', 'ExtractController@extractWkh');
    Route::get('/extract-pdf', 'ExtractController@generate');
    Route::get('/extract-doc', 'ExtractController@convertDocToPdf');
});
