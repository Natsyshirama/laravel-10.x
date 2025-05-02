<?php

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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Devise\DevisController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::get('/get-logged-user', [AuthController::class, 'getLoggedUser'])->name('user.logged');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

//Devis Fournisseur
Route::get('/devis', [DevisController::class, 'index'])->name('devis.index');

Route::prefix('devis')->group(function () {
    Route::get('/{name}', [DevisController::class, 'show'])->name('devis.show');
    Route::put('/{name}', [DevisController::class, 'update'])->name('devis.update');
});

