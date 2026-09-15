<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:investor'])
    ->prefix('investitor')
    ->name('investitor.')
    ->group(function () {
        Route::view('dashboard', 'investitor.dashboard')->name('dashboard');
    });
