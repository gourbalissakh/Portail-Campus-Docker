# 📝 Génie Logiciel - Application CRUD Étudiants

## 📌 Objectif du Projet GL

Développer une **application web complète** de gestion des étudiants avec les fonctionnalités suivantes :

✅ **CRUD** : Create, Read, Update, Delete
✅ **Pagination** : Affichage par pages (évite de charger tous les étudiants)
✅ **Recherche** : Filtrer par nom, prénom, filière, niveau
✅ **Authentification** : Système de login/logout pour admins
✅ **Sessions** : Gestion de l'état de connexion
✅ **Sécurité** : Protection contre injections SQL, XSS, CSRF

---

## 🏗️ Architecture de l'Application

```
┌──────────────────────────────────────────────┐
│           Navigateur (Client)                │
│        http://localhost:8080                 │
└──────────────────┬───────────────────────────┘
                   │ HTTP Requests
                   ▼
        ┌──────────────────────┐
        │   Apache + PHP 8.2   │
        │   Container: web     │
        │   172.20.0.20:80     │
        └──────────┬───────────┘
                   │ PDO/SQL
                   ▼
        ┌──────────────────────┐
        │   MySQL 8.0          │
        │   Container: db      │
        │   172.20.0.30:3306   │
        └──────────────────────┘
```

---

## 📁 Structure des Fichiers

```
Portail-campus-CRUD/
│
├── index.php                      # Page d'accueil
│
├── config/
│   └── database.php               # Connexion PDO
│
├── includes/
│   ├── header.php                 # En-tête HTML commun
│   ├── footer.php                 # Pied de page commun
│   ├── session.php                # Gestion des sessions
│   └── functions.php              # Fonctions utilitaires
│
├── auth/
│   ├── login.php                  # Page de connexion
│   ├── logout.php                 # Déconnexion
│   └── check-login.php            # Vérification authentification
│
├── students/
│   ├── list.php                   # Liste des étudiants (READ)
│   ├── create.php                 # Ajouter étudiant (CREATE)
│   ├── edit.php                   # Modifier étudiant (UPDATE)
│   ├── delete.php                 # Supprimer étudiant (DELETE)
│   └── view.php                   # Détails d'un étudiant
│
├── assets/
│   ├── css/
│   │   └── style.css              # Styles CSS
│   ├── js/
│   │   └── app.js                 # Scripts JavaScript
│   └── img/
│       └── logo.png               # Logo campus
│
└── sql/
    └── init.sql                   # Script d'initialisation BDD
```

---

## 🗄️ Base de Données

### Script d'Initialisation

**Fichier : `sql/init.sql`**

```sql
-- Créer la table étudiants
CREATE TABLE IF NOT EXISTS etudiants (
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

-- Créer la table admins
CREATE TABLE IF NOT EXISTS admins (
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

-- Insérer un admin par défaut (mot de passe: admin123)
INSERT INTO admins (username, password, nom, prenom, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Système', 'admin@campus.sn');

-- Insérer des étudiants de test
INSERT INTO etudiants (matricule, nom, prenom, email, telephone, filiere, niveau, date_naissance, adresse) VALUES
('L3GLAR2026001', 'Diop', 'Amadou', 'amadou.diop@campus.sn', '771234567', 'GLAR', 'L3', '2003-05-15', 'Dakar'),
('L3GLAR2026002', 'Ndiaye', 'Fatou', 'fatou.ndiaye@campus.sn', '771234568', 'GLAR', 'L3', '2002-11-20', 'Thiès'),
('L3INFO2026001', 'Sow', 'Moussa', 'moussa.sow@campus.sn', '771234569', 'Informatique', 'L3', '2003-08-10', 'Saint-Louis'),
('L2MATH2026001', 'Kane', 'Aissatou', 'aissatou.kane@campus.sn', '771234570', 'Mathématiques', 'L2', '2004-03-25', 'Ziguinchor'),
('M1GLAR2026001', 'Fall', 'Ibrahim', 'ibrahim.fall@campus.sn', '771234571', 'GLAR', 'M1', '2001-07-18', 'Kaolack');
```

**Pour exécuter :**
```powershell
Get-Content sql/init.sql | docker exec -i db_portail_campus mysql -u root -prootpass portail_campus_db
```

