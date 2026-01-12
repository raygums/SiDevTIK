<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// hasil desain Frontend (Dummy Data)
Route::view('/design/dashboard', 'design.dashboard');
