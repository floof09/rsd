<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Redirect root to /login so the URL reflects /login
$routes->get('/', function() { return redirect()->to('/login'); });

// Auth Routes
$routes->get('login', 'Auth::login');
$routes->get('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/doLogin', 'Auth::doLogin');
$routes->get('auth/logout', 'Auth::logout');

// Admin Routes
$routes->get('admin/dashboard', 'AdminDashboard::index');
$routes->get('admin/application', 'AdminApplication::index');
$routes->post('admin/application/save', 'AdminApplication::save');
$routes->get('admin/applications', 'AdminApplication::list');
$routes->get('admin/applications/(:num)', 'AdminApplication::show/$1');
$routes->get('admin/applications/(:num)/edit', 'AdminApplication::edit/$1');
$routes->post('admin/applications/(:num)/update', 'AdminApplication::update/$1');
$routes->get('admin/applications/(:num)/resume', 'AdminApplication::resume/$1');
// Admin: Update status
$routes->post('admin/applications/(:num)/status', 'AdminApplication::updateStatus/$1');
$routes->get('admin/system-logs', 'SystemLogs::index');
$routes->get('admin/system-logs/filter/(:any)', 'SystemLogs::filterByModule/$1');
$routes->get('admin/system-logs/clear-old', 'SystemLogs::clearOldLogs');
// Admin: Recruiters/Users
$routes->get('admin/recruiters', 'AdminUsers::activeList');
// Admin: Create recruiter account
$routes->post('admin/recruiters/create', 'AdminUsers::create');
// Admin: Email Test
$routes->get('admin/email/test', 'EmailTest::send');
// Admin: Reports
$routes->get('admin/reports', 'Reports::index');
$routes->get('admin/reports/data', 'Reports::data');
$routes->get('admin/reports/export', 'Reports::export');

// Interviewer Routes
$routes->get('interviewer/dashboard', 'InterviewerDashboard::index');
$routes->get('interviewer/application', 'AdminApplication::index');
$routes->get('interviewer/applications', 'AdminApplication::interviewerList');
$routes->get('interviewer/applications/(:num)', 'AdminApplication::show/$1');
$routes->get('interviewer/applications/(:num)/edit', 'AdminApplication::edit/$1');
$routes->post('interviewer/applications/(:num)/update', 'AdminApplication::update/$1');
$routes->get('interviewer/applications/(:num)/resume', 'AdminApplication::resume/$1');
	// IGT additional interview (interviewer only)
	$routes->get('interviewer/applications/(:num)/igt', 'AdminApplication::igtForm/$1');
	$routes->post('interviewer/applications/(:num)/igt/save', 'AdminApplication::igtSave/$1');
// Interviewer: Approve for endorsement
$routes->post('interviewer/applications/(:num)/approve', 'AdminApplication::approveForEndorsement/$1');

// Geocoding API proxy routes
$routes->get('api/geocode/reverse', 'Geocode::reverse');
$routes->get('api/geocode/search', 'Geocode::search');

// Maintenance/Tools (guarded by token in .env)
$routes->get('tools/migrate', 'Tools::migrate');
$routes->get('tools/seed-admin', 'Tools::seedAdmin');
$routes->get('tools/seed-interviewer', 'Tools::seedInterviewer');
$routes->get('tools/env', 'Tools::env');
$routes->get('tools/logs', 'Tools::logs');
$routes->get('tools/health', 'Tools::health');
// Create a log file with sample entries for diagnostics
$routes->get('tools/test-log', 'Tools::testLog');
// Repair migrations table state and run remaining migrations
$routes->get('tools/repair-migrations', 'Tools::repairMigrations');
// Verify a user's credentials against stored hash
$routes->get('tools/auth-check', 'Tools::authCheck');
// Database status: tables and counts
$routes->get('tools/db-status', 'Tools::dbStatus');
// Session diagnostics
$routes->get('tools/session-info', 'Tools::sessionInfo');
$routes->get('tools/session-files', 'Tools::sessionFiles');
