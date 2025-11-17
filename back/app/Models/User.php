<?php
/**
 * User Model
 *
 * Gère les opérations CRUD pour la table users
 *
 * @package App\Models
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Models;

use PDO;

class User extends BaseModel
{
    /**
     * @var string Nom de la table
     */
    protected string $table = 'users';

    /**
     * Trouve un utilisateur par son email
     *
     * @param string $email L'email de l'utilisateur
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouve un utilisateur par son username
     *
     * @param string $username Le nom d'utilisateur
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Vérifie si un email existe déjà
     *
     * @param string $email L'email à vérifier
     * @param int|null $excludeId ID à exclure (pour les updates)
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);

        if ($excludeId !== null) {
            $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Vérifie si un username existe déjà
     *
     * @param string $username Le username à vérifier
     * @param int|null $excludeId ID à exclure (pour les updates)
     * @return bool
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);

        if ($excludeId !== null) {
            $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Crée un nouvel utilisateur avec mot de passe hashé
     *
     * @param array $data Données de l'utilisateur
     * @return int|false L'ID de l'utilisateur créé
     */
    public function createUser(array $data): int|false
    {
        // Hash du mot de passe
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->create($data);
    }

    /**
     * Met à jour un utilisateur (avec hash du password si fourni)
     *
     * @param int $id L'ID de l'utilisateur
     * @param array $data Données à mettre à jour
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        // Hash du mot de passe si présent
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->update($id, $data);
    }

    /**
     * Vérifie le mot de passe d'un utilisateur
     *
     * @param string $password Le mot de passe en clair
     * @param string $hashedPassword Le hash stocké en BDD
     * @return bool
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Récupère les builds d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @return array
     */
    public function getUserBuilds(int $userId): array
    {
        $sql = "SELECT b.*, c.name as character_name, c.element, c.icon_url
                FROM builds b
                JOIN characters c ON b.character_id = c.id
                WHERE b.user_id = :user_id
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère les favoris d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @return array
     */
    public function getUserFavorites(int $userId): array
    {
        $sql = "SELECT b.*, c.name as character_name, c.element, c.icon_url, u.username as author
                FROM favorites f
                JOIN builds b ON f.build_id = b.id
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE f.user_id = :user_id
                ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
