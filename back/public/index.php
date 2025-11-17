<?php
/**
 * Entry Point - Genshin Impact Build Manager API
 *
 * Point d'entrée principal de l'application
 * Configure l'autoloader PSR-4 et initialise le routeur
 *
 * @package Genshin Build Manager
 * @author DWWM Project
 * @version 1.0
 */

// =====================================================
// CONFIGURATION DES ERREURS
// =====================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// =====================================================
// AUTOLOADER PSR-4
// =====================================================
spl_autoload_register(function ($class) {
    // Namespace de base: App\
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    // Vérification du prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Récupération du nom de classe relatif
    $relative_class = substr($class, $len);

    // Conversion du namespace en chemin de fichier
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Inclusion du fichier s'il existe
    if (file_exists($file)) {
        require $file;
    }
});

// =====================================================
// CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// =====================================================
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// =====================================================
// GESTION DES HEADERS CORS
// =====================================================
$allowedOrigins = explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost:5173');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Réponse aux requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// =====================================================
// CRÉATION DU DOSSIER LOGS SI NÉCESSAIRE
// =====================================================
$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// =====================================================
// GESTION GLOBALE DES ERREURS
// =====================================================
set_exception_handler(function($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
    error_log("Stack trace: " . $exception->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne du serveur',
        'error' => ($_ENV['APP_DEBUG'] ?? false) ? $exception->getMessage() : null,
        'trace' => ($_ENV['APP_DEBUG'] ?? false) ? $exception->getTraceAsString() : null
    ]);
    exit;
});

// =====================================================
// CHARGEMENT DES ROUTES ET DÉMARRAGE DU ROUTEUR
// =====================================================
try {
    require_once __DIR__ . '/../app/Routes/api.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du chargement de l\'application',
        'error' => ($_ENV['APP_DEBUG'] ?? false) ? $e->getMessage() : null
    ]);
    exit;
}
