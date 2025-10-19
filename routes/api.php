<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReleaseController;
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
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/users/roles/available', [UserController::class, 'getRoles']);
    Route::get('/users/stats/dashboard', [UserController::class, 'getStats']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
    
    Route::get('/permissions', [RoleController::class, 'getAllPermissions']);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'getRolePermissions']);


    // Advertisements
    Route::get('/advertisements', [AdvertisementController::class, 'list']);
    Route::post('/advertisements', [AdvertisementController::class, 'store']);
    Route::get('/advertisements/{advertisement}', [AdvertisementController::class, 'show']);
    Route::put('/advertisements/{advertisement}', [AdvertisementController::class, 'update']);
    Route::delete('/advertisements/{advertisement}', [AdvertisementController::class, 'destroy']);

    // Movies
    Route::get('/movies', [MovieController::class, 'list']);
    Route::post('/movies', [MovieController::class, 'store']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);


});


