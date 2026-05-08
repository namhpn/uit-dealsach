<?php

use CodeIgniter\Router\RouteCollection;

/**
 * DealSach Route Configuration
 *
 * Route map reference: uit-dealsach-prompts/P1/RouteMap.md
 * Admin prefix loaded from .env: dealsach.adminPath (default: ds-admin)
 *
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Public\HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

$adminPath = getenv('dealsach.adminPath') ?: 'ds-admin';

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Homepage, catalog, book detail, OTP tracking, outbound redirect.
| All routes use the 'web' filter implicitly via CI4 default behaviour.
*/

$routes->group('', ['namespace' => 'App\Controllers\Public'], static function ($routes) {

    // ── Homepage ──
    $routes->get('/', 'HomeController::index', [
        'as' => 'home',
    ]);

    // ── Catalog & Book Detail ──
    $routes->get('sach', 'BookController::index', [
        'as' => 'catalog.index',
    ]);

    $routes->get('sach/(:segment)', 'BookController::show/$1', [
        'as' => 'books.show',
    ]);

    // ── OTP Tracking ──
    $routes->post('tracking/otp/request', 'TrackingController::requestOtp', [
        'as'     => 'tracking.otp.request',
        'filter' => ['csrf', 'throttle:otp-request'],
    ]);

    $routes->post('tracking/otp/verify', 'TrackingController::verifyOtp', [
        'as'     => 'tracking.otp.verify',
        'filter' => ['csrf', 'throttle:otp-verify'],
    ]);

    // ── Tracking Rules ──
    $routes->post('tracking/rules', 'TrackingController::createRule', [
        'as'     => 'tracking.rules.create',
        'filter' => ['csrf', 'verifiedEmail', 'throttle:tracking-create'],
    ]);

    $routes->post('tracking/rules/disable', 'TrackingController::disableRule', [
        'as'     => 'tracking.rules.disable',
        'filter' => ['csrf', 'signedTrackingToken'],
    ]);

    // ── Outbound Redirect ──
    $routes->get('go/(:num)', 'RedirectController::go/$1', [
        'as'     => 'redirect.go',
        'filter' => 'throttle:redirect',
    ]);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Hidden admin panel behind dynamic prefix from .env.
| Auth routes (login/logout) have their own filter sets.
| All other admin routes are wrapped in the adminAuth filter group.
*/

$routes->group($adminPath, [
    'namespace' => 'App\Controllers\Admin',
], static function ($routes) {

    // ── Authentication ──
    $routes->get('login', 'AuthController::loginForm', [
        'as'     => 'admin.login.form',
        'filter' => 'adminGuest',
    ]);

    $routes->post('login', 'AuthController::login', [
        'as'     => 'admin.login.submit',
        'filter' => ['csrf', 'adminGuest', 'throttle:admin-login'],
    ]);

    $routes->post('logout', 'AuthController::logout', [
        'as'     => 'admin.logout',
        'filter' => ['csrf', 'adminAuth'],
    ]);

    // ── Authenticated Admin Area ──
    $routes->group('', ['filter' => 'adminAuth'], static function ($routes) {

        // Dashboard
        $routes->get('/', 'DashboardController::index', [
            'as' => 'admin.dashboard',
        ]);

        // ── Book CRUD ──
        $routes->get('books', 'BookCrudController::index', [
            'as' => 'admin.books.index',
        ]);

        $routes->get('books/new', 'BookCrudController::createForm', [
            'as' => 'admin.books.new',
        ]);

        $routes->post('books', 'BookCrudController::create', [
            'as'     => 'admin.books.create',
            'filter' => 'csrf',
        ]);

        $routes->get('books/(:num)', 'BookCrudController::show/$1', [
            'as' => 'admin.books.show',
        ]);

        $routes->get('books/(:num)/edit', 'BookCrudController::edit/$1', [
            'as' => 'admin.books.edit',
        ]);

        $routes->post('books/(:num)', 'BookCrudController::update/$1', [
            'as'     => 'admin.books.update',
            'filter' => 'csrf',
        ]);

        $routes->post('books/(:num)/delete', 'BookCrudController::delete/$1', [
            'as'     => 'admin.books.delete',
            'filter' => 'csrf',
        ]);

        // ── CSV Exports ──
        $routes->get('exports/books.csv', 'ExportController::booksCsv', [
            'as' => 'admin.exports.books',
        ]);

        $routes->get('exports/activity.csv', 'ExportController::activityCsv', [
            'as' => 'admin.exports.activity',
        ]);

        $routes->get('ajax/books/search', 'AjaxController::bookSearch', [
            'as' => 'admin.ajax.books.search',
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| CLI Commands (registered via Spark, not as HTTP routes)
|--------------------------------------------------------------------------
| php spark dealsach:crawl fahasa      → Commands\DealSachCrawlCommand
| php spark dealsach:crawl phuongnam   → Commands\DealSachCrawlCommand
| php spark dealsach:crawl tiki        → Commands\DealSachCrawlCommand
| php spark dealsach:crawl shopee      → Commands\DealSachCrawlCommand
| php spark dealsach:crawl all         → Commands\DealSachCrawlCommand
| php spark dealsach:alerts            → Commands\DealSachAlertsCommand
*/
