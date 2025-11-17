<?php
/**
 * Favorite Model
 *
 * Gère les opérations CRUD pour la table favorites
 *
 * @package App\Models
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Models;

use PDO;

class Favorite extends BaseModel
{
    /**
     * @var string Nom de la table
     */
    protected string $table = 'favorites';

    /**
     * Ajoute un build aux favoris
     *
     * @param int $userId L'ID de l'utilisateur
     * @param int $buildId L'ID du build
     * @return int|false L'ID du favori créé
     */
    public function addFavorite(int $userId, int $buildId): int|false
    {
        // Vérifier si déjà en favori
        if ($this->isFavorite($userId, $buildId)) {
            return false;
        }

        return $this->create([
            'user_id' => $userId,
            'build_id' => $buildId
        ]);
    }

    /**
     * Retire un build des favoris
     *
     * @param int $userId L'ID de l'utilisateur
     * @param int $buildId L'ID du build
     * @return bool
     */
    public function removeFavorite(int $userId, int $buildId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND build_id = :build_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':build_id', $buildId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Vérifie si un build est en favori pour un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @param int $buildId L'ID du build
     * @return bool
     */
    public function isFavorite(int $userId, int $buildId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE user_id = :user_id AND build_id = :build_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':build_id', $buildId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Récupère tous les favoris d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @param int $limit Limite de résultats
     * @param int $offset Offset
     * @return array
     */
    public function getUserFavorites(int $userId, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.weapon_type,
                       c.rarity,
                       c.icon_url as character_icon,
                       u.username as author,
                       u.avatar as author_avatar,
                       f.created_at as favorited_at
                FROM {$this->table} f
                JOIN builds b ON f.build_id = b.id
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE f.user_id = :user_id AND b.is_public = 1
                ORDER BY f.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre de favoris d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @return int
     */
    public function countUserFavorites(int $userId): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    /**
     * Compte le nombre de fois qu'un build a été mis en favori
     *
     * @param int $buildId L'ID du build
     * @return int
     */
    public function countBuildFavorites(int $buildId): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE build_id = :build_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':build_id', $buildId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    /**
     * Récupère les IDs des builds favoris d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @return array Tableau d'IDs
     */
    public function getUserFavoriteBuildIds(int $userId): array
    {
        $sql = "SELECT build_id FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'build_id');
    }

    /**
     * Toggle le statut favori (ajoute si absent, retire si présent)
     *
     * @param int $userId L'ID de l'utilisateur
     * @param int $buildId L'ID du build
     * @return array Résultat avec statut
     */
    public function toggleFavorite(int $userId, int $buildId): array
    {
        if ($this->isFavorite($userId, $buildId)) {
            $success = $this->removeFavorite($userId, $buildId);
            return [
                'success' => $success,
                'action' => 'removed',
                'is_favorite' => false
            ];
        } else {
            $id = $this->addFavorite($userId, $buildId);
            return [
                'success' => $id !== false,
                'action' => 'added',
                'is_favorite' => true,
                'id' => $id
            ];
        }
    }
}
