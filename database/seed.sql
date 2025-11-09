-- ============================================
-- Fichier : seed.sql
-- Description : Données de test pour Alert Coupure
-- IMPORTANT : Exécuter APRÈS schema.sql
-- ============================================

USE alert_coupure;

-- ============================================
-- VILLES DE MADAGASCAR
-- ============================================
INSERT INTO ac_villes (nom, code) VALUES
('Antananarivo', 'TNR'),
('Toamasina', 'TMM'),
('Antsirabe', 'ATB'),
('Mahajanga', 'MJN'),
('Fianarantsoa', 'FNR'),
('Toliara', 'TLE'),
('Antsiranana', 'DIE');

-- ============================================
-- QUARTIERS PAR VILLE
-- ============================================

-- Antananarivo (TNR)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Analakely', 1, NULL, NULL),
('Ankorondrano', 1, NULL, NULL),
('Ivandry', 1, NULL, NULL),
('67 Ha', 1, NULL, NULL),
('Behoririka', 1, NULL, NULL),
('Ambohijatovo', 1, NULL, NULL),
('Andohalo', 1, NULL, NULL),
('Ambatobe', 1, NULL, NULL);

-- Toamasina (TMM)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Tanambao I', 2, NULL, NULL),
('Tanambao II', 2, NULL, NULL),
('Bazary Be', 2, NULL, NULL),
('Morafeno', 2, NULL, NULL),
('Anjoma', 2, NULL, NULL);
('Andranomadio', 2, NULL, NULL);
('Bazary Kely', 2, NULL, NULL);

-- Antsirabe (ATB)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Asabotsy', 3, NULL, NULL),
('Manandona', 3, NULL, NULL),
('Ambohitsara', 3, NULL, NULL);

-- Mahajanga (MJN)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Tsararano', 4, NULL, NULL),
('Mahabibo', 4, NULL, NULL),
('Abattoir', 4, NULL, NULL);

-- Fianarantsoa (FNR)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Tanambao', 5, NULL, NULL),
('Kianja', 5, NULL, NULL);

-- Toliara (TLE)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Tanambao', 6, NULL, NULL),
('Bazar Be', 6, NULL, NULL);

-- Antsiranana (DIE)
INSERT INTO ac_quartiers (nom, ville_id, latitude, longitude) VALUES
('Tanambao', 7, NULL, NULL),
('Lazaret', 7, NULL, NULL);

-- ============================================
-- UTILISATEURS
-- ============================================

-- Admin par défaut
-- Email: admin@alertcoupure.mg
-- Password: Admin@2025
-- Hash généré avec : password_hash('Admin@2025', PASSWORD_DEFAULT)
INSERT INTO ac_users (nom, prenom, email, password, role, quartier_id, is_active) VALUES
('Admin', 'Système', 'admin@alertcoupure.mg', '$2y$10$s8nbvziAZvlJ4lI21bsGPeK6kk2.WbtUPXN1NSbCUz1eGeoYUJJP2', 'admin', NULL, TRUE);

-- Utilisateurs de test
-- Password pour tous : User@2024
-- Hash : $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO ac_users (nom, prenom, email, password, role, quartier_id, is_active) VALUES
('RAKOTO', 'Jean', 'jean.rakoto@gmail.com', '$2y$10$96ddN6BFFGsPFl1PguQeIOhid4Nzfmqig2L0v41IoANLcjT.NcOE.', 'user', 1, TRUE),
('RASOA', 'Marie', 'marie.rasoa@gmail.com', '$2y$10$96ddN6BFFGsPFl1PguQeIOhid4Nzfmqig2L0v41IoANLcjT.NcOE.', 'user', 2, TRUE),
('RANDRIA', 'Paul', 'paul.randria@gmail.com', '$2y$10$96ddN6BFFGsPFl1PguQeIOhid4Nzfmqig2L0v41IoANLcjT.NcOE.', 'user', 5, TRUE),
('RAHARISON', 'Sophie', 'sophie.raharison@gmail.com', '$2y$10$96ddN6BFFGsPFl1PguQeIOhid4Nzfmqig2L0v41IoANLcjT.NcOE.', 'user', 9, TRUE),
('RAVELOMANATSOA', 'Marc', 'marc.ravelomanatsoa@gmail.com', '$2y$10$96ddN6BFFGsPFl1PguQeIOhid4Nzfmqig2L0v41IoANLcjT.NcOE.', 'user', 12, TRUE);

