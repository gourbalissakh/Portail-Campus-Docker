# 🗄️ Service MySQL - Base de Données

## 📌 Utilité du Service MySQL

**MySQL** est un système de gestion de base de données relationnelle (SGBDR) qui permet de :
- Stocker des données de manière structurée et persistante
- Gérer les relations entre différentes tables
- Exécuter des requêtes SQL pour manipuler les données
- Assurer la cohérence et l'intégrité des données

### Pourquoi MySQL 8.0 ?

- ✅ **Performance** : Moteur InnoDB optimisé
- ✅ **Sécurité** : Authentification renforcée
- ✅ **Popularité** : Standard de l'industrie web
- ✅ **Compatibilité** : Fonctionne parfaitement avec PHP/PDO

---

## 🏗️ Architecture Base de Données

```
┌────────────────────────────────────────┐
│       Application PHP (Web)            │
│        172.20.0.20:80                  │
└───────────────┬────────────────────────┘
                │ PDO / MySQLi
                │ Requêtes SQL
                ▼
     ┌──────────────────────┐
     │   MySQL Server       │
     │   172.20.0.30:3306   │
     │   Container: db      │
     └──────────┬───────────┘
                │
                ▼
     ┌──────────────────────┐
     │  Volume Persistant   │
     │  db_data_portail     │
     │  (Stockage données)  │
     └──────────────────────┘
```

---

## 🐳 Configuration Docker

### Dans `docker-compose.yml`

```yaml
db_portail_campus:
  image: mysql:8.0                      # Image officielle MySQL 8.0
  container_name: db_portail_campus
  environment:
    MYSQL_ROOT_PASSWORD: rootpass       # Mot de passe root
    MYSQL_DATABASE: portail_campus_db   # Base créée automatiquement
    MYSQL_USER: campus_user             # Utilisateur applicatif
    MYSQL_PASSWORD: campus_pass         # Mot de passe utilisateur
  volumes:
    - db_data_portail_campus:/var/lib/mysql  # Persistance des données
  networks:
    portail-campus-network:
      ipv4_address: 172.20.0.30         # IP fixe
  # Port 3306 NON exposé (sécurité)

volumes:
  db_data_portail_campus:               # Volume Docker pour données
```

### Explication des Paramètres

| Variable | Valeur | Signification |
|----------|--------|---------------|
| `MYSQL_ROOT_PASSWORD` | rootpass | Mot de passe administrateur MySQL |
| `MYSQL_DATABASE` | portail_campus_db | Base créée au démarrage |
| `MYSQL_USER` | campus_user | Utilisateur avec droits sur la base |
| `MYSQL_PASSWORD` | campus_pass | Mot de passe de l'utilisateur |
| `ipv4_address` | 172.20.0.30 | IP fixe pour résolution DNS |

---

## 🔐 Utilisateurs et Permissions

### 1. Utilisateur Root (Administrateur)

```sql
Username: root
Password: rootpass
Host: localhost / %
Privileges: ALL PRIVILEGES
```

**Utilisation :** Administration complète, création de tables, gestion des utilisateurs.

**⚠️ Ne JAMAIS utiliser root dans l'application PHP !**

---

### 2. Utilisateur Applicatif (campus_user)

```sql
Username: campus_user
Password: campus_pass
Host: %
Database: portail_campus_db
Privileges: SELECT, INSERT, UPDATE, DELETE
```

**Utilisation :** Utilisé par l'application PHP pour les opérations CRUD.

---

## 📊 Structure de la Base de Données

### Schéma de Base

```sql
-- Base de données
portail_campus_db
    ├── etudiants          (Table principale)
    ├── admins             (Utilisateurs admin)
    ├── filieres           (Liste des filières - optionnel)
    └── logs_connexions    (Historique - optionnel)
```

### Table `etudiants`

