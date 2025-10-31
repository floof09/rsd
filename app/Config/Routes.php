<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

// Auth Routes
$routes->get('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/doLogin', 'Auth::doLogin');
$routes->get('auth/logout', 'Auth::logout');

// Admin Routes
$routes->get('admin/dashboard', 'AdminDashboard::index');
$routes->get('admin/application', 'AdminApplication::index');
$routes->post('admin/application/save', 'AdminApplication::save');
$routes->get('admin/applications', 'AdminApplication::list');
$routes->get('admin/system-logs', 'SystemLogs::index');
$routes->get('admin/system-logs/filter/(:any)', 'SystemLogs::filterByModule/$1');
$routes->get('admin/system-logs/clear-old', 'SystemLogs::clearOldLogs');
