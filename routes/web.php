<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
	Route::get('/', [UserController::class, 'login'])->name('login');
	//Route::post('/', [UserController::class, 'loginAuth'])->name('login.auth');
});