-- ============================================
-- COUPURES PLANIFIÉES (exemples)
-- ============================================

-- Coupure d'électricité à Antananarivo (passée)
INSERT INTO ac_coupures (ville_id, type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
(1, 'electricite', '2025-11-01 08:00:00', '2025-11-01 17:00:00', 'Maintenance', 'Maintenance préventive du réseau électrique', 'termine', 1);

-- Coupure d'eau à Toamasina (en cours)
INSERT INTO ac_coupures (ville_id, type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
(2, 'eau', '2025-11-09 06:00:00', '2025-11-09 18:00:00', 'Travaux', 'Réparation canalisation principale', 'en_cours', 1);

-- Coupure des deux services à Antsirabe (planifiée future)
INSERT INTO ac_coupures (ville_id, type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
(3, 'les_deux', '2025-11-15 09:00:00', '2025-11-15 16:00:00', 'Délestage', 'Délestage programmé suite à maintenance générale', 'planifie', 1);

-- Coupure d'électricité à Mahajanga (planifiée)
INSERT INTO ac_coupures (ville_id, type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
(4, 'electricite', '2025-11-12 10:00:00', '2025-11-12 15:00:00', 'Maintenance', 'Entretien transformateurs', 'planifie', 1);

-- Coupure d'eau à Fianarantsoa (planifiée)
INSERT INTO ac_coupures (ville_id, type_service, date_debut, date_fin, motif, description, statut, created_by) VALUES
(5, 'eau', '2025-11-14 07:00:00', '2025-11-14 19:00:00', 'Travaux', 'Extension du réseau de distribution', 'planifie', 1);

-- ============================================
-- SIGNALEMENTS (exemples)
-- ============================================

-- Signalement 1 : Panne électrique à Analakely
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(2, 1, 'electricite', 'panne', 'Panne totale depuis ce matin 6h. Tout le quartier est touché.', NULL, 'signale');

-- Signalement 2 : Poteau cassé à Ankorondrano
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(3, 2, 'electricite', 'poteau_casse', 'Poteau électrique endommagé près de l\'école primaire. Câbles pendent dangereusement.', NULL, 'en_traitement');

-- Signalement 3 : Fuite d'eau à Behoririka
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(2, 5, 'eau', 'fuite_eau', 'Grosse fuite d\'eau sur la route principale. L\'eau coule depuis 2 jours.', NULL, 'signale');

-- Signalement 4 : Transformateur HS à Tanambao Toamasina
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(4, 9, 'electricite', 'transformateur_hs', 'Le transformateur fait des étincelles et produit un bruit bizarre.', NULL, 'en_traitement');

-- Signalement 5 : Canalisation cassée à Asabotsy (résolu)
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(5, 12, 'eau', 'canalisation_cassee', 'Canalisation principale cassée suite aux travaux de voirie.', NULL, 'resolu');

-- Signalement 6 : Câble arraché à Ivandry
INSERT INTO ac_signalements (user_id, quartier_id, type_service, type_probleme, description, photo, statut) VALUES
(2, 3, 'electricite', 'cable_arrache', 'Câble électrique arraché par le vent hier soir. Danger public.', NULL, 'signale');

-- ============================================
-- INFORMATIONS DE CONNEXION
-- ============================================

-- ============================================
-- COMPTE ADMIN PAR DÉFAUT :
-- Email    : admin@alertcoupure.mg
-- Password : Admin@2025
-- ============================================

-- ============================================
-- COMPTES UTILISATEURS DE TEST :
-- Tous ont le password : User@2025
-- 
-- 1. jean.rakoto@gmail.com (Analakely, TNR)
-- 2. marie.rasoa@gmail.com (Ankorondrano, TNR)
-- 3. paul.randria@gmail.com (Behoririka, TNR)
-- 4. sophie.raharison@gmail.com (Tanambao, TMM)
-- 5. marc.ravelomanantsoa@gmail.com (Asabotsy, ATB)
-- ============================================

-- FIN DU SEED