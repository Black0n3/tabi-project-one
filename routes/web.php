<?php

use App\Enums\UserRole;
use App\Http\Controllers\SitemapController;
use App\Livewire\Public\BuildingPage;
use App\Livewire\Public\HomePage;
use App\Livewire\Public\ProjectPage;
use App\Livewire\Public\ProjectsIndex;
use App\Livewire\Public\UnitPage;
use App\Livewire\Public\UnitsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('projekti', ProjectsIndex::class)->name('public.projects.index');
Route::get('projekti/{project}', ProjectPage::class)->name('public.projects.show');
Route::get('objekti/{building}', BuildingPage::class)->name('public.buildings.show');
Route::get('jedinice', UnitsIndex::class)->name('public.units.index');
Route::get('jedinice/{unit}', UnitPage::class)->name('public.units.show');

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