---

## 🔧 Fichiers de Configuration

### 1. Connexion Base de Données

**Fichier : `config/database.php`**

```php
<?php
/**
 * Configuration de la connexion à la base de données
 * Utilise PDO pour une meilleure sécurité
 */

// Paramètres de connexion
$host = 'db_portail_campus';  // ⚠️ Nom du conteneur Docker, PAS localhost
$dbname = 'portail_campus_db';
$username = 'campus_user';
$password = 'campus_pass';
$charset = 'utf8mb4';

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Exceptions pour les erreurs
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Tableaux associatifs par défaut
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Vraies requêtes préparées
];

// Connexion
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // En production, logger l'erreur au lieu de l'afficher
    error_log("Erreur de connexion BDD : " . $e->getMessage());
    die("Une erreur est survenue. Veuillez réessayer plus tard.");
}
?>
```

---

### 2. Gestion des Sessions

**Fichier : `includes/session.php`**

```php
<?php
/**
 * Gestion des sessions utilisateur
 */

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Rediriger vers login si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le nom d'utilisateur
 * @return string|null
 */
function getUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Définir un message flash
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupérer et supprimer le message flash
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
```

---

### 3. Fonctions Utilitaires

**Fichier : `includes/functions.php`**

```php
<?php
/**
 * Fonctions utilitaires
 */

/**
 * Échapper les données pour éviter XSS
 * @param string $data
 * @return string
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Générer un matricule étudiant
 * @param string $niveau
 * @param string $filiere
 * @param int $annee
 * @return string
 */
function generateMatricule($niveau, $filiere, $annee = null) {
    if ($annee === null) {
        $annee = date('Y');
    }
    
    // Format: L3GLAR2026001
    $filiereCode = strtoupper(substr($filiere, 0, 4));
    
    // Compter les étudiants existants cette année
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE matricule LIKE :pattern");
    $pattern = "$niveau$filiereCode$annee%";
    $stmt->execute(['pattern' => $pattern]);
    $count = $stmt->fetchColumn() + 1;
    
    return sprintf("%s%s%s%03d", $niveau, $filiereCode, $annee, $count);
}

/**
 * Valider une adresse email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Formater une date française
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Calculer l'âge à partir d'une date de naissance
 * @param string $birthdate
 * @return int
 */
function calculateAge($birthdate) {
    $birth = new DateTime($birthdate);
    $now = new DateTime();
    return $birth->diff($now)->y;
}

/**
 * Pagination - Calculer l'offset
 * @param int $page
 * @param int $perPage
 * @return int
 */
function getOffset($page, $perPage = 20) {
    return ($page - 1) * $perPage;
}

/**
 * Calculer le nombre total de pages
 * @param int $totalRecords
 * @param int $perPage
 * @return int
 */
function getTotalPages($totalRecords, $perPage = 20) {
    return ceil($totalRecords / $perPage);
}
?>
```

---

## 🔐 Authentification

### Page de Connexion

**Fichier : `auth/login.php`**

```php
<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header('Location: /students/list.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Vérifier les identifiants
        $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            
            // Mettre à jour la dernière connexion
            $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute(['id' => $admin['id']]);
            
            setFlashMessage('success', 'Connexion réussie !');
            header('Location: /students/list.php');
            exit;
        } else {
            $error = "Identifiants incorrects.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Portail Campus</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>🎓 Portail Campus</h1>
        <h2>Connexion Administration</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= escape($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        
        <p class="hint">Identifiants par défaut : admin / admin123</p>
    </div>
</body>
</html>
```

---

### Déconnexion

**Fichier : `auth/logout.php`**

```php
<?php
require_once '../includes/session.php';

// Détruire la session
session_destroy();

// Rediriger vers login
header('Location: /auth/login.php');
exit;
?>
```

---

## 📋 CRUD - Liste des Étudiants

**Fichier : `students/list.php`**

