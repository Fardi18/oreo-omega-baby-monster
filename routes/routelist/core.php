<?php

use Illuminate\Support\Facades\Route;

### SIORENSYS CORE (PLEASE DO NOT MODIFY THE CODE BELOW, UNLESS YOU UNDERSTAND WHAT YOU ARE DOING) ###

use App\Http\Controllers\Admin\Core\AdminController;
use App\Http\Controllers\Admin\Core\AdminGroupController;
use App\Http\Controllers\Admin\Core\AuthController;
use App\Http\Controllers\Admin\Core\BannerController;
use App\Http\Controllers\Admin\Core\BlockedIpController;
use App\Http\Controllers\Admin\Core\ConfigController;
use App\Http\Controllers\Admin\Core\CountryController;
use App\Http\Controllers\Admin\Core\ErrorLogController;
use App\Http\Controllers\Admin\Core\FaqController;
use App\Http\Controllers\Admin\Core\FaqItemController;
use App\Http\Controllers\Admin\Core\FormController;
use App\Http\Controllers\Admin\Core\LanguageController;
use App\Http\Controllers\Admin\Core\ModuleController;
use App\Http\Controllers\Admin\Core\ModuleRuleController;
use App\Http\Controllers\Admin\Core\NavMenuChildController;
use App\Http\Controllers\Admin\Core\NavMenuController;
use App\Http\Controllers\Admin\Core\NoteController;
use App\Http\Controllers\Admin\Core\NotificationController;
use App\Http\Controllers\Admin\Core\OfficeBranchController;
use App\Http\Controllers\Admin\Core\OfficeController;
use App\Http\Controllers\Admin\Core\PageController;
use App\Http\Controllers\Admin\Core\PhraseController;
use App\Http\Controllers\Admin\Core\SocialMediaController;
use App\Http\Controllers\Admin\Core\SystemLogController;
use App\Http\Controllers\Admin\Core\EmailTemplateController;


