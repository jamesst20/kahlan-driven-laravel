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

Route::get('admin', function () {
    return 'admin area for logged in user only';
})->middleware('auth');

Route::get('login', function () {
    return 'login form for guests only';
})->middleware('guest');

Route::get('session-test', function () {
    return session('session_test');
});

