<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verifikator', function () {
    return view('verifikator.dashboard');
});
