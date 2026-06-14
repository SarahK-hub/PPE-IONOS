<?php
declare(strict_types=1);

/*
 * public/index.php — adapté IONOS
 * - Lecture .env pour la BDD
 * - Logs dans /ppe_logs
 * - Router avec support regex
 * - Normalisation robuste du path (sous-dossier + /index.php/)
 */

$logDir = __DIR__ . '/../ppe_logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Ping de vie
@file_put_contents($logDir . '/ping.log', '[' . date('c') . "] index.php reached\n", FILE_APPEND);

// Capture des erreurs fatales
register_shutdown_function(function () use ($logDir) {
    $e = error_get_last();
    if ($e) {
        @file_put_contents(
            $logDir . '/php-fatal.log',
            '[' . date('c') . "] {$e['type']} {$e['message']} in {$e['file']}:{$e['line']}\n",
            FILE_APPEND
        );
    }
});

// Session
session_start();

// Log brut des requêtes (diagnostic POST sur IONOS)
@file_put_contents(
    $logDir . '/raw.log',
    '[' . date('c') . '] ' . ($_SERVER['REQUEST_METHOD'] ?? '?') . ' ' . ($_SERVER['REQUEST_URI'] ?? '?') .
    ' CT=' . ($_SERVER['CONTENT_TYPE'] ?? '-') .
    ' CL=' . ($_SERVER['CONTENT_LENGTH'] ?? '-') . "\n",
    FILE_APPEND
);

