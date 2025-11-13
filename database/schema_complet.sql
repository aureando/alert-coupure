-- ============================================
-- Base de données complète V2 : Alert Coupure
-- Coupures par quartiers (pas par ville)
-- ============================================

-- Supprimer l'ancienne base si elle existe
DROP DATABASE IF EXISTS alert_coupure;
CREATE DATABASE alert_coupure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alert_coupure;

-- ============================================
-- TABLE : ac_villes
-- ============================================
CREATE TABLE ac_villes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom),
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_quartiers
-- ============================================
CREATE TABLE ac_quartiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville_id INT UNSIGNED NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ac_villes(id) ON DELETE CASCADE,
    INDEX idx_ville (ville_id),
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_users (avec ville_id pour admins)
-- ============================================
CREATE TABLE ac_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    quartier_id INT UNSIGNED NULL COMMENT 'Pour users',
    ville_id INT UNSIGNED NULL COMMENT 'Pour admins',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quartier_id) REFERENCES ac_quartiers(id) ON DELETE SET NULL,
    FOREIGN KEY (ville_id) REFERENCES ac_villes(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_quartier (quartier_id),
    INDEX idx_ville (ville_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_coupures (SANS ville_id)
-- ============================================
CREATE TABLE ac_coupures (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_service ENUM('electricite', 'eau', 'les_deux') NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif VARCHAR(100) NULL,
    description TEXT NULL,
    statut ENUM('planifie', 'en_cours', 'termine') DEFAULT 'planifie',
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES ac_users(id) ON DELETE RESTRICT,
    INDEX idx_type_service (type_service),
    INDEX idx_statut (statut),
    INDEX idx_date_debut (date_debut),
    INDEX idx_date_fin (date_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_coupures_quartiers (LIAISON)
-- ============================================
CREATE TABLE ac_coupures_quartiers (
    coupure_id INT UNSIGNED NOT NULL,
    quartier_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (coupure_id, quartier_id),
    FOREIGN KEY (coupure_id) REFERENCES ac_coupures(id) ON DELETE CASCADE,
    FOREIGN KEY (quartier_id) REFERENCES ac_quartiers(id) ON DELETE CASCADE,
    INDEX idx_coupure (coupure_id),
    INDEX idx_quartier (quartier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : ac_signalements
-- ============================================
CREATE TABLE ac_signalements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    quartier_id INT UNSIGNED NOT NULL,
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
    description TEXT NOT NULL,
    photo VARCHAR(255) NULL,
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
-- DONNÉES DE TEST
-- ============================================

-- Villes
INSERT INTO ac_villes (nom, code) VALUES
('Antananarivo', 'TNR'),
('Toamasina', 'TMM'),
('Antsirabe', 'ATB'),
('Mahajanga', 'MJN'),
('Fianarantsoa', 'FNR');

-- Quartiers Antananarivo
INSERT INTO ac_quartiers (nom, ville_id) VALUES
('Analakely', 1),
('Ankorondrano', 1),
('Ivandry', 1),
('67 Ha', 1),
('Behoririka', 1),
('Ambohijatovo', 1),
('Andohalo', 1),
('Ambatobe', 1);

-- Quartiers autres villes
INSERT INTO ac_quartiers (nom, ville_id) VALUES
('Tanambao', 2),
('Bazary Be', 2),
('Asabotsy', 3),
('Manandona', 3),
('Tsararano', 4),
('Mahabibo', 4),
('Kianja', 5);

-- Admin (ville Antananarivo)
INSERT INTO ac_users (nom, prenom, email, password, role, ville_id) VALUES
('Admin', 'Système', 'admin@alertcoupure.mg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Users
INSERT INTO ac_users (nom, prenom, email, password, role, quartier_id) VALUES
('Rakoto', 'Jean', 'jean.rakoto@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1),
('Rasoa', 'Marie', 'marie.rasoa@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 4);

-- Coupure exemple (impacte 3 quartiers)
INSERT INTO ac_coupures (type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
('electricite', '2025-11-20 08:00:00', '2025-11-20 17:00:00', 'Maintenance', 'Maintenance préventive du réseau', 'planifie', 1);

-- Lier la coupure aux quartiers (67 Ha, Analakely, Ivandry)
INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id) VALUES
(1, 1), -- Analakely
(1, 3), -- Ivandry
(1, 4); -- 67 Ha

-- Signalement
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, statut) VALUES
(2, 1, 'electricite', 'panne', 'Panne totale depuis ce matin', 'signale');

-- ============================================
-- INFOS DE CONNEXION
-- ============================================
SELECT 'Base de données créée avec succès !' as message;
SELECT 'Admin: admin@alertcoupure.mg / Admin@2024' as connexion;
SELECT 'Users: jean.rakoto@gmail.com / User@2024' as test_user;