```sql
CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    filiere VARCHAR(50),
    niveau ENUM('L1', 'L2', 'L3', 'M1', 'M2') DEFAULT 'L1',
    date_naissance DATE,
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nom (nom),
    INDEX idx_filiere (filiere),
    INDEX idx_niveau (niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Explication des colonnes :**

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT AUTO_INCREMENT | Identifiant unique (clé primaire) |
| `matricule` | VARCHAR(20) UNIQUE | Numéro étudiant (ex: L3GLAR2026001) |
| `nom`, `prenom` | VARCHAR(100) | Nom et prénom |
| `email` | VARCHAR(150) UNIQUE | Email unique |
| `filiere` | VARCHAR(50) | GLAR, Info, Math, etc. |
| `niveau` | ENUM | L1, L2, L3, M1, M2 |
| `created_at` | TIMESTAMP | Date de création automatique |
| `updated_at` | TIMESTAMP | Date de dernière modification |

---

### Table `admins`

```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**⚠️ Les mots de passe doivent être hashés avec `password_hash()` en PHP !**

---

## 📝 Exemples de Requêtes SQL

### 1. Insérer des données de test

```sql
-- Insérer des étudiants
INSERT INTO etudiants (matricule, nom, prenom, email, filiere, niveau, date_naissance) VALUES
('L3GLAR2026001', 'Diop', 'Amadou', 'amadou.diop@campus.sn', 'GLAR', 'L3', '2003-05-15'),
('L3GLAR2026002', 'Ndiaye', 'Fatou', 'fatou.ndiaye@campus.sn', 'GLAR', 'L3', '2002-11-20'),
('L3INFO2026001', 'Sow', 'Moussa', 'moussa.sow@campus.sn', 'Informatique', 'L3', '2003-08-10'),
('L2MATH2026001', 'Kane', 'Aissatou', 'aissatou.kane@campus.sn', 'Mathématiques', 'L2', '2004-03-25');

-- Créer un admin
INSERT INTO admins (username, password, nom, prenom, email) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'Admin', 'Système', 'admin@campus.sn');
```

---

### 2. Requêtes de sélection

```sql
-- Liste de tous les étudiants
SELECT * FROM etudiants ORDER BY nom ASC;

-- Rechercher par nom
SELECT * FROM etudiants WHERE nom LIKE '%Diop%';

-- Étudiants d'une filière spécifique
SELECT * FROM etudiants WHERE filiere = 'GLAR';

-- Compter les étudiants par niveau
SELECT niveau, COUNT(*) as total 
FROM etudiants 
GROUP BY niveau;

-- Pagination (20 résultats par page)
SELECT * FROM etudiants 
ORDER BY nom ASC 
LIMIT 20 OFFSET 0;  -- Page 1
```

---

### 3. Requêtes de mise à jour

```sql
-- Modifier un étudiant
UPDATE etudiants 
SET email = 'nouveau.email@campus.sn', 
    telephone = '771234567' 
WHERE id = 1;

-- Changer de niveau
UPDATE etudiants 
SET niveau = 'M1' 
WHERE niveau = 'L3' AND filiere = 'GLAR';
```

---

### 4. Requêtes de suppression

```sql
-- Supprimer un étudiant
DELETE FROM etudiants WHERE id = 5;

-- Supprimer les étudiants sans email
DELETE FROM etudiants WHERE email IS NULL;
```

---

## 🧪 Tests et Vérification

### Test 1 : Vérifier que le conteneur fonctionne

```powershell
docker ps | Select-String "db_portail"
```

**Résultat attendu :**
```
db_portail_campus   Up X minutes   3306/tcp, 33060/tcp
```

---

### Test 2 : Se connecter à MySQL en ligne de commande

```powershell
docker exec -it db_portail_campus mysql -u campus_user -p
# Mot de passe : campus_pass
```

Une fois connecté :
```sql
SHOW DATABASES;
USE portail_campus_db;
SHOW TABLES;
SELECT * FROM etudiants;
```

---

### Test 3 : Connexion root

```powershell
docker exec -it db_portail_campus mysql -u root -p
# Mot de passe : rootpass
```

---

### Test 4 : Exécuter une requête depuis l'extérieur

