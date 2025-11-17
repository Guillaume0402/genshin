<?php
/**
 * API Router
 *
 * Gère le routage des requêtes API vers les contrôleurs appropriés
 *
 * @package App\Router
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Router;

class Router
{
    /**
     * @var array Routes enregistrées
     */
    private array $routes = [];

    /**
     * @var string URI de la requête
     */
    private string $requestUri;

    /**
     * @var string Méthode HTTP de la requête
     */
    private string $requestMethod;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Enregistre une route GET
     *
     * @param string $path Le chemin de la route
     * @param callable|array $callback Le callback à exécuter
     * @return void
     */
    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Enregistre une route POST
     *
     * @param string $path Le chemin de la route
     * @param callable|array $callback Le callback à exécuter
     * @return void
     */
    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Enregistre une route PUT
     *
     * @param string $path Le chemin de la route
     * @param callable|array $callback Le callback à exécuter
     * @return void
     */
    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * Enregistre une route DELETE
     *
     * @param string $path Le chemin de la route
     * @param callable|array $callback Le callback à exécuter
     * @return void
     */
    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Enregistre une route pour toutes les méthodes
     *
     * @param string $path Le chemin de la route
     * @param callable|array $callback Le callback à exécuter
     * @return void
     */
    public function any(string $path, $callback): void
    {
        $this->addRoute('ANY', $path, $callback);
    }

    /**
     * Ajoute une route au registre
     *
     * @param string $method La méthode HTTP
     * @param string $path Le chemin
     * @param callable|array $callback Le callback
     * @return void
     */
    private function addRoute(string $method, string $path, $callback): void
    {
        // Conversion des paramètres de route (ex: /api/users/:id) en regex
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    /**
     * Résout et exécute la route correspondante
     *
     * @return void
     */
    public function resolve(): void
    {
        foreach ($this->routes as $route) {
            // Vérification de la méthode HTTP
            if ($route['method'] !== 'ANY' && $route['method'] !== $this->requestMethod) {
                continue;
            }

            // Vérification du pattern de la route
            if (preg_match($route['pattern'], $this->requestUri, $matches)) {
                // Extraction des paramètres de la route
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Exécution du callback
                $this->executeCallback($route['callback'], $params);
                return;
            }
        }

        // Aucune route trouvée
        $this->sendNotFound();
    }

    /**
     * Exécute le callback de la route
     *
     * @param callable|array $callback Le callback
     * @param array $params Les paramètres extraits
     * @return void
     */
    private function executeCallback($callback, array $params): void
    {
        if (is_array($callback)) {
            // Format: [ControllerClass, 'method']
            [$controller, $method] = $callback;

            if (class_exists($controller)) {
                $instance = new $controller();

                if (method_exists($instance, $method)) {
                    // Appel avec les paramètres
                    call_user_func_array([$instance, $method], array_values($params));
                    return;
                }
            }
        } elseif (is_callable($callback)) {
            // Callback direct
            call_user_func_array($callback, array_values($params));
            return;
        }

        $this->sendNotFound();
    }

    /**
     * Envoie une réponse 404
     *
     * @return void
     */
    private function sendNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Route not found',
            'path' => $this->requestUri,
            'method' => $this->requestMethod
        ]);
        exit;
    }

    /**
     * Affiche toutes les routes enregistrées (debug)
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return array_map(function($route) {
            return [
                'method' => $route['method'],
                'path' => $route['path']
            ];
        }, $this->routes);
    }
}
