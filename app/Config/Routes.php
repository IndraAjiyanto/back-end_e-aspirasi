<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/login', 'AuthController::login');
$routes->post('/register', 'AuthController::register');
$routes->post('/logout', 'AuthController::logout');
$routes->get('/blocked', 'Home::blocked');


//Untuk Mahasiswa
$routes->group('mahasiswa', ['filter' => 'jwt:mahasiswa'], function($routes) {
    $routes->resource('aspirasi', ['controller' => 'AspirasiController']);
    $routes->get('unit', 'UnitController::index');
    // $routes->resource('dashboard', 'MahasiswaController::dashboard');
});


//Untuk PPKS
$routes->group('ppks', ['filter' => 'jwt:ppks'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardPpks');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Untuk Sarpras
$routes->group('sarpras', ['filter' => 'jwt:sarpras'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardSarpras');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Untuk Akademik
$routes->group('akademik', ['filter' => 'jwt:akademik'], function($routes) {
    $routes->get('aspirasi/all/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardAkademik');
    $routes->get('aspirasi/(:num)', 'AspirasiController::show/$1');
});

//Jawaban (Umum)
$routes->group('unit', ['filter' => 'jwt:ppks,sarpras,akademik'], function($routes) {
    $routes->resource('jawaban', ['controller' => 'JawabanController']);
});
