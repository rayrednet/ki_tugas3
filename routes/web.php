<?php

use App\Http\Controllers\Autentikasi\ControllerAutentikasi;
use App\Http\Controllers\ControllerProfile;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => [
    'auth'
]], function() {
    Route::get('/', function () {
        return redirect()->route('profile.index');
    });

    Route::group([
        'prefix' => 'profile',
        'as' => 'profile.',
    ], function() {
        Route::get('/', [ControllerProfile::class, 'index'])->name('index');
        Route::get('/edit', [ControllerProfile::class, 'edit'])->name('edit');
        Route::patch('/', [ControllerProfile::class, 'update'])->name('update');
    });

    Route::group([
        'prefix' => 'autentikasi',
        'as' => 'autentikasi.',
    ], function() {
        Route::get('/logout', [ControllerAutentikasi::class, 'logout'])->name('logout');
    });
});


Route::group(['middleware' => [
    'guest'
]], function() {
    Route::group([
        'prefix' => 'autentikasi',
        'as' => 'autentikasi.',
    ], function() {
        Route::get('/', [ControllerAutentikasi::class, 'index'])->name('index');
        Route::post('/register', [ControllerAutentikasi::class, 'register'])->name('register');
        Route::post('/login', [ControllerAutentikasi::class, 'login'])->name('login');
    });
});
