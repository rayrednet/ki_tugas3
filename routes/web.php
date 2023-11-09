<?php

use App\Http\Controllers\Autentikasi\ControllerAutentikasi;
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

Route::group([
    'prefix' => 'autentikasi',
    'as' => 'autentikasi.',
], function() {
    Route::get('/', [ControllerAutentikasi::class, 'index'])->name('index');
    Route::post('/login', [ControllerAutentikasi::class, 'login'])->name('login');
    Route::post('/register', [ControllerAutentikasi::class, 'register'])->name('register');
});

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
