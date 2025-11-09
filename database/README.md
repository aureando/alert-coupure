# 📊 Base de Données - Alert Coupure

## 🎯 Description

Base de données MySQL/MariaDB pour l'application de gestion des coupures d'eau et d'électricité.

---

## 📋 Prérequis

- **MySQL** 5.7+ ou **MariaDB** 10.3+
- **PHP** 8.0+ (pour PDO)
- **phpMyAdmin** (optionnel, pour interface graphique)
- **HeidiSQL** (optionnel, très pratique si vous utilisé Laragon)

---

## 🚀 Installation rapide

### **Méthode 1 : Via phpMyAdmin** (recommandé pour débutants)

1. Ouvrir **phpMyAdmin** : `http://localhost/phpmyadmin`

2. Créer la base de données :
   - Cliquer sur "Nouvelle base de données"
   - Nom : `alert_coupure`
   - Interclassement : `utf8mb4_unicode_ci`
   - Cliquer sur "Créer"

3. Importer le schéma :
   - Sélectionner la BDD `alert_coupure`
   - Onglet "Importer"
   - Choisir le fichier `schema.sql`
   - Cliquer sur "Exécuter"

4. Importer les données de test :
   - Toujours dans `alert_coupure`
   - Onglet "Importer"
   - Choisir le fichier `seed.sql`
   - Cliquer sur "Exécuter"

✅ **C'est fait !** La base est prête.

---

### **Méthode 2 : En ligne de commande** (pour les pros)

```bash
# Se connecter à MySQL
mysql -u root -p

# Créer et importer en une commande
mysql -u root -p < database/schema.sql
mysql -u root -p alert_coupure < database/seed.sql
```

**OU en une seule ligne :**

```bash
cat database/schema.sql database/seed.sql | mysql -u root -p
```

---

## 🗂️ Structure de la base

### **Tables principales :**

| Table | Description | Nombre de lignes (seed) |
|-------|-------------|------------------------|
| `ac_villes` | Villes de Madagascar | 7 |
| `ac_quartiers` | Quartiers par ville | ~30 |
| `ac_users` | Utilisateurs (admin + users) | 6 |
| `ac_coupures` | Coupures planifiées | 5 |
| `ac_signalements` | Signalements utilisateurs | 6 |

### **Relations :**

```
ac_villes (1) ──→ (*) ac_quartiers
    ↓
    └──→ (*) ac_coupures

ac_quartiers (1) ──→ (*) ac_users
             (1) ──→ (*) ac_signalements

ac_users (1) ──→ (*) ac_signalements
         (1) ──→ (*) ac_coupures (created_by)
```

---

## 🔑 Comptes par défaut

### **Admin (créé dans seed.sql) :**

```
📧 Email    : admin@alertcoupure.mg
🔑 Password : Admin@2025
👤 Rôle     : admin
```

### **Utilisateurs de test :**

Tous ont le même password : **`User@2025`**

| Email | Ville | Quartier |
|-------|-------|----------|
| jean.rakoto@gmail.com | Antananarivo | Analakely |
| marie.rasoa@gmail.com | Antananarivo | Ankorondrano |
| paul.randria@gmail.com | Antananarivo | Behoririka |
| sophie.raharison@gmail.com | Toamasina | Tanambao |
| marc.ravelo@gmail.com | Antsirabe | Asabotsy |

---

## ⚙️ Configuration de l'application

Après avoir installé la base, configure la connexion dans :

**`app/Config/config.php`**

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'alert_coupure');
define('DB_USER', 'root');
define('DB_PASS', ''); // Ton mot de passe MySQL
define('DB_CHARSET', 'utf8mb4');
```

---

## 🧪 Vérification de l'installation

### **Vérifier que toutes les tables existent :**

```sql
USE alert_coupure;
SHOW TABLES;
```

**Résultat attendu :**
```sql
+---------------------------+
| Tables_in_alert_coupure   |
+---------------------------+
| ac_coupures               |
| ac_quartiers              |
| ac_signalements           |
| ac_users                  |
| ac_villes                 |
+---------------------------+
```

### **Vérifier les données :**

```sql
-- Compter les villes
SELECT COUNT(*) FROM ac_villes; -- Devrait retourner 7

-- Compter les utilisateurs
SELECT COUNT(*) FROM ac_users; -- Devrait retourner 6

-- Voir l'admin
SELECT * FROM ac_users WHERE role = 'admin';
```

---

## 🔄 Réinitialiser la base (ATTENTION : supprime tout)

Si tu veux repartir de zéro :

```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS alert_coupure;"
mysql -u root -p < database/schema.sql
mysql -u root -p alert_coupure < database/seed.sql
```

**OU via phpMyAdmin :**
1. Sélectionner `alert_coupure`
2. Onglet "Opérations"
3. Cliquer sur "Supprimer la base de données"
4. Réimporter `schema.sql` puis `seed.sql`

---

## 📝 Ajouter de nouvelles données

### **Ajouter une ville :**

```sql
INSERT INTO ac_villes (nom, code) VALUES ('Morondava', 'MRV');
```

### **Ajouter un quartier :**

```sql
-- D'abord, trouver l'ID de la ville
SELECT id, nom FROM ac_villes WHERE nom = 'Antananarivo';

-- Puis insérer le quartier (remplace 1 par l'ID trouvé)
INSERT INTO ac_quartiers (nom, ville_id) VALUES ('Tsimbazaza', 1);
```

### **Créer un nouvel admin :**

```sql
-- Générer d'abord le hash du password avec PHP :
-- password_hash('TonMotDePasse', PASSWORD_DEFAULT)

INSERT INTO ac_users (nom, prenom, email, password, role) VALUES
('Nom', 'Prenom', 'email@example.com', '$2y$10$...', 'admin');
```

---

## 🛠️ Maintenance

### **Optimiser les tables :**

```sql
OPTIMIZE TABLE ac_villes, ac_quartiers, ac_users, ac_coupures, ac_signalements;
```

### **Sauvegarder la base :**

```bash
mysqldump -u root -p alert_coupure > backup_alert_coupure.sql
```

### **Restaurer depuis une sauvegarde :**

```bash
mysql -u root -p alert_coupure < backup_alert_coupure.sql
```

---

## 📊 Vues disponibles

Le schéma inclut 2 vues pratiques :

### **`v_coupures_details`**
Coupures avec infos ville et admin

```sql
SELECT * FROM v_coupures_details WHERE statut = 'planifie';
```

### **`v_signalements_details`**
Signalements avec infos user, quartier et ville

```sql
SELECT * FROM v_signalements_details WHERE statut = 'signale';
```

---

## ❓ Problèmes fréquents

### **Erreur : "Access denied for user 'root'@'localhost'"**
→ Vérifie le mot de passe MySQL dans la commande

### **Erreur : "Unknown database 'alert_coupure'"**
→ La base n'existe pas. Exécute d'abord `schema.sql`

### **Erreur : "Cannot add foreign key constraint"**
→ Les tables parents doivent être créées avant. Respecte l'ordre du `schema.sql`

### **Erreur avec les accents (caractères malgaches)**
→ Vérifie que l'interclassement est bien `utf8mb4_unicode_ci`

---

## 📞 Support

Pour toute question sur la base de données, consulte :
- **Documentation MySQL** : https://dev.mysql.com/doc/
- **Documentation MariaDB** : https://mariadb.com/kb/
