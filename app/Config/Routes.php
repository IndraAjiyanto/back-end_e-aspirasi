<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// $routes->get('/aspirasi', 'AspirasiController::index');
// $routes->post('/aspirasi', 'AspirasiController::create');
// $routes->get('/aspirasi/edit/(:num)', 'AspirasiController::edit/$1');
// $routes->post('/aspirasi/update/(:num)', 'AspirasiController::update/$1');
// $routes->post('/aspirasi/delete/(:num)', 'AspirasiController::delete/$1');
// $routes->post('/aspirasi/status/(:num)', 'AspirasiController::updateStatus/$1');
$routes->resource('aspirasi', ['controller' => 'AspirasiController']);
$routes->resource('jawaban', ['controller' => 'JawabanController']);
$routes->get('/unit/aspirasi/(:num)','UnitController::getAspirasiUnit/$1');
$routes->resource('unit', ['controller' => 'UnitController']);


// $routes->get('/jawaban', 'JawabanController::index');
// $routes->post('/jawaban', 'JawabanController::create');
// $routes->get('/jawaban/edit/(:num)', 'JawabanController::edit/$1');
// $routes->post('/jawaban/update/(:num)', 'JawabanController::update/$1');
// $routes->post('/jawabanan', 'JawabanController::delete','updtstatsu');