```php
<?php
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireLogin();  // Rediriger si non connecté

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = getOffset($page, $perPage);

// Recherche
$search = trim($_GET['search'] ?? '');
$filiere = $_GET['filiere'] ?? '';
$niveau = $_GET['niveau'] ?? '';

// Construire la requête SQL
$sql = "SELECT * FROM etudiants WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (nom LIKE :search OR prenom LIKE :search OR matricule LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($filiere)) {
    $sql .= " AND filiere = :filiere";
    $params['filiere'] = $filiere;
}

if (!empty($niveau)) {
    $sql .= " AND niveau = :niveau";
    $params['niveau'] = $niveau;
}

// Compter le total
$countStmt = $pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $sql));
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = getTotalPages($totalRecords, $perPage);

// Récupérer les étudiants
$sql .= " ORDER BY nom ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$etudiants = $stmt->fetchAll();

// Message flash
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Étudiants</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h1>📚 Liste des Étudiants</h1>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= escape($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Filtres et recherche -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= escape($search) ?>">
            
            <select name="filiere">
                <option value="">Toutes les filières</option>
                <option value="GLAR" <?= $filiere === 'GLAR' ? 'selected' : '' ?>>GLAR</option>
                <option value="Informatique" <?= $filiere === 'Informatique' ? 'selected' : '' ?>>Informatique</option>
                <option value="Mathématiques" <?= $filiere === 'Mathématiques' ? 'selected' : '' ?>>Mathématiques</option>
            </select>
            
            <select name="niveau">
                <option value="">Tous les niveaux</option>
                <option value="L1" <?= $niveau === 'L1' ? 'selected' : '' ?>>L1</option>
                <option value="L2" <?= $niveau === 'L2' ? 'selected' : '' ?>>L2</option>
                <option value="L3" <?= $niveau === 'L3' ? 'selected' : '' ?>>L3</option>
                <option value="M1" <?= $niveau === 'M1' ? 'selected' : '' ?>>M1</option>
                <option value="M2" <?= $niveau === 'M2' ? 'selected' : '' ?>>M2</option>
            </select>
            
            <button type="submit">Rechercher</button>
            <a href="list.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
        
        <div class="actions">
            <a href="create.php" class="btn btn-primary">➕ Ajouter un étudiant</a>
            <span><?= $totalRecords ?> étudiant(s) trouvé(s)</span>
        </div>
        
        <!-- Tableau des étudiants -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Filière</th>
                    <th>Niveau</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($etudiants)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Aucun étudiant trouvé.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <tr>
                            <td><?= escape($etudiant['matricule']) ?></td>
                            <td><?= escape($etudiant['nom']) ?></td>
                            <td><?= escape($etudiant['prenom']) ?></td>
                            <td><?= escape($etudiant['email']) ?></td>
                            <td><?= escape($etudiant['filiere']) ?></td>
                            <td><span class="badge"><?= escape($etudiant['niveau']) ?></span></td>
                            <td class="actions">
                                <a href="view.php?id=<?= $etudiant['id'] ?>" class="btn btn-sm btn-info">👁️</a>
                                <a href="edit.php?id=<?= $etudiant['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                <a href="delete.php?id=<?= $etudiant['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Confirmer la suppression ?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filiere=<?= urlencode($filiere) ?>&niveau=<?= urlencode($niveau) ?>">« Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filiere=<?= urlencode($filiere) ?>&niveau=<?= urlencode($niveau) ?>" 
                       class="<?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filiere=<?= urlencode($filiere) ?>&niveau=<?= urlencode($niveau) ?>">Suivant »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
```

---

## 🎯 Points Clés à Retenir

✅ **CRUD complet** : Créer, lire, modifier, supprimer des étudiants
✅ **Pagination** : 20 étudiants par page pour performance
✅ **Recherche multi-critères** : Nom, filière, niveau
✅ **Sécurité** : Requêtes préparées, échappement HTML, sessions
✅ **Messages flash** : Feedback utilisateur après actions
✅ **Responsive** : Compatible mobile/tablette/desktop

---

## 📚 Ressources

- [PHP PDO Documentation](https://www.php.net/manual/fr/book.pdo.php)
- [PHP Sessions](https://www.php.net/manual/fr/book.session.php)
- [SQL Injection Prevention](https://owasp.org/www-community/attacks/SQL_Injection)
- [XSS Prevention](https://owasp.org/www-community/attacks/xss/)
