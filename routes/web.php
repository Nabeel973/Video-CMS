<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\TagController;
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

// Route::middleware(['auth'])->group(function () {
//     // Admin routes
//     Route::prefix('admin')->group(function () {
//         // Genres
//         Route::get('/genres', [GenreController::class, 'index'])->name('admin.genres.index');
        
//         // Categories
//         Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        
//         // Releases
//         Route::get('/releases', [ReleaseController::class, 'index'])->name('admin.releases.index');
        
//         // Tags
//         Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
//     });
// });

Route::get('/{any}', [AppController::class, 'index'])->where('any', '.*');
