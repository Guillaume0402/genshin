<?php
/**
 * Character Model
 *
 * Gère les opérations CRUD pour la table characters
 *
 * @package App\Models
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Models;

use PDO;

class Character extends BaseModel
{
    /**
     * @var string Nom de la table
     */
    protected string $table = 'characters';

    /**
     * Récupère tous les personnages avec pagination
     *
     * @param int $limit Limite de résultats
     * @param int $offset Offset
     * @param array $filters Filtres optionnels (element, weapon_type, rarity)
     * @return array
     */
    public function getAllCharacters(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        // Filtres
        if (!empty($filters['element'])) {
            $sql .= " AND element = :element";
            $params[':element'] = $filters['element'];
        }

        if (!empty($filters['weapon_type'])) {
            $sql .= " AND weapon_type = :weapon_type";
            $params[':weapon_type'] = $filters['weapon_type'];
        }

        if (!empty($filters['rarity'])) {
            $sql .= " AND rarity = :rarity";
            $params[':rarity'] = $filters['rarity'];
        }

        if (!empty($filters['region'])) {
            $sql .= " AND region = :region";
            $params[':region'] = $filters['region'];
        }

        // Recherche par nom
        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";

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
     * Trouve un personnage par son nom
     *
     * @param string $name Le nom du personnage
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère les personnages par élément
     *
     * @param string $element L'élément (Pyro, Hydro, etc.)
     * @return array
     */
    public function getByElement(string $element): array
    {
        return $this->findWhere(['element' => $element]);
    }

    /**
     * Récupère les personnages par type d'arme
     *
     * @param string $weaponType Le type d'arme
     * @return array
     */
    public function getByWeaponType(string $weaponType): array
    {
        return $this->findWhere(['weapon_type' => $weaponType]);
    }

    /**
     * Récupère les personnages par rareté
     *
     * @param int $rarity La rareté (4 ou 5)
     * @return array
     */
    public function getByRarity(int $rarity): array
    {
        return $this->findWhere(['rarity' => $rarity]);
    }

    /**
     * Récupère les builds associés à un personnage
     *
     * @param int $characterId L'ID du personnage
     * @param bool $publicOnly Seulement les builds publics
     * @return array
     */
    public function getCharacterBuilds(int $characterId, bool $publicOnly = true): array
    {
        $sql = "SELECT b.*, u.username as author, u.avatar as author_avatar
                FROM builds b
                JOIN users u ON b.user_id = u.id
                WHERE b.character_id = :character_id";

        if ($publicOnly) {
            $sql .= " AND b.is_public = 1";
        }

        $sql .= " ORDER BY b.rating DESC, b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':character_id', $characterId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre de builds pour un personnage
     *
     * @param int $characterId L'ID du personnage
     * @return int
     */
    public function countBuilds(int $characterId): int
    {
        $sql = "SELECT COUNT(*) as total FROM builds WHERE character_id = :character_id AND is_public = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':character_id', $characterId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetch()['total'];
    }

    /**
     * Récupère les personnages les plus populaires (par nombre de builds)
     *
     * @param int $limit Nombre de résultats
     * @return array
     */
    public function getMostPopular(int $limit = 10): array
    {
        $sql = "SELECT c.*, COUNT(b.id) as builds_count
                FROM {$this->table} c
                LEFT JOIN builds b ON c.id = b.character_id AND b.is_public = 1
                GROUP BY c.id
                ORDER BY builds_count DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
