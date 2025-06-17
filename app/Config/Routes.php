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
$routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], function($routes) {
    $routes->resource('aspirasi', ['controller' => 'AspirasiController']);
    $routes->get('dashboard', 'MahasiswaController::dashboard');
});

//Untuk PPKS
$routes->group('ppks', ['filter' => 'role:ppks'], function($routes) {
    $routes->get('aspirasi/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardPpks');
});

//Untuk Sarpras
$routes->group('sarpras', ['filter' => 'role:sarpras'], function($routes) {
    $routes->get('aspirasi/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardSarpras');
});

//Untuk Akademik
$routes->group('akademik', ['filter' => 'role:akademik'], function($routes) {
    $routes->get('aspirasi/(:num)', 'UnitController::getAspirasiUnit/$1');
    $routes->get('dashboard', 'UnitController::dashboardAkademik');
});

//Jawaban (Umum)
$routes->group('unit', ['filter' => 'role:mahasiswa,ppks,sarpras,akademik'], function($routes) {
    $routes->resource('jawaban', ['controller' => 'JawabanController']);
    $routes->get('jawaban/aspirasi/(:num)', 'JawabanController::jawabanUnit/$1');
});

