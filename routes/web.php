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
   use App\Http\Controllers\Employee\EmployeeController;
   use App\Http\Controllers\Salary\SalaryController;
   use App\Http\Controllers\Import\ImportController;
use App\Http\Controllers\Reset\ResetController;
use App\Http\Controllers\Salary\AssignmentController;
use App\Http\Controllers\SalaryStructure\SStructureController;
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
Route::post('/commande-client/{name}/validate', [CommandeClientController::class, 'validerCommande'])->name('comdClient.valider');


//livraison
Route::get('/livraison', [LivraisonController::class, 'index'])->name('livraison.index');
Route::get('/livraison/{name}', [LivraisonController::class, 'show'])->name('livraison.show');

//Employee

Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/employees/{name}', [EmployeeController::class, 'show'])->name('employee.show');
Route::get('/fiche-paie/liste', [SalaryController::class, 'fichePaie'])->name('fiche-paie.liste');

Route::get('/fiche-paie', [SalaryController::class, 'index'])->name('fiche-paie.index');
Route::get('/fiche-paie/view', [SalaryController::class, 'show'])->name('fichePaie.show');
Route::get('/fiche-paie/view/exportpdf', [SalaryController::class, 'exportPdf'])->name('fichePaie.export');
Route::get('/fiche-paie/mois', [SalaryController::class, 'filtreParMois'])->name('fichePaie.filtreParMois');
Route::get('/fiche-paie/resume', [SalaryController::class, 'resumeParAnnee'])->name('fichePaie.resumeParAnnee');
Route::get('/fiche-paie/graphe', [SalaryController::class, 'grapheSalaire'])->name('fichePaie.grapheSalaire');


//import
Route::get('/import', [ImportController::class, 'index'])->name('import.index');
Route::post('/import/suppliers', [ImportController::class, 'importSuppliers'])->name('import.suppliers');
Route::post('/import/employees', [ImportController::class, 'importEmployees'])->name('import.employees');
Route::post('/import/salary_structure', [ImportController::class, 'importSalaryStructure'])->name('import.salary_structure');
Route::post('/import/salary_slip', [ImportController::class, 'importSalarySlip'])->name('import.salary_slip');
Route::post('/import/all', [ImportController::class, 'importAll'])->name('import.all');

//reset
Route::get('/reset-all-doctypes', [ResetController::class, 'resetAll'])->name('reset.all');

//salary structur

Route::get('/salary-structure',[SStructureController::class, 'index'])->name('salaraStr.index');
Route::get('/salary-structure/view',[SStructureController::class, 'show'])->name('salaraStr.show');
Route::get('/salary-structure/add', [SStructureController::class, 'addForm'])->name('salaraStr.addForm');
Route::post('/salary-structure', [SStructureController::class, 'store'])->name('salaraStr.store');


//Assignment Salary sTructure

Route::get('/assignment-salary/add', [AssignmentController::class, 'addAssignment'])->name('salaryAssg.addAssg');
Route::post('/assignent-salary', [AssignmentController::class, 'assignment'])->name('salaryAssg.posteAssg');

//
Route::post('/salary/modif', [AssignmentController::class, 'modif'])->name('salaryStr.modif');
//genere
Route::get('/genere-salaire', [AssignmentController::class, 'genereForm'])->name('genere.genForm');
Route::post('/genere-salaire', [AssignmentController::class, 'genereSSA'])->name('genere.genereSSA');
Route::get('/modif-salaire', [AssignmentController::class, 'modifForm'])->name('salary.modifForm');
Route::post('/salaire/modification-masse', [AssignmentController::class, 'modifSalaire'])->name('modif.salary.mass');

//recherche
Route::get('/recherche-salaire', [AssignmentController::class, 'rechercheForm'])->name('recherche.rechercheForm');
Route::post('/recherche-salaireResultat', [AssignmentController::class, 'recherche'])->name('recherche.moteur');


//test direct Base

use App\Http\Controllers\EmployerController;

Route::get('/employeer', [EmployerController::class, 'index'])->name('employees.index');
Route::get('/employeer/{id}', [EmployerController::class, 'show'])->name('employees.show');
