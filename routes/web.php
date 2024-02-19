<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'update', 'destroy', 'show'])
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::patch('chirps/{chirp}/like', [ChirpController::class, 'like'])->name('chirps.like');
    Route::patch('chirps/{chirp}/dislike', [ChirpController::class, 'dislike'])->name('chirps.unlike');
    Route::post('chirps/{chirp}/rechirp', [ChirpController::class, 'rechirp'])->name('chirps.rechirp');

});

Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('notifications', [NotificationsController::class, 'index'])
        ->name('notifications.index');
    Route::patch('notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-as-read');

    Route::patch('notifications/{notification}/mark-as-read', [NotificationsController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::delete('notifications/{notification}', [NotificationsController::class, 'destroy'])
        ->name('notifications.destroy');

});

Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('users/{user}/profile', [UserController::class, 'profile'])
    ->name('user.profile');

    Route::post('users/{user}/follow', [UserController::class, 'follow'])
        ->name('user.follow');

    Route::post('users/{user}/unfollow', [UserController::class, 'unfollow'])
        ->name('user.unfollow');
    Route::post('users/{user}/toggle-follow', [UserController::class, 'toggleFollow'])
        ->name('user.toggle-follow');
});
require __DIR__.'/auth.php';
