<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
Route::post('/auth/register', [App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
Route::post('/auth/logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/user', [App\Http\Controllers\Api\Auth\AuthController::class, 'user'])->middleware('auth:sanctum');

// Protected API routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Genres
    Route::get('/genres', [GenreController::class, 'list']);
    Route::post('/genres', [GenreController::class, 'store']);
    Route::get('/genres/{genre}', [GenreController::class, 'show']);
    Route::put('/genres/{genre}', [GenreController::class, 'update']);
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'list']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Releases
    Route::get('/releases', [ReleaseController::class, 'list']);
    Route::post('/releases', [ReleaseController::class, 'store']);
    Route::get('/releases/{release}', [ReleaseController::class, 'show']);
    Route::put('/releases/{release}', [ReleaseController::class, 'update']);
    Route::delete('/releases/{release}', [ReleaseController::class, 'destroy']);

    // Tags
    Route::get('/tags', [TagController::class, 'list']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);


    // Users
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
    Route::get('/users/{user}', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);
    Route::get('/users/roles/available', [App\Http\Controllers\UserController::class, 'getRoles']);
    Route::get('/users/stats/dashboard', [App\Http\Controllers\UserController::class, 'getStats']);

    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index']);
    Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store']);
    Route::get('/roles/{role}', [App\Http\Controllers\RoleController::class, 'show']);
    Route::put('/roles/{role}', [App\Http\Controllers\RoleController::class, 'update']);
    Route::delete('/roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy']);
    
    Route::get('/permissions', [App\Http\Controllers\RoleController::class, 'getAllPermissions']);
    Route::post('/roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'assignPermissions']);
    Route::get('/roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'getRolePermissions']);
});
