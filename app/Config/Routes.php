<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ========================================
// Rute Publik (Tanpa Filter)
// ========================================

// Auth Routes
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/process-login', 'Auth::processLogin');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/access-denied', 'Auth::accessDenied');

// API Auth Routes
$routes->post('api/auth/validate-token', 'Auth::validateToken');
$routes->post('api/auth/refresh-token', 'Auth::refreshToken');

// Halaman Access Denied (IP Whitelist)
$routes->get('access-denied', 'Auth::accessDenied');

// ========================================
// Rute User (Halaman Utama - Akses Publik/Tanpa Login)
// ========================================

// Rute utama (halaman home yang menampilkan form dan daftar)
$routes->get('/', 'Document::index');

// Rute untuk memproses data dari form upload
$routes->post('create', 'Document::create');
$routes->post('document/create', 'Document::create');

// Rute untuk mengunduh file berdasarkan hash blok
$routes->get('download/(:any)', 'Document::download/$1');

// Rute verifikasi user dihapus — fitur deprecated/removed

// ========================================
// Rute Admin Panel (Dilindungi Auth + IP Whitelist)
// ========================================

$routes->group('admin', ['filter' => ['auth:admin', 'ip_whitelist']], static function ($routes) {

    // Dashboard Admin
    $routes->get('/', 'Admin::dashboard');
    $routes->get('dashboard', 'Admin::dashboard');

    // Blockchain Explorer (Read-only monitoring)
    $routes->get('explorer', 'Admin::explorer');

    // System Monitoring
    $routes->get('monitoring', 'Admin::monitoring');

    // Manajemen Backup
    $routes->get('backups', 'Admin::backups');
    $routes->get('backup/create', 'Admin::createBackup');

    // Manajemen IP Whitelist
    $routes->get('whitelist', 'Admin::whitelist');
    $routes->post('whitelist/add', 'Admin::addWhitelist');
    $routes->get('whitelist/activate/(:num)', 'Admin::activateIP/$1');
    $routes->get('whitelist/deactivate/(:num)', 'Admin::deactivateIP/$1');
    $routes->get('whitelist/delete/(:num)', 'Admin::deleteWhitelist/$1');

    // Recovery Manual
    $routes->get('recover/(:num)', 'Admin::manualRecover/$1');

    // Consensus Recovery (3-Database Majority Voting)
    $routes->get('consensus/check', 'Admin::consensusCheck');
    $routes->post('consensus/recover', 'Admin::consensusRecover');
    $routes->get('consensus/history', 'Admin::recoveryHistory');
    $routes->post('consensus/rollback/(:num)', 'Admin::consensusRollback/$1');
    $routes->get('consensus/quick-check', 'Admin::quickConsensusCheck'); // AJAX

    // User Management
    $routes->get('users', 'Admin::users');
    $routes->post('users/add', 'Admin::addUser');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/update/(:num)', 'Admin::updateUser/$1');
    $routes->get('users/delete/(:num)', 'Admin::deleteUser/$1');
    $routes->get('users/toggle/(:num)', 'Admin::toggleUserStatus/$1');
});

// ========================================
// API RESTful Endpoints
// ========================================

$routes->group('api', static function ($routes) {

    // Blocks
    $routes->get('blocks', 'Api::blocks');
    $routes->get('blocks/(:num)', 'Api::block/$1');
    $routes->get('blocks/hash/(:any)', 'Api::blockByHash/$1');

    // Chain Validation
    $routes->get('chain/validate', 'Api::validateChain');

    // Backups
    $routes->get('backups', 'Api::backups');

    // Whitelist (Protected)
    $routes->get('whitelist', 'Api::whitelist');
    $routes->post('whitelist', 'Api::addWhitelist');
    $routes->put('whitelist/(:num)/activate', 'Api::activateWhitelist/$1');
    $routes->put('whitelist/(:num)/deactivate', 'Api::deactivateWhitelist/$1');
    $routes->delete('whitelist/(:num)', 'Api::deleteWhitelist/$1');

    // Statistics
    $routes->get('stats', 'Api::stats');

    // Recovery
    $routes->post('recovery/(:num)', 'Api::recovery/$1');
    $routes->post('check-integrity', 'Api::checkIntegrity');
    $routes->post('auto-recovery', 'Api::autoRecovery');

    // Activity Logs
    $routes->get('activity-logs', 'Api::activityLogs');
});

// ========================================
// 404 Not Found Page
// ========================================

// Catch-all route untuk halaman yang tidak ditemukan (harus di paling akhir)
$routes->get('(:any)', 'Home::notfound');
