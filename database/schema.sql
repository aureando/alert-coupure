-- ============================================
-- Base de données : alert_coupure
-- Description : Gestion des coupures d'eau et d'électricité
-- Version : 1.0
-- ============================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS alert_coupure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alert_coupure;

-- ============================================
-- TABLE : ac_villes (VILLES)
-- Description : Liste des villes
-- ============================================
CREATE TABLE ac_villes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE COMMENT 'Code court (ex: TNR, TMM)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom),
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_quartiers (QUARTIER)
-- Description : Quartiers appartenant aux villes
-- ============================================
CREATE TABLE ac_quartiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville_id INT UNSIGNED NOT NULL,
    latitude DECIMAL(10, 8) NULL COMMENT 'Pour future carte interactive',
    longitude DECIMAL(11, 8) NULL COMMENT 'Pour future carte interactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ac_villes(id) ON DELETE CASCADE,
    INDEX idx_ville (ville_id),
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_users (UTILISATEURS)
-- Description : Utilisateurs du système
-- Roles : superadmin, admin, user
-- ============================================
CREATE TABLE ac_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash avec password_hash()',
    role ENUM('superadmin', 'admin', 'user') DEFAULT 'user',
    quartier_id INT UNSIGNED NULL COMMENT 'Quartier de résidence (pour users)',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Compte actif ou désactivé',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quartier_id) REFERENCES ac_quartiers(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_quartier (quartier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_coupures (COUPURES)
-- Description : Coupures planifiées par les admins
-- ============================================
CREATE TABLE ac_coupures (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ville_id INT UNSIGNED NOT NULL COMMENT 'Ville concernée (tous les quartiers)',
    type_service ENUM('electricite', 'eau', 'les_deux') NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif VARCHAR(100) NULL COMMENT 'Ex: maintenance, délestage, travaux',
    description TEXT NULL COMMENT 'Détails de la coupure',
    statut ENUM('planifie', 'en_cours', 'termine') DEFAULT 'planifie',
    created_by INT UNSIGNED NOT NULL COMMENT 'Admin qui a créé la coupure',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ac_villes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES ac_users(id) ON DELETE RESTRICT,
    INDEX idx_ville (ville_id),
    INDEX idx_type_service (type_service),
    INDEX idx_statut (statut),
    INDEX idx_date_debut (date_debut),
    INDEX idx_date_fin (date_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_signalements (SIGNALEMENTS)
-- Description : Signalements de pannes par les utilisateurs
-- ============================================
CREATE TABLE ac_signalements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'Utilisateur qui signale',
    quartier_id INT UNSIGNED NOT NULL COMMENT 'Quartier du problème',
    type_service ENUM('electricite', 'eau') NOT NULL,
    type_probleme ENUM(
        'panne',
        'poteau_casse',
        'cable_arrache',
        'transformateur_hs',
        'fuite_eau',
        'canalisation_cassee',
        'compteur_defectueux',
        'autre'
    ) NOT NULL,
    description TEXT NOT NULL COMMENT 'Description détaillée du problème',
    photo VARCHAR(255) NULL COMMENT 'Nom du fichier photo (optionnel)',
    statut ENUM('signale', 'en_traitement', 'resolu') DEFAULT 'signale',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES ac_users(id) ON DELETE CASCADE,
    FOREIGN KEY (quartier_id) REFERENCES ac_quartiers(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_quartier (quartier_id),
    INDEX idx_type_service (type_service),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VUES (Views) - Optionnel mais pratique
-- ============================================

-- Vue : Coupures avec nom de ville
CREATE OR REPLACE VIEW v_coupures_details AS
SELECT 
    c.*,
    v.nom AS ville_nom,
    v.code AS ville_code,
    CONCAT(u.prenom, ' ', u.nom) AS admin_nom
FROM ac_coupures c
JOIN ac_villes v ON c.ville_id = v.id
JOIN ac_users u ON c.created_by = u.id;

-- Vue : Signalements avec détails utilisateur et localisation
CREATE OR REPLACE VIEW v_signalements_details AS
SELECT 
    s.*,
    CONCAT(u.prenom, ' ', u.nom) AS user_nom,
    u.email AS user_email,
    q.nom AS quartier_nom,
    v.nom AS ville_nom,
    v.code AS ville_code
FROM ac_signalements s
JOIN ac_users u ON s.user_id = u.id
JOIN ac_quartiers q ON s.quartier_id = q.id
JOIN ac_villes v ON q.ville_id = v.id;

-- ============================================
-- FIN DU SCHEMA
-- ============================================