# Schéma de Base de Données - Genshin Build Manager

Ce document décrit la structure complète de la base de données MySQL.

---

## Vue d'ensemble

La base de données contient **4 tables principales** :

1. **users** - Utilisateurs de l'application
2. **characters** - Personnages de Genshin Impact
3. **builds** - Builds créés par les utilisateurs
4. **favorites** - Système de favoris

---

## Diagramme des relations

```
┌─────────────┐
│   users     │
└──────┬──────┘
       │
       │ 1:N
       │
┌──────▼──────────────┐      ┌──────────────────┐
│     builds          │ N:1  │   characters     │
└──────┬──────────────┘◄─────┴──────────────────┘
       │
       │ 1:N
       │
┌──────▼──────┐
│  favorites  │
└─────────────┘
```

---

## Table : `users`

Stocke les utilisateurs de l'application.

### Structure

| Colonne      | Type         | Contraintes                  | Description                    |
|-------------|--------------|------------------------------|--------------------------------|
| id          | INT          | PRIMARY KEY, AUTO_INCREMENT  | Identifiant unique             |
| username    | VARCHAR(50)  | UNIQUE, NOT NULL             | Nom d'utilisateur              |
| email       | VARCHAR(100) | UNIQUE, NOT NULL             | Email unique                   |
| password    | VARCHAR(255) | NOT NULL                     | Mot de passe hashé (bcrypt)    |
| avatar      | VARCHAR(255) | NULL                         | URL de l'avatar                |
| created_at  | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP    | Date de création               |
| updated_at  | TIMESTAMP    | ON UPDATE CURRENT_TIMESTAMP  | Date de mise à jour            |

### Index

- `idx_email` sur `email`
- `idx_username` sur `username`

### Exemple de données

