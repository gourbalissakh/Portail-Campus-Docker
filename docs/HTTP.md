# 🌐 Service HTTP - Apache + PHP 8.2

## 📌 Utilité du Service HTTP

Le serveur **HTTP (HyperText Transfer Protocol)** permet de :
- Héberger des sites web et applications
- Traiter des requêtes clients (GET, POST, etc.)
- Exécuter du code PHP côté serveur
- Servir des fichiers statiques (HTML, CSS, JS, images)

### Pourquoi Apache + PHP ?

- ✅ **Apache** : Serveur web le plus populaire, stable et éprouvé
- ✅ **PHP 8.2** : Langage de programmation côté serveur pour applications dynamiques
- ✅ **Intégration native** : Apache et PHP fonctionnent parfaitement ensemble
- ✅ **Communauté** : Énorme base de connaissances et modules disponibles

---

## 🏗️ Architecture Web

```
┌──────────────────────────────────────────────┐
│            Client (Navigateur)               │
│        http://localhost:8080/index.php       │
└──────────────────┬───────────────────────────┘
                   │ Requête HTTP GET
                   ▼
        ┌──────────────────────┐
        │   Apache (Port 80)   │
        │   Container: web     │
        │   IP: 172.20.0.20    │
        └──────────┬───────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │    Module PHP 8.2    │
        │  Traite index.php    │
        └──────────┬───────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │   MySQL (si besoin)  │
        │   172.20.0.30:3306   │
        └──────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │   Réponse HTML       │
        │   Retour au client   │
        └──────────────────────┘
```

---

## 🐳 Configuration Docker

### Dans `docker-compose.yml`

```yaml
serveur_web_php:
  image: php:8.2-apache              # Image officielle PHP avec Apache
  container_name: serveur_web_php
  ports:
    - "8080:80"                      # Mappe le port 80 du conteneur vers 8080 de l'hôte
  volumes:
    - ./Portail-campus-CRUD:/var/www/html   # Dossier de l'application
  build:
    context: .
    dockerfile: Dockerfile           # Pour installer des extensions PHP
  depends_on:
    - db_portail_campus              # Attend que MySQL soit prêt
  networks:
    portail-campus-network:
      ipv4_address: 172.20.0.20      # IP fixe
```

### Explication des Paramètres

| Paramètre | Valeur | Signification |
|-----------|--------|---------------|
| `image` | php:8.2-apache | Image Docker officielle avec Apache + PHP 8.2 |
| `ports` | 8080:80 | Port 80 du conteneur accessible via 8080 sur Windows |
| `volumes` | ./Portail-campus-CRUD:/var/www/html | Monte votre code PHP dans le conteneur |
| `depends_on` | db_portail_campus | Démarre après la base de données |
| `ipv4_address` | 172.20.0.20 | IP fixe pour la résolution DNS |

---

## 📁 Structure du Répertoire Web

```
Portail-campus-CRUD/
├── index.php                  # Page d'accueil
├── config/
│   └── database.php           # Configuration PDO
├── includes/
│   ├── header.php             # En-tête commun
│   ├── footer.php             # Pied de page
│   └── session.php            # Gestion sessions
├── pages/
│   ├── liste-etudiants.php    # Liste (READ)
│   ├── ajouter-etudiant.php   # Formulaire (CREATE)
│   ├── modifier-etudiant.php  # Formulaire (UPDATE)
│   └── supprimer-etudiant.php # Action (DELETE)
├── auth/
│   ├── login.php              # Page de connexion
│   └── logout.php             # Déconnexion
├── assets/
│   ├── css/
│   │   └── style.css          # Styles
│   └── js/
│       └── app.js             # Scripts JavaScript
└── .htaccess                  # Configuration Apache (optionnel)
```

---

## 🔧 Dockerfile Personnalisé

Pour installer des extensions PHP (comme MySQL PDO) :

**Fichier : `Dockerfile`**

```dockerfile
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer mod_rewrite (pour URL rewriting)
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html
```

### Extensions PHP Installées

| Extension | Utilité |
|-----------|---------|
| `pdo` | Interface PDO pour bases de données |
| `pdo_mysql` | Driver MySQL pour PDO |
| `mysqli` | Extension MySQL améliorée |

---

## 📝 Exemple de Code PHP

### 1. Configuration Base de Données

**Fichier : `config/database.php`**

```php
<?php
// ⚠️ IMPORTANT : Utiliser le nom du service Docker, PAS localhost !
$host = 'db_portail_campus';  // Nom du conteneur MySQL
$dbname = 'portail_campus_db';
$username = 'campus_user';
$password = 'campus_pass';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    // echo "Connexion réussie !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
```

### 2. Page d'Accueil Simple

