<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'ClaimController@index')->middleware('auth');

    $claimsMethods = ['index', 'create', 'store', 'show'];
    Route::resource('claims', 'ClaimController')->only($claimsMethods);
    Route::post('/claims/{claim}/assign/{user}', 'ClaimController@assign')->name('claims.assign');
    Route::post('/claims/{claim}/close', 'ClaimController@close')->name('claims.close');

    Route::get('/files/{id}', 'FileController@download')->name('files.download');
});
