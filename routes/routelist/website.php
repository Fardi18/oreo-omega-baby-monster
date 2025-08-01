<?php

use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\PostLaunchController;
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
    'middleware' => ['web', 'mobile.only', \App\Http\Middleware\SetLocale::class]
], function () {
    // Landing Page
    Route::get('/', [SiteController::class, 'landing_page'])->name('web.landing_page');

    // Pre-Launch
    Route::get('/pre-launch', [PreLaunchController::class, 'register_pre_launch_page'])->name('web.register_pre_launch_page');
    Route::post('/pre-launch', [PreLaunchController::class, 'register_pre_launch_process'])->name('web.register_pre_launch_process');

    // Post-Launch
    Route::get('/post-launch', [PostLaunchController::class, 'register_post_launch_page'])->name('web.register_post_launch_page');
    Route::post('/post-launch', [PostLaunchController::class, 'register_post_launch_process'])->name('web.register_post_launch_process');

    // Login
    Route::get('/login', [PostLaunchController::class, 'login_page'])->name('web.login_page');
    Route::post('/login', [PostLaunchController::class, 'login_process'])->name('web.login_process');

    // PIN
    Route::get('/create-pin/{id}', [PostLaunchController::class, 'create_pin_page'])->name('web.create_pin_page');
    Route::post('/create-pin/{id}', [PostLaunchController::class, 'create_pin_process'])->name('web.create_pin_process');
    Route::get('/forgot-pin', [PostLaunchController::class, 'forgot_pin_page'])->name('web.forgot_pin_page');
    Route::post('/forgot-pin', [PostLaunchController::class, 'forgot_pin_process'])->name('web.forgot_pin_process');

    // OTP
    Route::get('/verify-otp/{id}', [PreLaunchController::class, 'verify_otp_page'])->name('web.verify_otp_page');
    Route::post('/verify-otp/{id}', [PreLaunchController::class, 'verify_otp_process'])->name('web.verify_otp_process');
    Route::post('/resend-otp/{id}', [PreLaunchController::class, 'resend_otp'])->name('web.resend_otp');

    // PIN AND OTP
    Route::get('/forgot-pin-otp/{id}', [PostLaunchController::class, 'forgot_pin_otp_page'])->name('web.forgot_pin_otp_page');
    Route::post('/forgot-pin-otp/{id}', [PostLaunchController::class, 'forgot_pin_otp_process'])->name('web.forgot_pin_otp_process');

    // Success
    Route::get('/success/{id}', [PreLaunchController::class, 'success_page'])->name('web.success_page');

    // Only for authenticated users
    Route::group(['middleware' => ['auth.session']], function () {
        // Welcome
        Route::get('/welcome', [SiteController::class, 'welcome'])->name('web.welcome');

        // Logout
        Route::get('/logout', [PostLaunchController::class, 'logout'])->name('web.logout');
    });
});
