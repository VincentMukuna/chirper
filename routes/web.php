<?php

use App\Http\Controllers\Chirp\ChirpController;
use App\Http\Controllers\Chirp\LikeController;
use App\Http\Controllers\Chirp\RechirpController;
use App\Http\Controllers\Chirp\ReplyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\User\UserController;
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

//Chirping
Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'update', 'destroy', 'show'])
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function (){
    Route::post('chirps/{chirp}/reply', [ReplyController::class, 'store'])->name('chirps.reply');
});

//Chirp actions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::patch('chirps/{chirp}/like', [LikeController::class, 'like'])->name('chirps.like');
    Route::patch('chirps/{chirp}/unlike', [LikeController::class, 'dislike'])->name('chirps.unlike');
    Route::patch('chirps/{chirp}/toggle-like', [LikeController::class, 'toggle'])->name('chirps.toggle-like');
    Route::post('chirps/{chirp}/rechirp', [RechirpController::class, 'rechirp'])->name('chirps.rechirp');
    Route::post('chirps/{chirp}/undo-rechirp', [RechirpController::class, 'undo_rechirp'])->name('chirps.undo_rechirp');

});

//notifications
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

//Users
Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::get('users/{user}/following', [FollowController::class, 'following'])
        ->name('users.following');
     Route::get('users/{user}/followers', [FollowController::class, 'followers'])
        ->name('users.followers');


    Route::post('users/{user}/follow', [FollowController::class, 'create'])
        ->name('users.follow');

    Route::post('users/{user}/unfollow', [FollowController::class, 'destroy'])
        ->name('users.unfollow');

    Route::post('users/{user}/toggle-follow', [FollowController::class, 'toggleFollow'])
        ->name('users.toggle-follow');
});
require __DIR__.'/auth.php';
