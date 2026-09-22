<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accountant\SearchController;
use App\Http\Controllers\Accountant\RecetteController;
use App\Http\Controllers\Accountant\AssuranceController;

Route::middleware(['auth'])->group(function () {

    Route::middleware('isAccountant')->group(function () {
        Route::prefix('accountant')->name('accountant.')->namespace('\App\Http\Controllers\Accountant')->group(function () {

            Route::get('profile', 'AccountantController@profile')->name('profile');
            Route::put('update', 'AccountantController@update')->name('update');

            Route::prefix('recette')->name('recette.')->group(function () {
                Route::get('list', 'RecetteController@list')->name('list');
                Route::get('day/{day}', 'RecetteController@day')->name('day');
                Route::get('du_jour', [RecetteController::class, 'aujourdHui'])->name('du_jour');
                Route::get('historique', [RecetteController::class, 'historique'])->name('historique');
            });
            Route::get('recette/{day}', 'RecetteController@list')->name('recette');


            Route::prefix('search')->name('search.')->group(function () {
                Route::get('index', 'SearchController@index')->name('index');
                Route::get('treatment', [SearchController::class, 'treatment'])->name('treatment');
            });

            Route::prefix('assurance')->name('assurance.')->group(function () {
                Route::get('index', 'AssuranceController@index')->name('index');
                Route::get('search', 'AssuranceController@search')->name('search');
                Route::get('pdf/{date_beging}/{date_end}/{assurance_id}/{type}', 'AssuranceController@pdf')->name('pdf');
                Route::get('today', [AssuranceController::class, 'today'])->name('today');
                Route::get('historique', [AssuranceController::class, 'historique'])->name('historique');
            });

            // Comptabilité Générale & Passerelle Sage SAARI
            Route::prefix('accounting')->name('accounting.')->group(function () {
                Route::get('dashboard', 'AccountingController@dashboard')->name('dashboard');
                Route::get('plan-comptable', 'AccountingController@planComptable')->name('plan_comptable');
                Route::post('store-compte', 'AccountingController@storeCompte')->name('store_compte');
                Route::put('update-compte/{id}', 'AccountingController@updateCompte')->name('update_compte');
                Route::get('delete-compte/{id}', 'AccountingController@deleteCompte')->name('delete_compte');
                Route::get('journaux', 'AccountingController@journaux')->name('journaux');
                Route::get('journaux/pdf', 'AccountingController@journauxPdf')->name('journaux_pdf');
                Route::get('grand-livre', 'AccountingController@grandLivre')->name('grand_livre');
                Route::get('grand-livre/pdf', 'AccountingController@grandLivrePdf')->name('grand_livre_pdf');
                Route::get('balance', 'AccountingController@balance')->name('balance');
                Route::get('balance/pdf', 'AccountingController@balancePdf')->name('balance_pdf');
                Route::get('expenses', 'AccountingController@expenses')->name('expenses');
                Route::get('expenses/pdf', 'AccountingController@expensesPdf')->name('expenses_pdf');
                Route::post('store-expense', 'AccountingController@storeExpense')->name('store_expense');
                Route::get('delete-expense/{id}', 'AccountingController@deleteExpense')->name('delete_expense');
                Route::get('assurances-suivi', 'AccountingController@assurances')->name('assurances_suivi');
                Route::get('assurances-suivi/pdf', 'AccountingController@assurancesPdf')->name('assurances_pdf');
                Route::post('store-settlement', 'AccountingController@storeSettlement')->name('store_settlement');
                Route::get('delete-settlement/{id}', 'AccountingController@deleteSettlement')->name('delete_settlement');
                Route::get('deposits', 'AccountingController@deposits')->name('deposits');
                Route::get('deposits/pdf', 'AccountingController@depositsPdf')->name('deposits_pdf');
                Route::get('deposits/pdf/{id}', 'AccountingController@depositPdf')->name('deposit_pdf');
                Route::post('store-deposit', 'AccountingController@storeDeposit')->name('store_deposit');
                Route::get('delete-deposit/{id}', 'AccountingController@deleteDeposit')->name('delete_deposit');
                Route::get('export-sage', 'AccountingController@exportSageView')->name('export_sage');
                Route::get('export-sage/download', 'AccountingController@exportSageDownload')->name('export_sage_download');
                Route::get('export-excel/download', 'AccountingController@exportExcelDownload')->name('export_excel_download');
                Route::get('sync', 'AccountingController@sync')->name('sync');
            });
        });


    });
});
