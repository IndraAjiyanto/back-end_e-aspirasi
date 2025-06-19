<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
    $routes->post('/login', 'AuthController::login');
    $routes->post('/register', 'AuthController::register');
$routes->post('/logout', 'AuthController::logout');

//Untuk Mahasiswa
$routes->group('mahasiswa', ['filter' => 'jwt:mahasiswa'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'AspirasiController::getAspirasi/$1');
    $routes->resource('aspirasi', ['controller' => 'AspirasiController']);
    $routes->get('unit', 'UnitController::index');
});

//Untuk PPKS
$routes->group('ppks', ['filter' => 'jwt:ppks'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Untuk Sarpras
$routes->group('sarpras', ['filter' => 'jwt:sarpras'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Untuk Akademik
$routes->group('akademik', ['filter' => 'jwt:akademik'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Jawaban (Umum)
$routes->group('unit', ['filter' => 'jwt:ppks,sarpras,akademik'], function($routes) {
    $routes->resource('jawaban', ['controller' => 'JawabanController']);
});
