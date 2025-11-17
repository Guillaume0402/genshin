<?php
/**
 * API Routes
 *
 * Définition de toutes les routes de l'API REST
 *
 * @package App\Routes
 * @author DWWM Project
 * @version 1.0
 */

use App\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\BuildController;
use App\Controllers\CharacterController;
use App\Controllers\FavoriteController;

$router = new Router();

// =====================================================
// ROUTES D'AUTHENTIFICATION
// =====================================================
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->get('/api/auth/me', [AuthController::class, 'me']);
$router->put('/api/auth/profile', [AuthController::class, 'updateProfile']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);

// =====================================================
// ROUTES DES BUILDS
// =====================================================
$router->get('/api/builds', [BuildController::class, 'index']);
$router->get('/api/builds/my-builds', [BuildController::class, 'myBuilds']);
$router->get('/api/builds/top-rated', [BuildController::class, 'topRated']);
$router->get('/api/builds/recent', [BuildController::class, 'recent']);
$router->get('/api/builds/search', [BuildController::class, 'search']);
$router->get('/api/builds/:id', [BuildController::class, 'show']);
$router->post('/api/builds', [BuildController::class, 'create']);
$router->put('/api/builds/:id', [BuildController::class, 'update']);
$router->delete('/api/builds/:id', [BuildController::class, 'delete']);

// =====================================================
// ROUTES DES PERSONNAGES
// =====================================================
$router->get('/api/characters', [CharacterController::class, 'index']);
$router->get('/api/characters/popular', [CharacterController::class, 'popular']);
$router->get('/api/characters/search', [CharacterController::class, 'search']);
$router->get('/api/characters/element/:element', [CharacterController::class, 'byElement']);
$router->get('/api/characters/weapon/:weaponType', [CharacterController::class, 'byWeapon']);
$router->get('/api/characters/rarity/:rarity', [CharacterController::class, 'byRarity']);
$router->get('/api/characters/:id', [CharacterController::class, 'show']);
$router->get('/api/characters/:id/builds', [CharacterController::class, 'builds']);

// =====================================================
// ROUTES DES FAVORIS
// =====================================================
$router->get('/api/favorites', [FavoriteController::class, 'index']);
$router->get('/api/favorites/ids', [FavoriteController::class, 'getFavoriteIds']);
$router->get('/api/favorites/check/:buildId', [FavoriteController::class, 'check']);
$router->post('/api/favorites', [FavoriteController::class, 'add']);
$router->post('/api/favorites/toggle', [FavoriteController::class, 'toggle']);
$router->delete('/api/favorites/:buildId', [FavoriteController::class, 'remove']);

// =====================================================
// ROUTE DE TEST / HEALTH CHECK
// =====================================================
$router->get('/api/health', function() {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API is running',
        'version' => '1.0.0',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// =====================================================
// ROUTE DEBUG (liste toutes les routes)
// =====================================================
if ($_ENV['APP_DEBUG'] ?? false) {
    $router->get('/api/routes', function() use ($router) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'routes' => $router->getRoutes()
        ]);
    });
}

// Résolution de la route
$router->resolve();
