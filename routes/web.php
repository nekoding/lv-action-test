<?php

use App\Actions\User\DeleteAccount;
use App\Actions\User\GetProfile;
use App\Actions\User\UpdateProfile;
use App\Http\Controllers\ProfileController;
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

Route::view('/', 'welcome');

Route::view('/dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', GetProfile::class)->name('profile.edit');
    Route::patch('/profile', UpdateProfile::class)->name('profile.update');
    Route::delete('/profile', DeleteAccount::class)->name('profile.destroy');
});

require __DIR__ . '/auth.php';
