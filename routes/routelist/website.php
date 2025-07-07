<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\SiteController;


if (env('ADMIN_DIR', '') != '') {
    // HOME
    Route::get('/', [SiteController::class, 'index'])->name('web.home');

    // FAQ
    Route::get('/faq', [SiteController::class, 'faq'])->name('web.faq');

    // PAGE
    Route::get('/{slug}', [SiteController::class, 'page'])->name('web.page');
}
