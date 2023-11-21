<?php

use App\Http\Controllers\Autentikasi\ControllerAutentikasi;
use App\Http\Controllers\ControllerFile;
use App\Http\Controllers\ControllerInformasiUser;
use App\Http\Controllers\ControllerKey;
use App\Http\Controllers\ControllerProfile;
use App\Http\Controllers\ControllerShareFileUser;
use App\Http\Controllers\ControllerShareInformasiUser;
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
        'prefix' => 'informasi',
        'as' => 'informasi.',
    ], function() {
        Route::get('/', [ControllerInformasiUser::class, 'index'])->name('index');
        Route::get('/create', [ControllerInformasiUser::class, 'create'])->name('create');
        Route::post('/', [ControllerInformasiUser::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ControllerInformasiUser::class, 'edit'])->name('edit');
        Route::patch('/', [ControllerInformasiUser::class, 'update'])->name('update');
        Route::delete('/{id}', [ControllerInformasiUser::class, 'delete'])->name('delete');
    });

    Route::group([
        'prefix' => 'file',
        'as' => 'file.',
    ], function() {
        Route::get('/', [ControllerFile::class, 'index'])->name('index');
        Route::get('/create', [ControllerFile::class, 'create'])->name('create');
        Route::get('/{id}', [ControllerFile::class, 'show'])->name('show');
        Route::post('/store', [ControllerFile::class, 'store'])->name('store');
        Route::delete('/{id}', [ControllerFile::class, 'delete'])->name('delete');
    });

    Route::group([
        'prefix' => 'share',
        'as' => 'share.',
    ], function() {
        Route::get('/', [ControllerKey::class, 'show'])->name('show');

        Route::group([
            'prefix' => 'informasi',
            'as' => 'informasi.'
        ], function() {
            Route::get('/', [ControllerShareInformasiUser::class, 'index'])->name('index');
            Route::post('/', [ControllerShareInformasiUser::class, 'show'])->name('show');
        });

        Route::group([
            'prefix' => 'file',
            'as' => 'file.'
        ], function() {
            Route::get('/', [ControllerShareFileUser::class, 'index'])->name('index');
            Route::post('/', [ControllerShareFileUser::class, 'show'])->name('show');
            Route::post('/download', [ControllerShareFileUser::class, 'download'])->name('download');
        });
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
