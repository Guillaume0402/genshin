<?php
/**
 * Base Model Class
 *
 * Classe de base pour tous les models
 * Fournit les méthodes CRUD communes
 *
 * @package App\Models
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Models;

use App\Database\Database;
use PDO;

abstract class BaseModel
{
    /**
     * Connexion PDO
     * @var PDO
     */
    protected PDO $db;

    /**
     * Nom de la table
     * @var string
     */
    protected string $table;

    /**
     * Clé primaire
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère tous les enregistrements
     *
     * @param int $limit Limite de résultats
     * @param int $offset Offset pour la pagination
     * @return array
     */
    public function findAll(int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement par son ID
     *
     * @param int $id L'ID de l'enregistrement
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère des enregistrements selon une condition
     *
     * @param array $conditions Tableau associatif [colonne => valeur]
     * @return array
     */
    public function findWhere(array $conditions): array
    {
        $where = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Insère un nouvel enregistrement
     *
     * @param array $data Données à insérer
     * @return int|false L'ID de l'enregistrement créé ou false
     */
    public function create(array $data): int|false
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Met à jour un enregistrement
     *
     * @param int $id L'ID de l'enregistrement
     * @param array $data Données à mettre à jour
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        $params[':id'] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) .
               " WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprime un enregistrement
     *
     * @param int $id L'ID de l'enregistrement
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Compte le nombre d'enregistrements
     *
     * @param array $conditions Conditions optionnelles
     * @return int
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            $params = [];

            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = :{$column}";
                $params[":{$column}"] = $value;
            }

            $sql .= " WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->db->query($sql);
        }

        return (int)$stmt->fetch()['total'];
    }
}
