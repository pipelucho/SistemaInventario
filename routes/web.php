<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return (redirect('/admin'));
});

Route::get('/login', function () {
    return (redirect('/admin'));
});

// ruta para logout
Route::get('/logout', function () {
    Auth::logout();
    return (redirect('/admin'));
});

Route::get('admin/logout', function () {
    Auth::logout();
    return (redirect('/admin'));
});

