<?php
/**
 * JWT Authentication Middleware
 *
 * Gère l'authentification via JWT (JSON Web Token)
 * Vérifie et décode les tokens pour protéger les routes
 *
 * @package App\Middleware
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Middleware;

class Auth
{
    /**
     * Secret key pour signer les JWT (depuis .env)
     * @var string
     */
    private string $secretKey;

    /**
     * Algorithme de signature
     * @var string
     */
    private string $algorithm;

    /**
     * Durée de validité du token en secondes
     * @var int
     */
    private int $expiration;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'default-secret-key-change-this';
        $this->algorithm = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
        $this->expiration = (int)($_ENV['JWT_EXPIRATION'] ?? 86400); // 24h par défaut
    }

    /**
     * Génère un JWT pour un utilisateur
     *
     * @param array $userData Données de l'utilisateur (id, username, email)
     * @return string Le token JWT généré
     */
    public function generateToken(array $userData): string
    {
        $header = [
            'alg' => $this->algorithm,
            'typ' => 'JWT'
        ];

        $payload = [
            'user_id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'iat' => time(), // Issued at
            'exp' => time() + $this->expiration // Expiration
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = $this->generateSignature($headerEncoded . '.' . $payloadEncoded);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }

    /**
     * Vérifie et décode un JWT
     *
     * @param string $token Le token JWT à vérifier
     * @return array|null Les données du payload si valide, null sinon
     */
    public function verifyToken(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureProvided] = $parts;

        // Vérification de la signature
        $signatureExpected = $this->generateSignature($headerEncoded . '.' . $payloadEncoded);

        if (!hash_equals($signatureExpected, $signatureProvided)) {
            return null;
        }

        // Décodage du payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        if (!$payload) {
            return null;
        }

        // Vérification de l'expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Middleware principal : vérifie l'authentification
     *
     * @return array|null Les données utilisateur si authentifié, null sinon
     */
    public function authenticate(): ?array
    {
        // Récupération du token depuis les headers
        $token = $this->getTokenFromHeaders();

        if (!$token) {
            $this->sendUnauthorizedResponse('Token manquant');
            return null;
        }

        // Vérification du token
        $userData = $this->verifyToken($token);

        if (!$userData) {
            $this->sendUnauthorizedResponse('Token invalide ou expiré');
            return null;
        }

        return $userData;
    }

    /**
     * Récupère le token depuis les headers HTTP
     *
     * @return string|null Le token JWT ou null
     */
    private function getTokenFromHeaders(): ?string
    {
        $headers = getallheaders();

        // Support pour "Authorization: Bearer <token>"
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        // Support pour "authorization" (minuscules)
        if (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Génère la signature HMAC
     *
     * @param string $data Les données à signer
     * @return string La signature encodée en base64url
     */
    private function generateSignature(string $data): string
    {
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        return $this->base64UrlEncode($signature);
    }

    /**
     * Encode en base64url (compatible JWT)
     *
     * @param string $data Les données à encoder
     * @return string Les données encodées
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Décode depuis base64url
     *
     * @param string $data Les données à décoder
     * @return string Les données décodées
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Envoie une réponse 401 Unauthorized
     *
     * @param string $message Le message d'erreur
     * @return void
     */
    private function sendUnauthorizedResponse(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }

    /**
     * Vérifie si la requête actuelle est authentifiée
     *
     * @return bool True si authentifié
     */
    public function check(): bool
    {
        $token = $this->getTokenFromHeaders();

        if (!$token) {
            return false;
        }

        $userData = $this->verifyToken($token);
        return $userData !== null;
    }

    /**
     * Récupère l'utilisateur actuellement authentifié
     *
     * @return array|null Les données utilisateur ou null
     */
    public function user(): ?array
    {
        $token = $this->getTokenFromHeaders();

        if (!$token) {
            return null;
        }

        return $this->verifyToken($token);
    }
}