// Autoload simple
spl_autoload_register(function (string $class): void {
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

use Core\Router;

$router = new Router();

// ===================== ROUTES AUTH =====================
$router->get('/',          [Controllers\AuthController::class, 'login']);
$router->get('/login',     [Controllers\AuthController::class, 'login']);
$router->get('/auth',      [Controllers\AuthController::class, 'login']);
$router->post('/auth',     [Controllers\AuthController::class, 'doLogin']);
$router->post('/login',    [Controllers\AuthController::class, 'doLogin']);
$router->get('/dashboard', [Controllers\AuthController::class, 'dashboard']);
$router->get('/dashboard/comptable', [Controllers\AuthController::class, 'comptable']);
$router->get('/dashboard/visiteur',  [Controllers\AuthController::class, 'visiteur']);
$router->get('/logout',    [Controllers\AuthController::class, 'logout']);

// ===================== ROUTES ETAT =====================
$router->get('/etat',                      [Controllers\EtatController::class, 'index']);
$router->get('/etat/',                     [Controllers\EtatController::class, 'index']);
$router->get('/etat/create',               [Controllers\EtatController::class, 'create']);
$router->post('/etat/create',              [Controllers\EtatController::class, 'store']);
$router->get('#^/etat/([0-9]+)$#',         [Controllers\EtatController::class, 'show']);
$router->get('#^/etat/([0-9]+)/update$#',  [Controllers\EtatController::class, 'update']);
$router->post('#^/etat/([0-9]+)/update$#', [Controllers\EtatController::class, 'save']);
$router->post('#^/etat/([0-9]+)/delete$#', [Controllers\EtatController::class, 'delete']);

// ===================== ROUTES FRAIS FORFAIT =====================
$router->get('/fraisforfait',                      [Controllers\fraisforfaitController::class, 'index']);
$router->get('/fraisforfait/',                     [Controllers\fraisforfaitController::class, 'index']);
$router->get('/fraisforfait/create',               [Controllers\fraisforfaitController::class, 'create']);
$router->post('/fraisforfait/store',               [Controllers\fraisforfaitController::class, 'store']);
$router->get('#^/fraisforfait/([0-9]+)$#',         [Controllers\fraisforfaitController::class, 'show']);
$router->get('#^/fraisforfait/([0-9]+)/update$#',  [Controllers\fraisforfaitController::class, 'update']);
$router->post('#^/fraisforfait/([0-9]+)/update$#', [Controllers\fraisforfaitController::class, 'save']);
$router->post('#^/fraisforfait/([0-9]+)/delete$#', [Controllers\fraisforfaitController::class, 'delete']);

// ===================== ROUTES FRAIS HORS FORFAIT =====================
$router->get('/frais_hors_forfait',                      [Controllers\frais_hors_forfaitController::class, 'index']);
$router->get('/frais_hors_forfait/',                     [Controllers\frais_hors_forfaitController::class, 'index']);
$router->get('/frais_hors_forfait/create',               [Controllers\frais_hors_forfaitController::class, 'create']);
$router->post('/frais_hors_forfait/store',               [Controllers\frais_hors_forfaitController::class, 'store']);
$router->get('#^/frais_hors_forfait/([0-9]+)$#',         [Controllers\frais_hors_forfaitController::class, 'show']);
$router->get('#^/frais_hors_forfait/([0-9]+)/update$#',  [Controllers\frais_hors_forfaitController::class, 'update']);
$router->post('#^/frais_hors_forfait/([0-9]+)/update$#', [Controllers\frais_hors_forfaitController::class, 'save']);
$router->post('#^/frais_hors_forfait/([0-9]+)/delete$#', [Controllers\frais_hors_forfaitController::class, 'delete']);

// ===================== ROUTES VISITEUR =====================
$router->get('/visiteur',                      [Controllers\visiteurController::class, 'index']);
$router->get('/visiteur/',                     [Controllers\visiteurController::class, 'index']);
$router->get('/visiteur/create',               [Controllers\visiteurController::class, 'create']);
$router->post('/visiteur/store',               [Controllers\visiteurController::class, 'store']);
$router->get('#^/visiteur/([0-9]+)$#',         [Controllers\visiteurController::class, 'show']);
$router->get('#^/visiteur/([0-9]+)/update$#',  [Controllers\visiteurController::class, 'update']);
$router->post('#^/visiteur/([0-9]+)/update$#', [Controllers\visiteurController::class, 'save']);
$router->post('#^/visiteur/([0-9]+)/delete$#', [Controllers\visiteurController::class, 'delete']);

// ===================== ROUTES FICHE FRAIS =====================
$router->get('/fichefrais',                                          [Controllers\fichefraisController::class, 'index']);
$router->get('/fichefrais/',                                         [Controllers\fichefraisController::class, 'index']);
$router->get('/mes-fiches',                                          [Controllers\fichefraisController::class, 'mesFiches']);
$router->get('/fichefrais/create',                                   [Controllers\fichefraisController::class, 'create']);
$router->post('/fichefrais/store',                                   [Controllers\fichefraisController::class, 'store']);
$router->get('#^/fichefrais/([a-zA-Z0-9_-]+)/([0-9]{6})$#',         [Controllers\fichefraisController::class, 'show']);
$router->get('#^/fichefrais/([a-zA-Z0-9_-]+)/([0-9]{6})/update$#',  [Controllers\fichefraisController::class, 'update']);
$router->post('#^/fichefrais/([a-zA-Z0-9_-]+)/([0-9]{6})/update$#', [Controllers\fichefraisController::class, 'save']);
$router->post('#^/fichefrais/([a-zA-Z0-9_-]+)/([0-9]{6})/delete$#', [Controllers\fichefraisController::class, 'delete']);
$router->post('#^/fichefrais/([a-zA-Z0-9_-]+)/([0-9]{6})/horsforfait/([0-9]+)/update$#',[Controllers\fichefraisController::class, 'updateFraisHorsForfait']);


// ===================== NORMALISATION DU PATH (IONOS) =====================
$uriPath    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$scriptDir  = rtrim(dirname($scriptName), '/');

$path = $uriPath;

// Enlève le sous-dossier éventuel
if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir)) ?: '/';
}

// Gère les URLs du type /index.php/etat (IONOS sans mod_rewrite)
if ($path === '/index.php') {
    $path = '/';
} elseif (str_starts_with($path, '/index.php/')) {
    $path = substr($path, strlen('/index.php')) ?: '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Trace de dispatch
@file_put_contents($logDir . '/trace.log', '[' . date('c') . "] DISPATCH $method $path\n", FILE_APPEND);

// ===================== DISPATCH =====================
try {
    $router->dispatch($method, $path);
    @file_put_contents($logDir . '/trace.log', '[' . date('c') . "] DISPATCH DONE\n", FILE_APPEND);
} catch (\Throwable $e) {
    @file_put_contents(
        $logDir . '/php-exception.log',
        '[' . date('c') . '] ' . get_class($e) . ': ' . $e->getMessage() .
        ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" .
        $e->getTraceAsString() . "\n\n",
        FILE_APPEND
    );
    http_response_code(500);
    echo 'Erreur interne.';
}