<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('filament.dashboard.auth.login');
})->name('login');

// Route::get('/admin/report/{key}', function ($key) {
//     return view('report', ['key' => $key]);
// })->name('report');
