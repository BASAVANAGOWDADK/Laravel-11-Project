<?php

use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
    Route::post('/profile/avatar/ai', [AvatarController::class, 'generate'])->name('profile.avatar.ai');
});

require __DIR__ . '/auth.php';

Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');

Route::get('/auth/callback', function () {
    $githubuser = Socialite::driver('github')->user();

    $user = User::firstOrCreate([
        'email' => $githubuser->email,
    ], [
        'name' => $githubuser->name,
        'email' => $githubuser->email,
        'password' => 'password',
    ]);

    Auth::login($user);
    return redirect('/dashboard');
});
