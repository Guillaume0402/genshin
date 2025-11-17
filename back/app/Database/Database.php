<?php
/**
 * Database Connection Class
 *
 * Gère la connexion à la base de données MySQL avec PDO
 * Utilise le pattern Singleton pour une instance unique
 *
 * @package App\Database
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    /**
     * Instance unique de la classe (Singleton)
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Connexion PDO
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Configuration de la base de données
     * @var array
     */
    private array $config;

    /**
     * Constructeur privé (Singleton)
     * Charge la configuration depuis .env
     */
    private function __construct()
    {
        $this->loadEnv();
        $this->config = [
            'host' => $_ENV['DB_HOST'] ?? 'db',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'database' => $_ENV['DB_DATABASE'] ?? 'genshin',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? 'rootpassword',
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        ];
    }

    /**
     * Charge les variables d'environnement depuis le fichier .env
     *
     * @return void
     */
    private function loadEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';

        if (!file_exists($envFile)) {
            // Si .env n'existe pas, on utilise les valeurs par défaut Docker
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignore les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse les lignes KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Supprime les guillemets si présents
                $value = trim($value, '"\'');

                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Retourne l'instance unique de Database (Singleton)
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Établit et retourne la connexion PDO
     *
     * @return PDO
     * @throws PDOException Si la connexion échoue
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['database'],
                    $this->config['charset']
                );

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->config['charset']
                ];

                $this->connection = new PDO(
                    $dsn,
                    $this->config['username'],
                    $this->config['password'],
                    $options
                );

            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new PDOException("Impossible de se connecter à la base de données: " . $e->getMessage());
            }
        }

        return $this->connection;
    }

    /**
     * Empêche le clonage de l'instance (Singleton)
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Empêche la désérialisation de l'instance (Singleton)
     *
     * @return void
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Ferme la connexion à la base de données
     *
     * @return void
     */
    public function closeConnection(): void
    {
        $this->connection = null;
    }

    /**
     * Test la connexion à la base de données
     *
     * @return bool True si la connexion est établie
     */
    public function testConnection(): bool
    {
        try {
            $this->getConnection();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
