<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('register', function($routes){
    $routes->get('/', 'RegisterController::index');
    $routes->post('/', 'RegisterController::store');
});

$routes->group('login', function ($routes) {
    $routes->get('/', 'LoginController::index');
    $routes->post('/', 'LoginController::login');
});

$routes->group('logout', function ($routes) {
    $routes->get('/', 'LogoutController::index');
});

$routes->get('/dashboard', 'Dashboard::index');

$routes->group('purchase-orders', function($routes) {
    $routes->get('new', 'PurchaseOrderController::new');
    $routes->post('create', 'PurchaseOrderController::create');
    $routes->get('view/(:num)', 'PurchaseOrderController::view/$1');
    $routes->post('delete/(:num)', 'PurchaseOrderController::delete/$1');
    $routes->post('accept/(:num)', 'PurchaseOrderController::accept/$1');
    $routes->post('reject/(:num)', 'PurchaseOrderController::reject/$1');
    $routes->post('management/(:num)', 'PurchaseOrderController::management/$1');
    $routes->post('complete/(:num)', 'PurchaseOrderController::complete/$1');
    $routes->post('upload-image', 'PurchaseOrderController::uploadImage');
    $routes->match(['get', 'post'], 'upload-image', 'PurchaseOrderController::uploadImage');
});



