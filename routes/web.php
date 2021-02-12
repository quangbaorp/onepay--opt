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


Route::get('reg' , 'HomeController@reg')->name('reg');
Route::get('/', 'HomeController@index')->name('home');
Route::post('/', 'HomeController@onepay')->name('onepay');
Route::get('/respone-onepay' , 'HomeController@responeOnepay')->name('responeOnepay');