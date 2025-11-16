<?php
declare(strict_types=1);

// autoload
require __DIR__ . '/../vendor/autoload.php';

// tiny .env loader (reads .env into getenv and $_ENV)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1=>null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

Session::start();

// --- START: FIX FOR SUBFOLDER ROUTING ---
$base_path = '/metro_wb_lab-main/public';
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$request_path = strtok($request_uri, '?'); // Get path without query string

if (strpos($request_path, $base_path) === 0) {
    $route = substr($request_path, strlen($base_path));
} else {
    $route = $request_path;
}
if (empty($route) || $route[0] !== '/') {
    $route = '/' . $route;
}
// --- END: FIX FOR SUBFOLDER ROUTING ---
// --- END: FIX FOR SUBFOLDER ROUTING ---

var_dump("Request URI:", $request_uri, "Calculated Route:", $route); die();


$router = new Router();
$auth = new AuthController();
$dash = new DashboardController();

$router->get('/', fn() => $auth->showLogin());
$router->get('/login', fn() => $auth->showLogin());
$router->get('/register', fn() => $auth->showRegister());
$router->get('/dashboard', fn() => $dash->index());
$router->get('/test-mail', fn() => $dash->testMail());

$router->post('/register', fn() => $auth->register());
$router->post('/login', fn() => $auth->login());
$router->get('/logout', fn() => $auth->logout());

$router->dispatch($route, $_SERVER['REQUEST_METHOD'] ?? 'GET');
