<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicioL');
    return view('welcome');
});

Route::get('/inicioL', function () {
    return view('inicioL');
});

Route::get('/inicioN', function () {
    return view('inicioN');
});
