<?php

use CodeIgniter\Router\RouteCollection;

/**
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
*/

$routes->group('', ['namespace' => 'App\Controllers\Public'], static function ($routes) {
    $routes->get('/', 'HomeController::index', [
        'as' => 'home',
    ]);

    $routes->get('sach', 'BookController::index', [
        'as' => 'catalog.index',
    ]);

    $routes->get('sach/(:segment)', 'BookController::show/$1', [
        'as' => 'books.show',
    ]);

    $routes->post('tracking/otp/request', 'TrackingController::requestOtp', [
        'as' => 'tracking.otp.request',
        'filter' => ['csrf', 'throttle:otp-request'],
    ]);

    $routes->post('tracking/otp/verify', 'TrackingController::verifyOtp', [
        'as' => 'tracking.otp.verify',
        'filter' => ['csrf', 'throttle:otp-verify'],
    ]);

    $routes->post('tracking/rules', 'TrackingController::createRule', [
        'as' => 'tracking.rules.create',
        'filter' => ['csrf', 'verifiedEmail', 'throttle:tracking-create'],
    ]);

    $routes->post('tracking/rules/disable', 'TrackingController::disableRule', [
        'as' => 'tracking.rules.disable',
        'filter' => ['csrf', 'signedTrackingToken'],
    ]);

    $routes->get('go/(:num)', 'RedirectController::go/$1', [
        'as' => 'redirect.go',
        'filter' => 'throttle:redirect',
    ]);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

$routes->group($adminPath, [
    'namespace' => 'App\Controllers\Admin',
], static function ($routes) {
    $routes->get('login', 'AuthController::loginForm', [
        'as' => 'admin.login.form',
        'filter' => 'adminGuest',
    ]);

    $routes->post('login', 'AuthController::login', [
        'as' => 'admin.login.submit',
        'filter' => ['csrf', 'adminGuest', 'throttle:admin-login'],
    ]);

    $routes->post('logout', 'AuthController::logout', [
        'as' => 'admin.logout',
        'filter' => ['csrf', 'adminAuth'],
    ]);

    $routes->group('', ['filter' => 'adminAuth'], static function ($routes) {
        $routes->get('/', 'DashboardController::index', [
            'as' => 'admin.dashboard',
        ]);

        $routes->get('books', 'BookCrudController::index', [
            'as' => 'admin.books.index',
        ]);

        $routes->get('books/new', 'BookCrudController::new', [
            'as' => 'admin.books.new',
        ]);

        $routes->post('books', 'BookCrudController::create', [
            'as' => 'admin.books.create',
            'filter' => 'csrf',
        ]);

        $routes->get('books/(:num)', 'BookCrudController::show/$1', [
            'as' => 'admin.books.show',
        ]);

        $routes->get('books/(:num)/edit', 'BookCrudController::edit/$1', [
            'as' => 'admin.books.edit',
        ]);

        $routes->post('books/(:num)', 'BookCrudController::update/$1', [
            'as' => 'admin.books.update',
            'filter' => 'csrf',
        ]);

        $routes->post('books/(:num)/delete', 'BookCrudController::delete/$1', [
            'as' => 'admin.books.delete',
            'filter' => 'csrf',
        ]);

        $routes->get('exports/books.csv', 'ExportController::booksCsv', [
            'as' => 'admin.exports.books',
        ]);

        $routes->get('exports/activity.csv', 'ExportController::activityCsv', [
            'as' => 'admin.exports.activity',
        ]);
    });
});