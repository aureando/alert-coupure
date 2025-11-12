-- ============================================
-- MIGRATION : Coupures par quartiers
-- À exécuter dans phpMyAdmin
-- ============================================

USE alert_coupure;

-- 1. Créer la table de liaison
CREATE TABLE IF NOT EXISTS ac_coupures_quartiers (
    coupure_id INT UNSIGNED NOT NULL,
    quartier_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (coupure_id, quartier_id),
    FOREIGN KEY (coupure_id) REFERENCES ac_coupures(id) ON DELETE CASCADE,
    FOREIGN KEY (quartier_id) REFERENCES ac_quartiers(id) ON DELETE CASCADE,
    INDEX idx_coupure (coupure_id),
    INDEX idx_quartier (quartier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migrer les données existantes (coupures par ville → quartiers)
INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id)
SELECT c.id, q.id
FROM ac_coupures c
JOIN ac_quartiers q ON q.ville_id = c.ville_id;

-- 3. Supprimer la contrainte FK sur ville_id
ALTER TABLE ac_coupures DROP FOREIGN KEY ac_coupures_ibfk_1;

-- 4. Supprimer la colonne ville_id (plus besoin)
ALTER TABLE ac_coupures DROP COLUMN ville_id;

-- 5. Vérification
SELECT 'Migration terminée !' as message;
SELECT COUNT(*) as nb_liaisons FROM ac_coupures_quartiers;