```sql
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@genshin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

---

## Table : `characters`

Stocke les personnages de Genshin Impact.

### Structure

| Colonne      | Type         | Contraintes                  | Description                    |
|-------------|--------------|------------------------------|--------------------------------|
| id          | INT          | PRIMARY KEY, AUTO_INCREMENT  | Identifiant unique             |
| name        | VARCHAR(50)  | UNIQUE, NOT NULL             | Nom du personnage              |
| element     | ENUM         | NOT NULL                     | Pyro, Hydro, Anemo, Electro, Dendro, Cryo, Geo |
| weapon_type | ENUM         | NOT NULL                     | Sword, Claymore, Polearm, Bow, Catalyst |
| rarity      | TINYINT      | NOT NULL, CHECK (4 ou 5)     | Rareté (4★ ou 5★)             |
| region      | VARCHAR(50)  | NULL                         | Région d'origine               |
| icon_url    | VARCHAR(255) | NULL                         | URL de l'icône                 |
| description | TEXT         | NULL                         | Description du personnage      |
| created_at  | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP    | Date de création               |
| updated_at  | TIMESTAMP    | ON UPDATE CURRENT_TIMESTAMP  | Date de mise à jour            |

### Index

- `idx_element` sur `element`
- `idx_weapon_type` sur `weapon_type`
- `idx_rarity` sur `rarity`

### Exemple de données

```sql
INSERT INTO characters (name, element, weapon_type, rarity, region, description) VALUES
('Hu Tao', 'Pyro', 'Polearm', 5, 'Liyue', '77th Director of the Wangsheng Funeral Parlor'),
('Ganyu', 'Cryo', 'Bow', 5, 'Liyue', 'Secretary at Yuehai Pavilion'),
('Bennett', 'Pyro', 'Sword', 4, 'Mondstadt', 'Member of the Adventurers Guild');
```

---

## Table : `builds`

Stocke les builds créés par les utilisateurs.

### Structure

| Colonne              | Type         | Contraintes                  | Description                    |
|---------------------|--------------|------------------------------|--------------------------------|
| id                  | INT          | PRIMARY KEY, AUTO_INCREMENT  | Identifiant unique             |
| user_id             | INT          | FOREIGN KEY → users(id), NOT NULL | Créateur du build        |
| character_id        | INT          | FOREIGN KEY → characters(id), NOT NULL | Personnage du build  |
| title               | VARCHAR(100) | NOT NULL                     | Titre du build                 |
| description         | TEXT         | NULL                         | Description détaillée          |
| **ARTEFACTS**       |              |                              |                                |
| artifact_set        | VARCHAR(100) | NULL                         | Nom du set d'artefacts         |
| artifact_main_stats | JSON         | NULL                         | Stats principales (JSON)       |
| artifact_sub_stats  | JSON         | NULL                         | Sous-stats recommandées (JSON) |
| **ARME**            |              |                              |                                |
| weapon_name         | VARCHAR(100) | NULL                         | Nom de l'arme                  |
| weapon_refinement   | TINYINT      | DEFAULT 1, CHECK (1-5)       | Niveau de raffinement (1-5)    |
| **TALENTS**         |              |                              |                                |
| talent_priority     | VARCHAR(50)  | NULL                         | Priorité (ex: "E > Q > A")     |
| **ÉQUIPE**          |              |                              |                                |
| team_composition    | JSON         | NULL                         | Composition d'équipe (JSON)    |
| **STATS**           |              |                              |                                |
| rating              | DECIMAL(3,2) | DEFAULT 0.00, CHECK (0-5)    | Note moyenne sur 5             |
| views_count         | INT          | DEFAULT 0                    | Nombre de vues                 |
| favorites_count     | INT          | DEFAULT 0                    | Nombre de favoris              |
| **MÉTADONNÉES**     |              |                              |                                |
| is_public           | BOOLEAN      | DEFAULT true                 | Visibilité publique            |
| tags                | JSON         | NULL                         | Tags de recherche (JSON)       |
| created_at          | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP    | Date de création               |
| updated_at          | TIMESTAMP    | ON UPDATE CURRENT_TIMESTAMP  | Date de mise à jour            |

### Index

- `idx_user_id` sur `user_id`
- `idx_character_id` sur `character_id`
- `idx_is_public` sur `is_public`
- `idx_rating` sur `rating`
- `idx_created_at` sur `created_at`

### Contraintes de clés étrangères

```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
```

### Format JSON

**artifact_main_stats :**
```json
{
  "sands": "ATK%",
  "goblet": "Pyro DMG Bonus",
  "circlet": "CRIT Rate"
}
```

**artifact_sub_stats :**
```json
["CRIT Rate", "CRIT DMG", "ATK%", "Energy Recharge"]
```

**team_composition :**
```json
[5, 12, 23, 8]
```

**tags :**
```json
["DPS", "Vaporize", "F2P-Friendly", "5-Star"]
```

### Exemple de données

```sql
INSERT INTO builds (user_id, character_id, title, description, artifact_set, weapon_name, weapon_refinement, talent_priority, is_public, tags) VALUES
(1, 1, 'Hu Tao Vaporize DPS', 'Build optimal pour maximiser les dégâts de Hu Tao', 'Crimson Witch of Flames', 'Staff of Homa', 1, 'E > Q > A', true, '["DPS", "Vaporize", "5-Star"]');
```

---

## Table : `favorites`

Système de favoris utilisateur pour les builds.

### Structure

| Colonne    | Type      | Contraintes                  | Description                    |
|-----------|-----------|------------------------------|--------------------------------|
| id        | INT       | PRIMARY KEY, AUTO_INCREMENT  | Identifiant unique             |
| user_id   | INT       | FOREIGN KEY → users(id), NOT NULL | Utilisateur                |
| build_id  | INT       | FOREIGN KEY → builds(id), NOT NULL | Build favori              |
| created_at| TIMESTAMP | DEFAULT CURRENT_TIMESTAMP    | Date d'ajout aux favoris       |

### Index

- `idx_user_id` sur `user_id`
- `idx_build_id` sur `build_id`
- `unique_favorite` sur `(user_id, build_id)` (empêche les doublons)

### Contraintes de clés étrangères

```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE
```

### Exemple de données

```sql
INSERT INTO favorites (user_id, build_id) VALUES
(1, 3),
(2, 1),
(2, 2);
```

---

## Requêtes SQL utiles

### Récupérer tous les builds d'un utilisateur

```sql
SELECT b.*, c.name as character_name, c.element, c.icon_url
FROM builds b
JOIN characters c ON b.character_id = c.id
WHERE b.user_id = 1
ORDER BY b.created_at DESC;
```

### Récupérer les builds les mieux notés

```sql
SELECT b.*, c.name as character_name, u.username as author
FROM builds b
JOIN characters c ON b.character_id = c.id
JOIN users u ON b.user_id = u.id
WHERE b.is_public = 1 AND b.rating > 0
ORDER BY b.rating DESC, b.favorites_count DESC
LIMIT 10;
```

### Récupérer les favoris d'un utilisateur

```sql
SELECT b.*, c.name as character_name, u.username as author
FROM favorites f
JOIN builds b ON f.build_id = b.id
JOIN characters c ON b.character_id = c.id
JOIN users u ON b.user_id = u.id
WHERE f.user_id = 1
ORDER BY f.created_at DESC;
```

### Compter le nombre de builds par personnage

```sql
SELECT c.name, COUNT(b.id) as builds_count
FROM characters c
LEFT JOIN builds b ON c.id = b.character_id AND b.is_public = 1
GROUP BY c.id
ORDER BY builds_count DESC;
```

### Rechercher des builds

```sql
SELECT b.*, c.name as character_name
FROM builds b
JOIN characters c ON b.character_id = c.id
WHERE b.is_public = 1
AND (b.title LIKE '%vaporize%' OR b.description LIKE '%vaporize%' OR c.name LIKE '%vaporize%')
ORDER BY b.rating DESC;
```

---

## Triggers et événements

### Trigger : Mise à jour automatique du compteur de favoris

Ce trigger pourrait être ajouté pour mettre à jour automatiquement `builds.favorites_count` :

```sql
CREATE TRIGGER update_favorites_count_after_insert
AFTER INSERT ON favorites
FOR EACH ROW
BEGIN
    UPDATE builds
    SET favorites_count = (SELECT COUNT(*) FROM favorites WHERE build_id = NEW.build_id)
    WHERE id = NEW.build_id;
