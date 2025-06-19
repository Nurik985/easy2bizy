<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
	Route::get('/', [UserController::class, 'showLoginForm'])->name('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/campaigns', function (){
        return view('admin.campaigns.index');
    })->name('campaigns');

	Route::get('/reports', function (){
		return view('admin.reports.index');
	})->name('reports');

	Route::get('/settings', function (){
		return view('admin.settings.index');
	})->name('settings');

	Route::get('/staff', function (){
		return view('admin.staff.index');
	})->name('staff');

	Route::get('/statistics', function (){
		return view('admin.statistics.index');
	})->name('statistics');

	Route::post('logout', [UserController::class, 'destroy'])->name('logout');

});
