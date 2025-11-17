<?php
/**
 * Base Controller Class
 *
 * Classe de base pour tous les controllers
 * Fournit les méthodes communes pour les réponses JSON
 *
 * @package App\Controllers
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Controllers;

class BaseController
{
    /**
     * Envoie une réponse JSON de succès
     *
     * @param mixed $data Les données à renvoyer
     * @param string $message Message optionnel
     * @param int $statusCode Code HTTP (200 par défaut)
     * @return void
     */
    protected function sendSuccess($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Envoie une réponse JSON d'erreur
     *
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP (400 par défaut)
     * @param array|null $errors Erreurs détaillées optionnelles
     * @return void
     */
    protected function sendError(string $message, int $statusCode = 400, ?array $errors = null): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Récupère le corps de la requête JSON
     *
     * @return array|null Les données décodées ou null
     */
    protected function getRequestBody(): ?array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    /**
     * Valide les champs requis
     *
     * @param array $data Les données à valider
     * @param array $required Les champs requis
     * @return array|null Tableau d'erreurs ou null si valide
     */
    protected function validateRequired(array $data, array $required): ?array
    {
        $errors = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Le champ {$field} est requis";
            }
        }

        return empty($errors) ? null : $errors;
    }

    /**
     * Valide un email
     *
     * @param string $email L'email à valider
     * @return bool
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Nettoie une chaîne (supprime les espaces, HTML, etc.)
     *
     * @param string $string La chaîne à nettoyer
     * @return string
     */
    protected function sanitize(string $string): string
    {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Récupère un paramètre GET
     *
     * @param string $key La clé du paramètre
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Récupère les paramètres de pagination
     *
     * @return array ['limit' => int, 'offset' => int, 'page' => int]
     */
    protected function getPaginationParams(): array
    {
        $page = max(1, (int)$this->getParam('page', 1));
        $limit = min(100, max(1, (int)$this->getParam('limit', 20)));
        $offset = ($page - 1) * $limit;

        return [
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page
        ];
    }

    /**
     * Gère les erreurs CORS
     *
     * @return void
     */
    protected function handleCors(): void
    {
        $allowedOrigins = explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost:5173');
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');

        // Réponse aux requêtes OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
