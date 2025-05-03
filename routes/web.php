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
use App\Http\Controllers\Commande\CommandeController;
use App\Http\Controllers\Facture\FactureAchatController;
use App\Http\Controllers\Dashboard\DashboardController;

///
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::get('/get-logged-user', [AuthController::class, 'getLoggedUser'])->name('user.logged');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

//Devis Fournisseur
Route::get('/devis/filtre', [DevisController::class, 'filtre'])->name('devis.filtre');

Route::get('/devis', [DevisController::class, 'index'])->name('devis.index');

Route::prefix('devis')->group(function () {
    Route::get('/{name}', [DevisController::class, 'show'])->name('devis.show');
});
Route::put('/devis/{name}/update-item', [DevisController::class, 'updateItem'])->name('devis.update-item');

Route::put('/devis/{name}/update-rate', [DevisController::class, 'updateItemRate'])
    ->name('devis.update-rate');

//Commande Achat
Route::get('/commandes/filtre', [CommandeController::class, 'filtre'])->name('commandes.filtre');
Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
Route::get('/commandes/{name}', [CommandeController::class, 'show'])->name('commandes.show');


//facture achat
Route::get('/factures/achat', [\App\Http\Controllers\Facture\FactureAchatController::class, 'index'])->name('factures.achat.index');
Route::get('/factures/achat/{name}', [\App\Http\Controllers\Facture\FactureAchatController::class, 'show'])->name('factures.achat.show');

//dashboard
Route::get('/dashboard/achats', [DashboardController::class, 'achatsGlobal'])->name('dashboard.achats');
