<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

# DEVELOPMENT TESTER
use App\Http\Controllers\DevController;

Route::group(['prefix' => 'dev'], function () {
    // SANDBOX
    Route::get('/', [DevController::class, 'sandbox'])->name('dev.sandbox');

    // PHPINFO
    Route::get('/phpinfo', fn() => phpinfo())->name('dev.phpinfo');

    // SAMPLE STRUCTURE OF NAV MENU
    Route::get('/nav-menu-structure', [DevController::class, 'nav_menu_structure'])->name('dev.nav_menu');

    // CUSTOM PAGES
    Route::get('/custom-pages/{name}', [DevController::class, 'custom_pages'])->name('dev.custom_pages');

    // NEED AUTH
    Route::middleware(['auth.admin'])->group(function () {
        // CHEATSHEET FORM
        Route::get('/cheatsheet-form', [DevController::class, 'cheatsheet_form'])->name('dev.cheatsheet_form');

        // CRYPT TOOLS
        Route::match(['get', 'post'], '/encrypt', [DevController::class, 'encrypt'])->name('dev.encrypt');
        Route::match(['get', 'post'], '/decrypt', [DevController::class, 'decrypt'])->name('dev.decrypt');

        // EMAIL
        Route::prefix('email')->group(function () {
            // Send Email using SMTP - sample: "{URL}/dev/email?send=true&email=username@domain.com"
            // Preview Email - sample: "{URL}/dev/email"
            Route::get('/', [DevController::class, 'email_send']);

            Route::match(['get', 'post'], '/template', [DevController::class, 'email_template'])->name('dev.email_template');
        });

        // TESTER FORM
        Route::match(['get', 'post'], '/tester-form', [DevController::class, 'tester_form'])->name('dev.tester_form');
    });
});

# CRON
use App\Http\Controllers\CronController;

Route::prefix('cron')->group(function () {
    Route::get('/reminder', [CronController::class, 'reminder']);
});

# HELPER
use App\Http\Controllers\HelperController;

Route::get('/files/{file}', [HelperController::class, 'access_file'])->middleware('file.permission')->name('access_file');

// Default redirect from "/" to "/en"
Route::get('/', function () {
    return redirect('/en/en');
});

foreach (File::allFiles(__DIR__ . '/routelist') as $route_file) {
    require $route_file->getPathname();
}

