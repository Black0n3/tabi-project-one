<?php

use App\Livewire\Admin\Buildings\Form as BuildingForm;
use App\Livewire\Admin\Buildings\ShowPage as BuildingShow;
use App\Livewire\Admin\Floors\Form as FloorForm;
use App\Livewire\Admin\Investors\Form as InvestorForm;
use App\Livewire\Admin\Investors\IndexPage as InvestorsIndex;
use App\Livewire\Admin\Investors\ShowPage as InvestorShow;
use App\Livewire\Admin\Projects\Form as ProjectForm;
use App\Livewire\Admin\Projects\ShowPage as ProjectShow;
use App\Livewire\Admin\Rooms\Form as RoomForm;
use App\Livewire\Admin\Units\Form as UnitForm;
use App\Livewire\Admin\Units\ShowPage as UnitShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');

        Route::get('investitori', InvestorsIndex::class)->name('investors.index');
        Route::get('investitori/novi', InvestorForm::class)->name('investors.create');
        Route::get('investitori/{investor}', InvestorShow::class)->name('investors.show');
        Route::get('investitori/{investor}/uredi', InvestorForm::class)->name('investors.edit');

        Route::get('projekti/novi', ProjectForm::class)->name('projects.create');
        Route::get('projekti/{project}', ProjectShow::class)->name('projects.show');
        Route::get('projekti/{project}/uredi', ProjectForm::class)->name('projects.edit');

        Route::get('objekti/novi', BuildingForm::class)->name('buildings.create');
        Route::get('objekti/{building}', BuildingShow::class)->name('buildings.show');
        Route::get('objekti/{building}/uredi', BuildingForm::class)->name('buildings.edit');

        Route::get('katovi/novi', FloorForm::class)->name('floors.create');
        Route::get('katovi/{floor}/uredi', FloorForm::class)->name('floors.edit');

        Route::get('jedinice/novi', UnitForm::class)->name('units.create');
        Route::get('jedinice/{unit}', UnitShow::class)->name('units.show');
        Route::get('jedinice/{unit}/uredi', UnitForm::class)->name('units.edit');

        Route::get('prostorije/novi', RoomForm::class)->name('rooms.create');
        Route::get('prostorije/{room}/uredi', RoomForm::class)->name('rooms.edit');
    });
