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
$routes->get('/jawaban/aspirasi/(:num)', 'JawabanController::jawabanUnit/$1');
$routes->resource('jawaban', ['controller' => 'JawabanController']);
$routes->get('/unit/aspirasi/(:num)','UnitController::getAspirasiUnit/$1');
$routes->resource('unit', ['controller' => 'UnitController']);
// $routes->resource('auth', ['controller' => 'AuthController']);

$routes->post('auth/login', 'AuthController::attemptLogin'); // Post request for login
$routes->get('auth/register', 'AuthController::register');

$routes->post('auth/register', 'AuthController::attemptRegister'); // Post request for registration
$routes->get('auth/logout', 'AuthController::logout'); // Get request for logout
$routes->post('auth/forgot-password', 'AuthController::attemptForgot'); // Post request for forgot password
$routes->post('auth/reset-password', 'AuthController::attemptReset'); // Post request for password reset
$routes->get('auth/activate', 'AuthController::activateAccount'); // Get request for account activation



// $routes->get('/jawaban', 'JawabanController::index');
// $routes->post('/jawaban', 'JawabanController::create');
// $routes->get('/jawaban/edit/(:num)', 'JawabanController::edit/$1');
// $routes->post('/jawaban/update/(:num)', 'JawabanController::update/$1');
<<<<<<< HEAD
// $routes->post('/jawaban', 'JawabanController::delete');


=======
// $routes->post('/jawabanan', 'JawabanController::delete','updtstatsu');
>>>>>>> 81138c1162a29ad99658a02b9295ef5319272e00
