<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookRequestController as AdminBookRequestController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LibraryController::class, 'home'])->name('home');
Route::get('/search', [LibraryController::class, 'search'])->name('search');
Route::get('/suggest', [LibraryController::class, 'suggest'])->name('suggest');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::middleware('auth')->group(function () {
    Route::post('/activity/time', [UserActivityController::class, 'store'])->name('activity.time');
    Route::post('/request-book', [BookRequestController::class, 'store'])->name('book-requests.store');
    Route::post('/book-requests/{bookRequest}/return', [BookRequestController::class, 'return'])->name('book-requests.return');
});

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('books', AdminBookController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('/book-requests', [AdminBookRequestController::class, 'index'])->name('book-requests.index');
    Route::post('/book-requests/{bookRequest}/approve', [AdminBookRequestController::class, 'approve'])->name('book-requests.approve');
    Route::post('/book-requests/{bookRequest}/reject', [AdminBookRequestController::class, 'reject'])->name('book-requests.reject');
});

require __DIR__.'/auth.php';
