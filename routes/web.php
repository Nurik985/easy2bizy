<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
	Route::get('/', [UserController::class, 'showLoginForm'])->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function (){
        return view('admin.index');
    })->name('dashboard');
});