Route::prefix(env('ADMIN_DIR'))->group(function () {
    if (env('ADMIN_CMS', false) == true) {
        // AUTH
        Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
        Route::post('/login-auth', [AuthController::class, 'login_auth'])->name('admin.login.auth');
        Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/logout-all', [AuthController::class, 'logout_all'])->name('admin.logout.all');
        Route::match(['get', 'post'], '/verify-2fa', [AuthController::class, 'verify_2fa'])->name('admin.login.verify_2fa');

        // NEED AUTH
        Route::middleware(['auth.admin'])->group(function () {

            // NOTIFICATION
            Route::prefix('notification')->group(function () {
                Route::get('/', [NotificationController::class, 'index'])->name('admin.notif.list');
                Route::get('/get-data', [NotificationController::class, 'get_data'])->name('admin.notif.get_data');
                Route::get('/open/{id}', [NotificationController::class, 'open'])->name('admin.notif.open');
                Route::get('/get-data/bar', [NotificationController::class, 'get_notif'])->name('admin.notif');
            });

            // CONFIG APP
            Route::prefix('config')->group(function () {
                Route::match(['get', 'post'], '/', [ConfigController::class, 'index'])->name('admin.config');
                Route::get('/export', [ConfigController::class, 'export'])->name('admin.config.export');
                Route::post('/import', [ConfigController::class, 'import'])->name('admin.config.import');
            });

            // CHANGE COUNTRY
            Route::get('change-country/{alias}', [AdminController::class, 'change_country'])->name('admin.change_country');

            // CHANGE LANGUAGE
            Route::get('change-language/{alias}', [AdminController::class, 'change_language'])->name('admin.change_language');

            // PROFILE
            Route::match(['get', 'post'], '/profile', [AdminController::class, 'profile'])->name('admin.profile');

            // CHANGE PASSWORD
            Route::post('/change-password', [AdminController::class, 'change_password'])->name('admin.change_password');

            // 2FA SETUP
            Route::post('/set-2fa', [AdminController::class, 'setup_2fa'])->name('admin.setup_2fa');

            // SYSTEM LOGS
            Route::prefix('system-logs')->group(function () {
                Route::get('/', [SystemLogController::class, 'index'])->name('admin.system_logs');
                Route::get('/get-data', [SystemLogController::class, 'get_data'])->name('admin.system_logs.get_data');
                Route::get('/export', [SystemLogController::class, 'export'])->name('admin.system_logs.export');
                Route::get('/{id}', [SystemLogController::class, 'view'])->name('admin.system_logs.view');
            });

            // MODULE
            Route::prefix('module')->group(function () {
                Route::get('/', [ModuleController::class, 'index'])->name('admin.module');
                Route::get('/get-data', [ModuleController::class, 'get_data'])->name('admin.module.get_data');
                Route::get('/create', [ModuleController::class, 'create'])->name('admin.module.create');
                Route::post('/store', [ModuleController::class, 'store'])->name('admin.module.store');
                Route::get('/edit/{id}', [ModuleController::class, 'edit'])->name('admin.module.edit');
                Route::post('/update/{id}', [ModuleController::class, 'update'])->name('admin.module.update');
                Route::post('/delete', [ModuleController::class, 'delete'])->name('admin.module.delete');
                Route::get('/deleted-data', [ModuleController::class, 'deleted_data'])->name('admin.module.deleted_data');
                Route::get('/get-deleted-data', [ModuleController::class, 'get_deleted_data'])->name('admin.module.get_deleted_data');
                Route::post('/restore', [ModuleController::class, 'restore'])->name('admin.module.restore');
            });

            // MODULE RULES
            Route::prefix('rules')->group(function () {
                Route::get('/', [ModuleRuleController::class, 'index'])->name('admin.module_rule');
                Route::get('/get-data', [ModuleRuleController::class, 'get_data'])->name('admin.module_rule.get_data');
                Route::get('/create', [ModuleRuleController::class, 'create'])->name('admin.module_rule.create');
                Route::post('/store', [ModuleRuleController::class, 'store'])->name('admin.module_rule.store');
                Route::get('/edit/{id}', [ModuleRuleController::class, 'edit'])->name('admin.module_rule.edit');
                Route::post('/update/{id}', [ModuleRuleController::class, 'update'])->name('admin.module_rule.update');
                Route::post('/delete', [ModuleRuleController::class, 'delete'])->name('admin.module_rule.delete');
                Route::get('/deleted-data', [ModuleRuleController::class, 'deleted_data'])->name('admin.module_rule.deleted_data');
                Route::get('/get-deleted-data', [ModuleRuleController::class, 'get_deleted_data'])->name('admin.module_rule.get_deleted_data');
                Route::post('/restore', [ModuleRuleController::class, 'restore'])->name('admin.module_rule.restore');
            });

            // PHRASE
            Route::prefix('phrase')->group(function () {
                Route::get('/', [PhraseController::class, 'index'])->name('admin.phrase');
                Route::get('/get-data', [PhraseController::class, 'get_data'])->name('admin.phrase.get_data');
                Route::get('/create', [PhraseController::class, 'create'])->name('admin.phrase.create');
                Route::post('/store', [PhraseController::class, 'store'])->name('admin.phrase.store');
                Route::get('/edit/{id}', [PhraseController::class, 'edit'])->name('admin.phrase.edit');
                Route::post('/update/{id}', [PhraseController::class, 'update'])->name('admin.phrase.update');
                Route::post('/delete', [PhraseController::class, 'delete'])->name('admin.phrase.delete');
                Route::get('/deleted-data', [PhraseController::class, 'deleted_data'])->name('admin.phrase.deleted_data');
                Route::get('/get-deleted-data', [PhraseController::class, 'get_deleted_data'])->name('admin.phrase.get_deleted_data');
                Route::post('/restore', [PhraseController::class, 'restore'])->name('admin.phrase.restore');
                Route::get('/export', [PhraseController::class, 'export'])->name('admin.phrase.export');
                Route::post('/import', [PhraseController::class, 'import'])->name('admin.phrase.import');
                Route::post('/truncate', [PhraseController::class, 'truncate'])->name('admin.phrase.truncate');
            });

            // OFFICE
            Route::prefix('office')->group(function () {
                Route::get('/', [OfficeController::class, 'index'])->name('admin.office');
                Route::get('/get-data', [OfficeController::class, 'get_data'])->name('admin.office.get_data');
                Route::get('/create', [OfficeController::class, 'create'])->name('admin.office.create');
                Route::post('/store', [OfficeController::class, 'store'])->name('admin.office.store');
                Route::get('/edit/{id}', [OfficeController::class, 'edit'])->name('admin.office.edit');
                Route::post('/update/{id}', [OfficeController::class, 'update'])->name('admin.office.update');
                Route::post('/delete', [OfficeController::class, 'delete'])->name('admin.office.delete');
                Route::get('/deleted-data', [OfficeController::class, 'deleted_data'])->name('admin.office.deleted_data');
                Route::get('/get-deleted-data', [OfficeController::class, 'get_deleted_data'])->name('admin.office.get_deleted_data');
                Route::post('/restore', [OfficeController::class, 'restore'])->name('admin.office.restore');
                Route::post('/sorting', [OfficeController::class, 'sorting'])->name('admin.office.sorting');

                // BRANCH
                Route::prefix('branch/{office_id}')->group(function () {
                    Route::get('/', [OfficeBranchController::class, 'index'])->name('admin.office_branch');
                    Route::get('/get-data', [OfficeBranchController::class, 'get_data'])->name('admin.office_branch.get_data');
                    Route::get('/create', [OfficeBranchController::class, 'create'])->name('admin.office_branch.create');
                    Route::post('/store', [OfficeBranchController::class, 'store'])->name('admin.office_branch.store');
                    Route::get('/edit/{id}', [OfficeBranchController::class, 'edit'])->name('admin.office_branch.edit');
                    Route::post('/update/{id}', [OfficeBranchController::class, 'update'])->name('admin.office_branch.update');
                    Route::post('/delete', [OfficeBranchController::class, 'delete'])->name('admin.office_branch.delete');
                    Route::get('/deleted-data', [OfficeBranchController::class, 'deleted_data'])->name('admin.office_branch.deleted_data');
                    Route::get('/get-deleted-data', [OfficeBranchController::class, 'get_deleted_data'])->name('admin.office_branch.get_deleted_data');
                    Route::post('/restore', [OfficeBranchController::class, 'restore'])->name('admin.office_branch.restore');
                    Route::post('/sorting', [OfficeBranchController::class, 'sorting'])->name('admin.office_branch.sorting');
                });
            });

            // ADMIN GROUP
            Route::prefix('group')->group(function () {
                Route::get('/', [AdminGroupController::class, 'index'])->name('admin.group');
                Route::get('/get-data', [AdminGroupController::class, 'get_data'])->name('admin.group.get_data');
                Route::get('/create', [AdminGroupController::class, 'create'])->name('admin.group.create');
                Route::post('/store', [AdminGroupController::class, 'store'])->name('admin.group.store');
                Route::get('/edit/{id}', [AdminGroupController::class, 'edit'])->name('admin.group.edit');
                Route::post('/update/{id}', [AdminGroupController::class, 'update'])->name('admin.group.update');
                Route::post('/delete', [AdminGroupController::class, 'delete'])->name('admin.group.delete');
                Route::get('/deleted-data', [AdminGroupController::class, 'deleted_data'])->name('admin.group.deleted_data');
                Route::get('/get-deleted-data', [AdminGroupController::class, 'get_deleted_data'])->name('admin.group.get_deleted_data');
                Route::post('/restore', [AdminGroupController::class, 'restore'])->name('admin.group.restore');
            });

            // ADMIN
            Route::prefix('administrator')->group(function () {
                Route::get('/', [AdminController::class, 'index'])->name('admin.user_admin');
                Route::get('/get-data', [AdminController::class, 'get_data'])->name('admin.user_admin.get_data');
                Route::get('/create', [AdminController::class, 'create'])->name('admin.user_admin.create');
                Route::post('/store', [AdminController::class, 'store'])->name('admin.user_admin.store');
                Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.user_admin.edit');
                Route::post('/update/{id}', [AdminController::class, 'update'])->name('admin.user_admin.update');
                Route::post('/reset-password/{id}', [AdminController::class, 'reset_password'])->name('admin.user_admin.reset_password');
                Route::post('/delete', [AdminController::class, 'delete'])->name('admin.user_admin.delete');
                Route::get('/deleted-data', [AdminController::class, 'deleted_data'])->name('admin.user_admin.deleted_data');
                Route::get('/get-deleted-data', [AdminController::class, 'get_deleted_data'])->name('admin.user_admin.get_deleted_data');
                Route::post('/restore', [AdminController::class, 'restore'])->name('admin.user_admin.restore');
            });

            // COUNTRY
            Route::prefix('country')->group(function () {
                Route::get('/', [CountryController::class, 'index'])->name('admin.country');
                Route::get('/get-data', [CountryController::class, 'get_data'])->name('admin.country.get_data');
                Route::get('/create', [CountryController::class, 'create'])->name('admin.country.create');
                Route::post('/store', [CountryController::class, 'store'])->name('admin.country.store');
                Route::get('/edit/{id}', [CountryController::class, 'edit'])->name('admin.country.edit');
                Route::post('/update/{id}', [CountryController::class, 'update'])->name('admin.country.update');
                Route::post('/delete', [CountryController::class, 'delete'])->name('admin.country.delete');
                Route::get('/deleted-data', [CountryController::class, 'deleted_data'])->name('admin.country.deleted_data');
                Route::get('/get-deleted-data', [CountryController::class, 'get_deleted_data'])->name('admin.country.get_deleted_data');
                Route::post('/restore', [CountryController::class, 'restore'])->name('admin.country.restore');

                // LANGUAGE
                Route::prefix('{parent_id}/language')->group(function () {
                    Route::get('/', [LanguageController::class, 'index'])->name('admin.language');
                    Route::get('/get-data', [LanguageController::class, 'get_data'])->name('admin.language.get_data');
                    Route::get('/create', [LanguageController::class, 'create'])->name('admin.language.create');
                    Route::post('/store', [LanguageController::class, 'store'])->name('admin.language.store');
                    Route::get('/edit/{id}', [LanguageController::class, 'edit'])->name('admin.language.edit');
                    Route::post('/update/{id}', [LanguageController::class, 'update'])->name('admin.language.update');
                    Route::post('/delete', [LanguageController::class, 'delete'])->name('admin.language.delete');
                    Route::get('/deleted-data', [LanguageController::class, 'deleted_data'])->name('admin.language.deleted_data');
                    Route::get('/get-deleted-data', [LanguageController::class, 'get_deleted_data'])->name('admin.language.get_deleted_data');
                    Route::post('/restore', [LanguageController::class, 'restore'])->name('admin.language.restore');
                    Route::post('/sorting', [LanguageController::class, 'sorting'])->name('admin.language.sorting');
                    Route::get('/dictionary/{id}', [LanguageController::class, 'dictionary'])->name('admin.language.dictionary');
                    Route::post('/dictionary/{id}/save', [LanguageController::class, 'dictionary_save'])->name('admin.language.dictionary.save');
                });
            });

            // BLOCKED IP
            Route::prefix('blocked-ip')->group(function () {
                Route::get('/', [BlockedIpController::class, 'index'])->name('admin.blocked_ip');
                Route::get('/get-data', [BlockedIpController::class, 'get_data'])->name('admin.blocked_ip.get_data');
                Route::get('/create', [BlockedIpController::class, 'create'])->name('admin.blocked_ip.create');
                Route::post('/store', [BlockedIpController::class, 'store'])->name('admin.blocked_ip.store');
                Route::get('/edit/{id}', [BlockedIpController::class, 'edit'])->name('admin.blocked_ip.edit');
                Route::post('/update/{id}', [BlockedIpController::class, 'update'])->name('admin.blocked_ip.update');
                Route::post('/delete', [BlockedIpController::class, 'delete'])->name('admin.blocked_ip.delete');
                Route::get('/deleted-data', [BlockedIpController::class, 'deleted_data'])->name('admin.blocked_ip.deleted_data');
                Route::get('/get-deleted-data', [BlockedIpController::class, 'get_deleted_data'])->name('admin.blocked_ip.get_deleted_data');
                Route::post('/restore', [BlockedIpController::class, 'restore'])->name('admin.blocked_ip.restore');
            });

            // ERROR LOGS
            Route::prefix('error-logs')->group(function () {
                Route::get('/', [ErrorLogController::class, 'index'])->name('admin.error_logs');
                Route::get('/get-data', [ErrorLogController::class, 'get_data'])->name('admin.error_logs.get_data');
                Route::get('/view/{id}', [ErrorLogController::class, 'view'])->name('admin.error_logs.view');
                Route::post('/update/{id}', [ErrorLogController::class, 'update'])->name('admin.error_logs.update');
            });

            // NAV MENU
            Route::prefix('nav-menu/{position}')->group(function () {
                Route::get('/', [NavMenuController::class, 'index'])->name('admin.nav_menu');
                Route::get('/get-data', [NavMenuController::class, 'get_data'])->name('admin.nav_menu.get_data');
                Route::post('/sorting', [NavMenuController::class, 'sorting'])->name('admin.nav_menu.sorting');
                Route::get('/create', [NavMenuController::class, 'create'])->name('admin.nav_menu.create');
                Route::post('/store', [NavMenuController::class, 'store'])->name('admin.nav_menu.store');
                Route::get('/edit/{id}', [NavMenuController::class, 'edit'])->name('admin.nav_menu.edit');
                Route::post('/update/{id}', [NavMenuController::class, 'update'])->name('admin.nav_menu.update');
                Route::post('/delete', [NavMenuController::class, 'delete'])->name('admin.nav_menu.delete');
                Route::get('/deleted-data', [NavMenuController::class, 'deleted_data'])->name('admin.nav_menu.deleted_data');
                Route::get('/get-deleted-data', [NavMenuController::class, 'get_deleted_data'])->name('admin.nav_menu.get_deleted_data');
                Route::post('/restore', [NavMenuController::class, 'restore'])->name('admin.nav_menu.restore');

                Route::prefix('{parent}')->group(function () {
                    Route::get('/', [NavMenuChildController::class, 'index'])->name('admin.nav_menu_child');
                    Route::get('/get-data', [NavMenuChildController::class, 'get_data'])->name('admin.nav_menu_child.get_data');
                    Route::post('/sorting', [NavMenuChildController::class, 'sorting'])->name('admin.nav_menu_child.sorting');
                    Route::get('/create', [NavMenuChildController::class, 'create'])->name('admin.nav_menu_child.create');
                    Route::post('/store', [NavMenuChildController::class, 'store'])->name('admin.nav_menu_child.store');
                    Route::get('/edit/{id}', [NavMenuChildController::class, 'edit'])->name('admin.nav_menu_child.edit');
                    Route::post('/update/{id}', [NavMenuChildController::class, 'update'])->name('admin.nav_menu_child.update');
                });
            });

            // PAGE
            Route::prefix('page')->group(function () {
                Route::get('/', [PageController::class, 'index'])->name('admin.page');
                Route::get('/get-data', [PageController::class, 'get_data'])->name('admin.page.get_data');
                Route::get('/create', [PageController::class, 'create'])->name('admin.page.create');
                Route::post('/store', [PageController::class, 'store'])->name('admin.page.store');
                Route::get('/edit/{id}', [PageController::class, 'edit'])->name('admin.page.edit');
                Route::post('/update/{id}', [PageController::class, 'update'])->name('admin.page.update');
                Route::post('/delete', [PageController::class, 'delete'])->name('admin.page.delete');
                Route::get('/deleted-data', [PageController::class, 'deleted_data'])->name('admin.page.deleted_data');
                Route::get('/get-deleted-data', [PageController::class, 'get_deleted_data'])->name('admin.page.get_deleted_data');
                Route::post('/restore', [PageController::class, 'restore'])->name('admin.page.restore');
            });

            // SOCIAL MEDIA
            Route::prefix('social-media')->group(function () {
                Route::get('/', [SocialMediaController::class, 'index'])->name('admin.social_media');
                Route::get('/get-data', [SocialMediaController::class, 'get_data'])->name('admin.social_media.get_data');
                Route::get('/create', [SocialMediaController::class, 'create'])->name('admin.social_media.create');
                Route::post('/store', [SocialMediaController::class, 'store'])->name('admin.social_media.store');
                Route::get('/edit/{id}', [SocialMediaController::class, 'edit'])->name('admin.social_media.edit');
                Route::post('/update/{id}', [SocialMediaController::class, 'update'])->name('admin.social_media.update');
                Route::post('/delete', [SocialMediaController::class, 'delete'])->name('admin.social_media.delete');
                Route::get('/deleted-data', [SocialMediaController::class, 'deleted_data'])->name('admin.social_media.deleted_data');
                Route::get('/get-deleted-data', [SocialMediaController::class, 'get_deleted_data'])->name('admin.social_media.get_deleted_data');
                Route::post('/restore', [SocialMediaController::class, 'restore'])->name('admin.social_media.restore');
                Route::post('/sorting', [SocialMediaController::class, 'sorting'])->name('admin.social_media.sorting');
            });

            // FAQ
            Route::prefix('faq')->group(function () {
                Route::get('/', [FaqController::class, 'index'])->name('admin.faq');
                Route::get('/get-data', [FaqController::class, 'get_data'])->name('admin.faq.get_data');
                Route::post('/sorting', [FaqController::class, 'sorting'])->name('admin.faq.sorting');
                Route::get('/create', [FaqController::class, 'create'])->name('admin.faq.create');
                Route::post('/store', [FaqController::class, 'store'])->name('admin.faq.store');
                Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('admin.faq.edit');
                Route::post('/update/{id}', [FaqController::class, 'update'])->name('admin.faq.update');
                Route::post('/delete', [FaqController::class, 'delete'])->name('admin.faq.delete');
                Route::get('/deleted-data', [FaqController::class, 'deleted_data'])->name('admin.faq.deleted_data');
                Route::get('/get-deleted-data', [FaqController::class, 'get_deleted_data'])->name('admin.faq.get_deleted_data');
                Route::post('/restore', [FaqController::class, 'restore'])->name('admin.faq.restore');

                Route::prefix('{parent}')->group(function () {
                    Route::get('/', [FaqItemController::class, 'index'])->name('admin.faq_item');
                    Route::get('/get-data', [FaqItemController::class, 'get_data'])->name('admin.faq_item.get_data');
                    Route::post('/sorting', [FaqItemController::class, 'sorting'])->name('admin.faq_item.sorting');
                    Route::get('/create', [FaqItemController::class, 'create'])->name('admin.faq_item.create');
                    Route::post('/store', [FaqItemController::class, 'store'])->name('admin.faq_item.store');
                    Route::get('/edit/{id}', [FaqItemController::class, 'edit'])->name('admin.faq_item.edit');
                    Route::post('/update/{id}', [FaqItemController::class, 'update'])->name('admin.faq_item.update');
                });
            });

            // NOTE
            Route::prefix('note')->group(function () {
                Route::get('/v1', [NoteController::class, 'index_v1'])->name('admin.note_v1');
                Route::get('/', [NoteController::class, 'index'])->name('admin.note');
                Route::get('/get-data', [NoteController::class, 'get_data'])->name('admin.note.get_data');
                Route::get('/create', [NoteController::class, 'create'])->name('admin.note.create');
                Route::post('/store', [NoteController::class, 'store'])->name('admin.note.store');
                Route::get('/edit/{id}', [NoteController::class, 'edit'])->name('admin.note.edit');
                Route::post('/update/{id}', [NoteController::class, 'update'])->name('admin.note.update');
                Route::post('/delete', [NoteController::class, 'delete'])->name('admin.note.delete');
                Route::get('/deleted-data', [NoteController::class, 'deleted_data'])->name('admin.note.deleted_data');
                Route::get('/get-deleted-data', [NoteController::class, 'get_deleted_data'])->name('admin.note.get_deleted_data');
                Route::post('/restore', [NoteController::class, 'restore'])->name('admin.note.restore');

                Route::get('/get-data/{id}', [NoteController::class, 'get_data_single'])->name('admin.note.get_data.single');
                Route::get('/load-more', [NoteController::class, 'load_more'])->name('admin.note.load_more');
            });

            // FORM
            Route::prefix('form')->group(function () {
                Route::get('/', [FormController::class, 'index'])->name('admin.form');
                Route::get('/get-data', [FormController::class, 'get_data'])->name('admin.form.get_data');
                Route::prefix('{type}')->group(function () {
                    Route::get('/create', [FormController::class, 'create'])->name('admin.form.create');
                    Route::post('/store', [FormController::class, 'store'])->name('admin.form.store');
                });
                Route::get('/edit/{id}', [FormController::class, 'edit'])->name('admin.form.edit');
                Route::post('/update/{id}', [FormController::class, 'update'])->name('admin.form.update');
                Route::post('/delete', [FormController::class, 'delete'])->name('admin.form.delete');
                Route::get('/deleted-data', [FormController::class, 'deleted_data'])->name('admin.form.deleted_data');
                Route::get('/get-deleted-data', [FormController::class, 'get_deleted_data'])->name('admin.form.get_deleted_data');
                Route::post('/restore', [FormController::class, 'restore'])->name('admin.form.restore');
            });

            // BANNER
            Route::prefix('banner/{position}')->group(function () {
                Route::get('/', [BannerController::class, 'index'])->name('admin.banner');
                Route::get('/get-data', [BannerController::class, 'get_data'])->name('admin.banner.get_data');
                Route::post('/sorting', [BannerController::class, 'sorting'])->name('admin.banner.sorting');
                Route::get('/create', [BannerController::class, 'create'])->name('admin.banner.create');
                Route::post('/store', [BannerController::class, 'store'])->name('admin.banner.store');
                Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('admin.banner.edit');
                Route::post('/update/{id}', [BannerController::class, 'update'])->name('admin.banner.update');
                Route::post('/delete', [BannerController::class, 'delete'])->name('admin.banner.delete');
                Route::get('/deleted-data', [BannerController::class, 'deleted_data'])->name('admin.banner.deleted_data');
                Route::get('/get-deleted-data', [BannerController::class, 'get_deleted_data'])->name('admin.banner.get_deleted_data');
                Route::post('/restore', [BannerController::class, 'restore'])->name('admin.banner.restore');
            });

            // EMAIL TEMPLATE
            Route::prefix('email-template')->group(function () {
                Route::get('/', [EmailTemplateController::class, 'index'])->name('admin.email_template');
                Route::get('/get-data', [EmailTemplateController::class, 'get_data'])->name('admin.email_template.get_data');
                Route::get('/create', [EmailTemplateController::class, 'create'])->name('admin.email_template.create');
                Route::post('/store', [EmailTemplateController::class, 'store'])->name('admin.email_template.store');
                Route::get('/edit/{id}', [EmailTemplateController::class, 'edit'])->name('admin.email_template.edit');
                Route::post('/update/{id}', [EmailTemplateController::class, 'update'])->name('admin.email_template.update');
                Route::post('/delete', [EmailTemplateController::class, 'delete'])->name('admin.email_template.delete');
                Route::get('/deleted-data', [EmailTemplateController::class, 'deleted_data'])->name('admin.email_template.deleted_data');
                Route::get('/get-deleted-data', [EmailTemplateController::class, 'get_deleted_data'])->name('admin.email_template.get_deleted_data');
                Route::post('/restore', [EmailTemplateController::class, 'restore'])->name('admin.email_template.restore');
                Route::post('/send-email-test', [EmailTemplateController::class, 'send_email_test'])->name('admin.email_template.send_email_test');
            });
        });
    }
});
