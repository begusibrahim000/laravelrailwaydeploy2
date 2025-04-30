<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/detail', function () {
    return view('detail');
});

Route::get('/hasil', function () {
    return view('hasil');
});

Route::get('/header-search', function () {
    return view('header-search');
});

Route::get('/jadwal', function () {
    return view('jadwal');
});

Route::get('/klasemen', function () {
    return view('klasemen');
});

Route::get('/search', function () {
    return view('search');
});

Route::get('/team', function () {
    return view('team');
});

Route::get('/livescores', function () {
    return view('livescores');
});
