<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdvertisementController;
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

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Protected API routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Genres - using apiResource with list method override
    Route::get('/genres', [GenreController::class, 'list']);
    Route::apiResource('genres', GenreController::class)->except(['index']);

    // Categories - using apiResource with list method override
    Route::get('/categories', [CategoryController::class, 'list']);
    Route::apiResource('categories', CategoryController::class)->except(['index']);

    // Tags - using apiResource with list method override
    Route::get('/tags', [TagController::class, 'list']);
    Route::apiResource('tags', TagController::class)->except(['index']);

    // Users - standard apiResource
    Route::apiResource('users', UserController::class);
    Route::get('/users/roles/available', [UserController::class, 'getRoles']);
    Route::get('/users/stats/dashboard', [UserController::class, 'getStats']);

    // Roles - standard apiResource
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [RoleController::class, 'getAllPermissions']);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'getRolePermissions']);

    // Advertisements - using apiResource with list method override and custom parameter
    Route::get('/advertisements', [AdvertisementController::class, 'list']);
    Route::apiResource('advertisements', AdvertisementController::class)->except(['index'])->parameters(['advertisements' => 'id']);

    // Movies - using apiResource with list method override and custom parameter
    Route::get('/movies', [MovieController::class, 'list']);
    Route::apiResource('movies', MovieController::class)->except(['index'])->parameters(['movies' => 'id']);
});