END;

CREATE TRIGGER update_favorites_count_after_delete
AFTER DELETE ON favorites
FOR EACH ROW
BEGIN
    UPDATE builds
    SET favorites_count = (SELECT COUNT(*) FROM favorites WHERE build_id = OLD.build_id)
    WHERE id = OLD.build_id;
END;
```

---

## Optimisations

### Index recommandés

Tous les index importants sont déjà créés dans le schéma. Pour de meilleures performances :

1. **Builds** : Index sur `is_public`, `rating`, `created_at` (déjà présents)
2. **Favorites** : Index unique sur `(user_id, build_id)` (déjà présent)
3. **Characters** : Index sur `element`, `weapon_type`, `rarity` (déjà présents)

### Statistiques

```sql
-- Nombre total d'utilisateurs
SELECT COUNT(*) FROM users;

-- Nombre total de builds publics
SELECT COUNT(*) FROM builds WHERE is_public = 1;

-- Nombre total de personnages
SELECT COUNT(*) FROM characters;

-- Build le plus populaire
SELECT * FROM builds ORDER BY favorites_count DESC LIMIT 1;
```

---

## Sauvegarde et restauration

### Sauvegarde

```bash
mysqldump -u root -p genshin > backup.sql
```

### Restauration

```bash
mysql -u root -p genshin < backup.sql
```

---

## Sécurité

1. **Mots de passe** : Toujours hashés avec `password_hash()` (bcrypt)
2. **SQL Injection** : Utilisation de requêtes préparées (PDO)
3. **Cascade Delete** : Suppression automatique des builds et favoris lors de la suppression d'un utilisateur
4. **Validation** : Les contraintes CHECK garantissent l'intégrité des données

---

## Évolutions futures possibles

- Ajout d'une table `comments` pour les commentaires sur les builds
- Ajout d'une table `ratings` pour les notes détaillées
- Ajout d'une table `notifications` pour les notifications utilisateur
- Ajout d'une table `follows` pour le système de suivi d'utilisateurs
- Historique des modifications avec une table `build_versions`

---

Pour toute question sur le schéma, consultez le fichier `back/database/schema.sql` qui contient le code SQL complet avec commentaires.