```powershell
docker exec db_portail_campus mysql -u campus_user -pcampus_pass -e "SELECT COUNT(*) as total FROM portail_campus_db.etudiants;"
```

---

### Test 5 : Voir les logs MySQL

```powershell
docker logs db_portail_campus
```

---

## 💾 Sauvegarde et Restauration

### Sauvegarder la base

```powershell
# Dump complet de la base
docker exec db_portail_campus mysqldump -u root -prootpass portail_campus_db > backup_$(Get-Date -Format "yyyyMMdd_HHmmss").sql
```

---

### Restaurer une sauvegarde

```powershell
# Restaurer depuis un fichier SQL
Get-Content backup_20260109.sql | docker exec -i db_portail_campus mysql -u root -prootpass portail_campus_db
```

---

### Exporter uniquement la structure

```powershell
docker exec db_portail_campus mysqldump -u root -prootpass --no-data portail_campus_db > structure.sql
```

---

### Exporter uniquement les données

```powershell
docker exec db_portail_campus mysqldump -u root -prootpass --no-create-info portail_campus_db > data.sql
```

---

## 🔗 Connexion depuis PHP (PDO)

### Exemple de connexion

```php
<?php
// Configuration
$host = 'db_portail_campus';  // ⚠️ Nom du service Docker, PAS localhost !
$dbname = 'portail_campus_db';
$username = 'campus_user';
$password = 'campus_pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
```

---

## 📊 Monitoring et Performance

### Voir les processus actifs

```sql
SHOW PROCESSLIST;
```

---

### Vérifier l'utilisation du stockage

```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
GROUP BY table_schema;
```

---

### Voir les tables et leur taille

```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'portail_campus_db'
ORDER BY (data_length + index_length) DESC;
```

---

## ⚠️ Troubleshooting

### Problème : "Access denied for user"

**Cause :** Mauvais identifiants ou permissions

**Solution :**
```powershell
# Vérifier les utilisateurs
docker exec -it db_portail_campus mysql -u root -prootpass -e "SELECT User, Host FROM mysql.user;"

# Recréer l'utilisateur si nécessaire
docker exec -it db_portail_campus mysql -u root -prootpass -e "
CREATE USER IF NOT EXISTS 'campus_user'@'%' IDENTIFIED BY 'campus_pass';
GRANT ALL PRIVILEGES ON portail_campus_db.* TO 'campus_user'@'%';
FLUSH PRIVILEGES;
"
```

---

### Problème : "Table doesn't exist"

**Cause :** Tables non créées

**Solution :**
```powershell
# Créer les tables via un script SQL
Get-Content init.sql | docker exec -i db_portail_campus mysql -u root -prootpass portail_campus_db
```

---

### Problème : Données perdues après redémarrage

**Cause :** Volume non configuré correctement

**Solution :** Vérifier le volume
```powershell
docker volume ls | Select-String "db_data"
docker volume inspect portail-campusdocker_db_data_portail_campus
```

---

### Problème : "Could not find driver" depuis PHP

**Cause :** Extension PDO MySQL manquante

**Solution :** Installer dans le Dockerfile du serveur web
```dockerfile
RUN docker-php-ext-install pdo pdo_mysql
```

---

## 🎯 Points Clés à Retenir

✅ MySQL écoute sur le port 3306 (interne au réseau Docker)
✅ Utilisez `db_portail_campus` comme host depuis PHP
✅ Les données sont persistées dans un volume Docker
✅ Ne jamais utiliser root dans l'application
✅ Toujours hasher les mots de passe (bcrypt/argon2)
✅ Sauvegarder régulièrement la base de données

---

## 📚 Ressources

- [Documentation MySQL 8.0](https://dev.mysql.com/doc/refman/8.0/en/)
- [PDO MySQL Driver](https://www.php.net/manual/fr/ref.pdo-mysql.php)
- [Docker MySQL Official](https://hub.docker.com/_/mysql)
- [SQL Tutorial](https://www.w3schools.com/sql/)
