-- =====================================================
-- GENSHIN IMPACT BUILD MANAGER - DATABASE SCHEMA
-- =====================================================
-- Description: Schéma MySQL complet pour la gestion des builds Genshin Impact
-- Version: 1.0
-- Author: DWWM Project
-- =====================================================

-- Suppression des tables existantes (pour réinitialisation)
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS builds;
DROP TABLE IF EXISTS characters;
DROP TABLE IF EXISTS users;

-- =====================================================
-- TABLE: users
-- =====================================================
-- Description: Stocke les utilisateurs de l'application
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt du mot de passe',
    avatar VARCHAR(255) DEFAULT NULL COMMENT 'URL de l\'avatar utilisateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: characters
-- =====================================================
-- Description: Stocke les personnages de Genshin Impact
-- =====================================================
CREATE TABLE characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    element ENUM('Pyro', 'Hydro', 'Anemo', 'Electro', 'Dendro', 'Cryo', 'Geo') NOT NULL,
    weapon_type ENUM('Sword', 'Claymore', 'Polearm', 'Bow', 'Catalyst') NOT NULL,
    rarity TINYINT NOT NULL CHECK (rarity IN (4, 5)) COMMENT '4 étoiles ou 5 étoiles',
    region VARCHAR(50) DEFAULT NULL COMMENT 'Région d\'origine (Mondstadt, Liyue, Inazuma, etc.)',
    icon_url VARCHAR(255) DEFAULT NULL COMMENT 'URL de l\'icône du personnage',
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_element (element),
    INDEX idx_weapon_type (weapon_type),
    INDEX idx_rarity (rarity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: builds
-- =====================================================
-- Description: Stocke les builds créés par les utilisateurs
-- =====================================================
CREATE TABLE builds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    character_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,

    -- Artefacts (Artifacts)
    artifact_set VARCHAR(100) DEFAULT NULL COMMENT 'Nom du set d\'artefacts',
    artifact_main_stats JSON DEFAULT NULL COMMENT 'Stats principales (Sablier, Coupe, Couronne)',
    artifact_sub_stats JSON DEFAULT NULL COMMENT 'Sous-stats recommandées',

    -- Arme (Weapon)
    weapon_name VARCHAR(100) DEFAULT NULL,
    weapon_refinement TINYINT DEFAULT 1 CHECK (weapon_refinement BETWEEN 1 AND 5),

    -- Talents
    talent_priority VARCHAR(50) DEFAULT NULL COMMENT 'Priorité des talents (ex: E > Q > A)',

    -- Team Composition
    team_composition JSON DEFAULT NULL COMMENT 'Équipe recommandée (array de character_ids)',

    -- Stats
    rating DECIMAL(3,2) DEFAULT 0.00 CHECK (rating BETWEEN 0 AND 5) COMMENT 'Note moyenne sur 5',
    views_count INT DEFAULT 0,
    favorites_count INT DEFAULT 0,

    -- Métadonnées
    is_public BOOLEAN DEFAULT true,
    tags JSON DEFAULT NULL COMMENT 'Tags pour la recherche (ex: ["DPS", "Support", "F2P"])',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_character_id (character_id),
    INDEX idx_is_public (is_public),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: favorites
-- =====================================================
-- Description: Système de favoris utilisateur pour les builds
-- =====================================================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    build_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, build_id) COMMENT 'Empêche les doublons',
    INDEX idx_user_id (user_id),
    INDEX idx_build_id (build_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES DE TEST
-- =====================================================

-- Insertion de personnages populaires
INSERT INTO characters (name, element, weapon_type, rarity, region, description) VALUES
('Hu Tao', 'Pyro', 'Polearm', 5, 'Liyue', '77th Director of the Wangsheng Funeral Parlor'),
('Ganyu', 'Cryo', 'Bow', 5, 'Liyue', 'Secretary at Yuehai Pavilion'),
('Raiden Shogun', 'Electro', 'Polearm', 5, 'Inazuma', 'Electro Archon and supreme ruler of Inazuma'),
('Kazuha', 'Anemo', 'Sword', 5, 'Inazuma', 'Wandering samurai of the once-famed Kaedehara Clan'),
('Zhongli', 'Geo', 'Polearm', 5, 'Liyue', 'Consultant of the Wangsheng Funeral Parlor'),
('Nahida', 'Dendro', 'Catalyst', 5, 'Sumeru', 'Dendro Archon and God of Wisdom'),
('Neuvillette', 'Hydro', 'Catalyst', 5, 'Fontaine', 'Chief Justice of Fontaine'),
('Bennett', 'Pyro', 'Sword', 4, 'Mondstadt', 'Member of the Adventurers Guild'),
('Xingqiu', 'Hydro', 'Sword', 4, 'Liyue', 'Second son of the Feiyun Commerce Guild'),
('Fischl', 'Electro', 'Bow', 4, 'Mondstadt', 'Investigator for the Adventurers Guild');

-- Insertion d'un utilisateur de test (mot de passe: "password123")
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@genshin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('testuser', 'test@genshin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertion de builds de test
INSERT INTO builds (user_id, character_id, title, description, artifact_set, weapon_name, weapon_refinement, talent_priority, is_public, tags) VALUES
(1, 1, 'Hu Tao Vaporize DPS', 'Build optimal pour maximiser les dégâts de Hu Tao avec réactions Vaporize', 'Crimson Witch of Flames', 'Staff of Homa', 1, 'E > Q > A', true, '["DPS", "Vaporize", "5-Star"]'),
(1, 2, 'Ganyu Freeze Support', 'Build freeze team avec Ganyu en DPS principal', 'Blizzard Strayer', 'Amos Bow', 1, 'A > Q > E', true, '["DPS", "Freeze", "F2P-Friendly"]'),
(2, 8, 'Bennett Full Support', 'Bennett optimisé pour le buff d\'attaque maximum', 'Noblesse Oblige', 'Aquila Favonia', 3, 'Q > E > A', true, '["Support", "Buffer", "Healer"]');

-- Insertion de favoris de test
INSERT INTO favorites (user_id, build_id) VALUES
(1, 3),
(2, 1),
(2, 2);

-- =====================================================
-- VÉRIFICATIONS
-- =====================================================

-- Compte des enregistrements
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'characters', COUNT(*) FROM characters
UNION ALL
SELECT 'builds', COUNT(*) FROM builds
UNION ALL
SELECT 'favorites', COUNT(*) FROM favorites;
