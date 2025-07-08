<?php

use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\PreLaunchController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\SiteController;


// if (env('ADMIN_DIR', '') != '') {
//     // HOME
//     Route::get('/', [SiteController::class, 'index'])->name('web.home');

//     // FAQ
//     Route::get('/faq', [SiteController::class, 'faq'])->name('web.faq');

//     // PAGE
//     Route::get('/{slug}', [SiteController::class, 'page'])->name('web.page');
// }

// Switch market
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Route::group([
    'prefix' => '{market}/{lang}',
    'middleware' => ['web', \App\Http\Middleware\SetLocale::class]
], function () {
    Route::get('/homie', [SiteController::class, 'index'])->name('web.home');
    Route::get('/faq', [SiteController::class, 'faq'])->name('web.faq');
    Route::get('/{slug}', [SiteController::class, 'page'])->name('web.page');

    // Landing Page
    Route::get('/', [SiteController::class, 'landing_page'])->name('web.landing_page');
    
    // Pre-Launch
    Route::post('/pre-launch', [PreLaunchController::class, 'register_pre_launch'])->name('web.register_pre_launch');
});
