<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', function () {
    return redirect()->route(
        request()->user()->role === UserRole::Admin ? 'admin.dashboard' : 'investitor.dashboard'
    );
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/investitor.php';
