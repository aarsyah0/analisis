<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\WelcomeController;  // ← Pastikan di‐import
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
|
| Untuk guest, root (/) akan men‐trigger WelcomeController@index,
| sehingga view 'welcome' menerima data $counts.
|
*/

Route::get('/wordcloud-data', [WelcomeController::class, 'wordcloudData'])
     ->name('wordcloud.data');
Route::group(['middleware' => 'guest'], function () {
    // root → WelcomeController@index (mengirimkan $counts ke welcome.blade.php)
    Route::get('/', [WelcomeController::class, 'index'])->name('home');

    // Rute login / register / reset password
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store'])->name('session.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Setelah login, user diarahkan ke /dashboard dan route lain milik middleware 'auth'.
| Jika welcome.blade.php juga memerlukan wordcloud-data, pastikan URL‐nya sesuai.
|
*/
Route::group(['middleware' => 'auth'], function () {
    // dashboard → HomeController@index
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // // Jika WordCloud diakses dari welcome juga, pastikan route ke WelcomeController
    // Route::get('/wordcloud-data', [HomeController::class, 'wordcloudData'])
    //      ->name('wordcloud.data');

    // Halaman‐halaman lain
    Route::get('billing',          fn() => view('billing'))->name('billing');
    Route::get('profile',          fn() => view('profile'))->name('profile');
    Route::get('rtl',              fn() => view('rtl'))->name('rtl');
    Route::get('user-management',  fn() => view('laravel-examples/user-management'))->name('user-management');
    Route::get('tables',           fn() => view('tables'))->name('tables');
    Route::get('virtual-reality',  fn() => view('virtual-reality'))->name('virtual-reality');
    Route::get('static-sign-in',   fn() => view('static-sign-in'))->name('sign-in');
    Route::get('static-sign-up',   fn() => view('static-sign-up'))->name('sign-up');

    // logout
    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');

    // data-sentimen
    Route::get('/data-sentimen',  [InfoUserController::class, 'create'])->name('data.sentimen.create');
    Route::post('/data-sentimen', [InfoUserController::class, 'store'])->name('data.sentimen.store');
});
