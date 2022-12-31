<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CompanyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Auth API without middleware
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::group([
    'middleware' => 'auth:sanctum'
], function () {
    // Auth API -> using middleware auth:sanctum
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('user', [UserController::class, 'fetch']);

    // Company API
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('', [CompanyController::class, 'fetch'])->name('fetch');
        Route::post('', [CompanyController::class, 'create'])->name('create');
        Route::post('update/{id}', [CompanyController::class, 'update'])->name('update');
    });

    // Team API
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('', [TeamController::class, 'fetch'])->name('fetch');
        Route::post('', [TeamController::class, 'create'])->name('create');
        Route::post('update/{id}', [TeamController::class, 'update'])->name('update');
        Route::delete('{id}', [TeamController::class, 'destroy'])->name('delete');
    });

    // Role API
    Route::prefix('role')->name('role.')->group(function () {
        Route::get('', [RoleController::class, 'fetch'])->name('fetch');
        Route::post('', [RoleController::class, 'create'])->name('create');
        Route::post('update/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('{id}', [RoleController::class, 'destroy'])->name('delete');
    });
});

