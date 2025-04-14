<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/aspirasi', 'AspirasiController::index');
$routes->post('/aspirasi', 'AspirasiController::create');
$routes->post('/aspirasi/update/(:num)', 'AspirasiController::update/$1');
$routes->delete('/aspirasi/delete/(:num)', 'AspirasiController::delete/$1');
$routes->post('/aspirasi/status/(:num)', 'AspirasiController::updateStatus/$1');

$routes->get('/jawaban', 'JawabanController::index');
$routes->get('/jawaban', 'JawabanController::create');
$routes->get('/jawaban', 'JawabanController::update');
$routes->get('/jawaban', 'JawabanController::delete');
