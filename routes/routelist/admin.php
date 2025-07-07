<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\HomeController;


Route::prefix(env('ADMIN_DIR'))->group(function () {
    if (env('ADMIN_CMS', false) == true) {
        // NEED AUTH
        Route::middleware(['auth.admin'])->group(function () {
            // HOME
            Route::get('/', [HomeController::class, 'index'])->name('admin.home');
            Route::get('/get-dummy-data', [HomeController::class, 'dashboard_dummy_data'])->name('admin.dashboard.dummy_data');
        });
    }
});