**Fichier : `index.php`**

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>🎓 Portail Campus - GLAR L3</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="pages/liste-etudiants.php">Étudiants</a>
            <a href="auth/login.php">Connexion</a>
        </nav>
    </header>

    <main>
        <h2>Bienvenue sur le Portail Campus</h2>
        
        <?php
        // Tester la connexion à la base de données
        require_once 'config/database.php';
        
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM etudiants");
            $result = $stmt->fetch();
            echo "<p>✅ Base de données connectée !</p>";
            echo "<p>Nombre d'étudiants : " . $result['total'] . "</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Erreur : " . $e->getMessage() . "</p>";
        }
        
        // Afficher des informations système
        echo "<h3>Informations PHP</h3>";
        echo "<ul>";
        echo "<li>Version PHP : " . phpversion() . "</li>";
        echo "<li>Serveur : " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
        echo "<li>IP Serveur : " . $_SERVER['SERVER_ADDR'] . "</li>";
        echo "</ul>";
        ?>
    </main>

    <footer>
        <p>&copy; 2026 - Licence 3 GLAR - Virtualisation & Cloud</p>
    </footer>
</body>
</html>
```

---

## 🧪 Tests et Vérification

### Test 1 : Vérifier que le conteneur fonctionne

```powershell
docker ps | Select-String "serveur_web"
```

**Résultat attendu :**
```
serveur_web_php   Up X minutes   0.0.0.0:8080->80/tcp
```

---

### Test 2 : Accéder au serveur web

Ouvrir dans le navigateur : http://localhost:8080

**Résultat attendu :** La page d'accueil s'affiche

---

### Test 3 : Vérifier PHP

Créer un fichier `info.php` :

```php
<?php
phpinfo();
?>
```

Accéder à http://localhost:8080/info.php

**⚠️ À supprimer en production !**

---

### Test 4 : Tester la connexion MySQL

```powershell
docker exec serveur_web_php php -r "
\$pdo = new PDO('mysql:host=db_portail_campus', 'campus_user', 'campus_pass');
echo 'Connexion MySQL réussie !\n';
"
```

---

### Test 5 : Voir les logs Apache

```powershell
docker logs serveur_web_php
```

**Logs utiles :**
```
Apache/2.4.65 (Debian) PHP/8.2.30 configured
AH00163: Apache/2.4.65 configured -- resuming normal operations
```

---

## ⚙️ Configuration Apache

### Fichier `.htaccess` (optionnel)

Créer dans `/var/www/html/.htaccess` :

```apache
# Activer le moteur de réécriture
RewriteEngine On

# Forcer HTTPS (si configuré)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Rediriger vers index.php si le fichier n'existe pas
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Désactiver le listing des répertoires
Options -Indexes

# Bloquer l'accès aux fichiers sensibles
<FilesMatch "^(\.env|\.git|composer\.json)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 🔐 Bonnes Pratiques de Sécurité

### 1. Ne jamais exposer phpinfo() en production

```php
<?php
// Uniquement en développement
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    die('Accès refusé');
}
phpinfo();
?>
```

### 2. Valider les entrées utilisateur

```php
$nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
```

### 3. Utiliser des requêtes préparées (PDO)

```php
// ❌ MAUVAIS (Injection SQL possible)
$sql = "SELECT * FROM etudiants WHERE nom = '$nom'";

// ✅ BON (Requête préparée)
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE nom = :nom");
$stmt->execute(['nom' => $nom]);
```

### 4. Gérer les erreurs proprement

```php
// En production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Logger les erreurs
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php-errors.log');
```

---

## 📊 Performances

### Optimisations Apache

```apache
# Dans httpd.conf ou .htaccess

# Activer la compression GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache des fichiers statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## ⚠️ Troubleshooting

### Problème : "403 Forbidden"

**Cause :** Permissions incorrectes

**Solution :**
```powershell
docker exec serveur_web_php chown -R www-data:www-data /var/www/html
docker exec serveur_web_php chmod -R 755 /var/www/html
```

---

### Problème : "500 Internal Server Error"

**Cause :** Erreur PHP ou configuration Apache

**Solution :** Vérifier les logs
```powershell
docker logs serveur_web_php
docker exec serveur_web_php cat /var/log/apache2/error.log
```

---

### Problème : "Could not find driver" (PDO)

**Cause :** Extension PDO MySQL non installée

**Solution :** Rebuild l'image avec le Dockerfile
```powershell
docker-compose build serveur_web_php
docker-compose up -d
```

---

### Problème : Modifications non visibles

**Cause :** Cache du navigateur

**Solution :**
- Vider le cache navigateur (Ctrl + F5)
- Redémarrer le conteneur : `docker-compose restart serveur_web_php`

---

## 🎯 Points Clés à Retenir

✅ Apache écoute sur le port 80 (exposé sur 8080 de Windows)
✅ Le code PHP est dans `/var/www/html` (monté depuis le dossier local)
✅ Pour MySQL, utilisez `db_portail_campus` comme host, **PAS localhost**
✅ Les extensions PHP doivent être installées via Dockerfile
✅ L'IP fixe (172.20.0.20) permet la résolution DNS

---

## 📚 Ressources

- [Documentation Apache](https://httpd.apache.org/docs/)
- [PHP 8.2 Documentation](https://www.php.net/manual/fr/)
- [Docker PHP Official Images](https://hub.docker.com/_/php)
- [PDO Documentation](https://www.php.net/manual/fr/book.pdo.php)
