<?php
/**
 * Build Model
 *
 * Gère les opérations CRUD pour la table builds
 *
 * @package App\Models
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Models;

use PDO;

class Build extends BaseModel
{
    /**
     * @var string Nom de la table
     */
    protected string $table = 'builds';

    /**
     * Récupère tous les builds avec informations associées
     *
     * @param int $limit Limite de résultats
     * @param int $offset Offset
     * @param array $filters Filtres optionnels
     * @return array
     */
    public function getAllBuilds(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.weapon_type,
                       c.rarity,
                       c.icon_url as character_icon,
                       u.username as author,
                       u.avatar as author_avatar
                FROM {$this->table} b
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE b.is_public = 1";

        $params = [];

        // Filtres
        if (!empty($filters['character_id'])) {
            $sql .= " AND b.character_id = :character_id";
            $params[':character_id'] = $filters['character_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND b.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['element'])) {
            $sql .= " AND c.element = :element";
            $params[':element'] = $filters['element'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (b.title LIKE :search OR b.description LIKE :search OR c.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        // Tri
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $allowedOrders = ['created_at', 'rating', 'views_count', 'favorites_count'];

        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'created_at';
        }

        if (!in_array(strtoupper($orderDir), ['ASC', 'DESC'])) {
            $orderDir = 'DESC';
        }

        $sql .= " ORDER BY b.{$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère un build par son ID avec toutes les informations
     *
     * @param int $id L'ID du build
     * @return array|null
     */
    public function getBuildDetails(int $id): ?array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.weapon_type,
                       c.rarity,
                       c.region,
                       c.icon_url as character_icon,
                       c.description as character_description,
                       u.username as author,
                       u.email as author_email,
                       u.avatar as author_avatar
                FROM {$this->table} b
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE b.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Incrémente le compteur de vues
     *
     * @param int $id L'ID du build
     * @return bool
     */
    public function incrementViews(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET views_count = views_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Met à jour le compteur de favoris
     *
     * @param int $id L'ID du build
     * @return bool
     */
    public function updateFavoritesCount(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET favorites_count = (
                    SELECT COUNT(*) FROM favorites WHERE build_id = :id
                )
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Vérifie si un utilisateur est propriétaire du build
     *
     * @param int $buildId L'ID du build
     * @param int $userId L'ID de l'utilisateur
     * @return bool
     */
    public function isOwner(int $buildId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id = :build_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':build_id', $buildId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Récupère les builds d'un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @param bool $includePrivate Inclure les builds privés
     * @return array
     */
    public function getUserBuilds(int $userId, bool $includePrivate = true): array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.icon_url as character_icon
                FROM {$this->table} b
                JOIN characters c ON b.character_id = c.id
                WHERE b.user_id = :user_id";

        if (!$includePrivate) {
            $sql .= " AND b.is_public = 1";
        }

        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Recherche de builds
     *
     * @param string $query Terme de recherche
     * @param int $limit Limite de résultats
     * @return array
     */
    public function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.icon_url as character_icon,
                       u.username as author
                FROM {$this->table} b
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE b.is_public = 1
                AND (b.title LIKE :query OR b.description LIKE :query OR c.name LIKE :query)
                ORDER BY b.rating DESC, b.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère les builds les mieux notés
     *
     * @param int $limit Nombre de résultats
     * @return array
     */
    public function getTopRated(int $limit = 10): array
    {
        $sql = "SELECT b.*,
                       c.name as character_name,
                       c.element,
                       c.icon_url as character_icon,
                       u.username as author
                FROM {$this->table} b
                JOIN characters c ON b.character_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE b.is_public = 1 AND b.rating > 0
                ORDER BY b.rating DESC, b.favorites_count DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère les builds les plus récents
     *
     * @param int $limit Nombre de résultats
     * @return array
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->getAllBuilds($limit, 0, ['order_by' => 'created_at', 'order_dir' => 'DESC']);
    }
}
