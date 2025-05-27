<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\QuotationClient\QuotationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Devise\DevisController;
use App\Http\Controllers\Commande\CommandeController;
use App\Http\Controllers\CommandeClient\CommandeClientController;
use App\Http\Controllers\Facture\FactureAchatController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Livraison\LivraisonController;
use PHPUnit\Util\Exporter;

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


    Route::get('devis/{name}', [DevisController::class, 'show'])->name('devis.show');

// web.php
Route::get('/createFormulaire', [DevisController::class, 'createFormulaire'])
     ->name('devis.createForm');
Route::post('/devis', [DevisController::class, 'store'])->name('devis.store');
Route::post('/devis/{name}/update-and-submit', [DevisController::class, 'updateAndSubmit'])
->name('devis.update-and-submit');



//Commande Achat
Route::get('/commandes/filtre', [CommandeController::class, 'filtre'])->name('commandes.filtre');
Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
Route::get('/commandes/{name}', [CommandeController::class, 'show'])->name('commandes.show');


//facture achat
Route::get('/factures/achat', [\App\Http\Controllers\Facture\FactureAchatController::class, 'index'])->name('factures.achat.index');
Route::get('/factures/achat/{name}', [\App\Http\Controllers\Facture\FactureAchatController::class, 'show'])->name('factures.achat.show');
Route::post('/factures-achat/{name}/validate', [FactureAchatController::class, 'validateFacture'])->name('factures.achat.validate');
Route::post('/factures-achat/{name}/pay', [FactureAchatController::class, 'payFacture'])->name('factures.achat.pay');

//dashboard
Route::get('/dashboard/achats', [DashboardController::class, 'achatsGlobal'])->name('dashboard.achats');


//export
Route::get('/factures-achat/{name}/export/pdf', [ExportController::class, 'exportSinglePdf'])->name('factures.achat.export.single.pdf');
Route::get('/factures-achat/{name}/export/csv', [ExportController::class, 'exportSingleCsv'])->name('factures.achat.export.single.csv');


//CLient
Route::get('/clients', [ClientController::class, 'index'])->name('client.index');
Route::get('/clients/{name}', [ClientController::class, 'show'])->name('client.show');
Route::post('/clients/{name}/activer', [ClientController::class, 'activerClient'])->name('client.activer');
Route::post('/clients/{name}/desactiver', [ClientController::class, 'desactiverClient'])->name('client.desactiver');
Route::get('/addclient', [ClientController::class, 'createForm'])->name('client.createForm');
Route::post('/clients', [ClientController::class, 'addClient'])->name('client.store');

//DevisClient(QuotationClient)
Route::get('/devis-client', [QuotationController::class, 'index'])->name('devisClient.index');
Route::get('/devis-client/{name}', [QuotationController::class, 'show'])->name('devisClient.show');
Route::get('/adddevis', [QuotationController::class, 'create'])->name('devisClient.create');
Route::post('/devis-client', [QuotationController::class, 'store'])->name('devisClient.store');
Route::post('/devis-client/{name}/validate', [QuotationController::class, 'validerQuotation'])->name('devisClient.validate');
Route::post('/devis-client/{name}/convert-to-order', [QuotationController::class, 'commander'])->name('devisClient.commander');

//CommandeClient

Route::get('/commande-client',[CommandeClientController::class,'index'])->name('comdClient.index');
Route::get('/commande-client/{name}', [CommandeClientController::class, 'show'])->name('comdClient.show');


//livraison

Route::get('/livraison', [LivraisonController::class, 'index'])->name('livraison.index');
Route::get('/livraison/{name}', [LivraisonController::class, 'show'])->name('livraison.show');