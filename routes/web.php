<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    // dashboard → HomeController@index (formerly HomeController@home)
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [HomeController::class, 'index']);

    // word-cloud JSON endpoint
    Route::get('/wordcloud-data', [HomeController::class, 'wordcloudData']);

    // other authenticated routes…
    Route::get('billing',          fn() => view('billing'))->name('billing');
    Route::get('profile',          fn() => view('profile'))->name('profile');
    Route::get('rtl',              fn() => view('rtl'))->name('rtl');
    Route::get('user-management',  fn() => view('laravel-examples/user-management'))->name('user-management');
    Route::get('tables',           fn() => view('tables'))->name('tables');
    Route::get('virtual-reality',  fn() => view('virtual-reality'))->name('virtual-reality');
    Route::get('static-sign-in',   fn() => view('static-sign-in'))->name('sign-in');
    Route::get('static-sign-up',   fn() => view('static-sign-up'))->name('sign-up');

    Route::get('/logout',          [SessionsController::class, 'destroy']);
    Route::get('/data-sentimen',   [InfoUserController::class, 'create']);
    Route::post('/data-sentimen',  [InfoUserController::class, 'store']);
});

Route::group(['middleware' => 'guest'], function () {
    // show login form
    Route::get('/login', [SessionsController::class, 'create'])
         ->name('login');

    // process login
    Route::post('/session', [SessionsController::class, 'store'])
         ->name('session.store');

    // registration
    Route::get('/register', [RegisterController::class, 'create'])
         ->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // password reset
    Route::get('/login/forgot-password', [ResetController::class, 'create'])
         ->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])
         ->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])
         ->name('password.update');
});

