# 📘 RAPPORT COMPLET - Projet Portail Campus

## 🎓 Infrastructure Dockerisée pour un Campus Universitaire

**Auteur :** Étudiant L3 GLAR  
**Date :** 10 Janvier 2026  
**Version :** 1.0  
**Technologies :** Docker, PHP 8.2, MySQL 8.0, BIND9, DHCP, Samba

---

## 📑 Table des Matières

1. [Introduction](#1-introduction)
2. [Objectifs du Projet](#2-objectifs-du-projet)
3. [Architecture Globale](#3-architecture-globale)
4. [Services Déployés](#4-services-déployés)
5. [Application CRUD - Génie Logiciel](#5-application-crud---génie-logiciel)
6. [Administration Réseau - Tests](#6-administration-réseau---tests)
7. [Problèmes Rencontrés et Solutions](#7-problèmes-rencontrés-et-solutions)
8. [Guide d'Installation](#8-guide-dinstallation)
9. [Guide d'Utilisation](#9-guide-dutilisation)
10. [Conclusion](#10-conclusion)

---

## 1. Introduction

### 1.1 Contexte

Ce projet s'inscrit dans le cadre de la formation **Licence 3 GLAR (Génie Logiciel et Administration Réseau)** au Sénégal. Il vise à mettre en pratique les compétences acquises en :

- **Virtualisation et Cloud** : Utilisation de Docker pour conteneuriser des services
- **Administration Réseau** : Configuration DNS, DHCP, Samba
- **Génie Logiciel** : Développement d'une application CRUD complète

### 1.2 Présentation Générale

Le **Portail Campus** est une infrastructure complète simulant un environnement réseau universitaire. Il permet de :

- Gérer les étudiants (CRUD)
- Distribuer automatiquement des adresses IP (DHCP)
- Résoudre des noms de domaine internes (DNS)
- Partager des fichiers (Samba/SMB)
- Héberger une application web (Apache/PHP/MySQL)

---

## 2. Objectifs du Projet

### 2.1 Exigences du Cahier des Charges

| Domaine | Exigences | Statut |
|---------|-----------|--------|
| **Services Communs** | DNS, DHCP, HTTP, Base de données, SMB | ✅ Complet |
| **Génie Logiciel (GL)** | CRUD étudiants, Pagination, Recherche, Sessions | ✅ Complet |
| **Administration Réseau (AR)** | DHCP avec options DNS, Résolution interne, Tests IP | ✅ Complet |

### 2.2 Objectifs Pédagogiques

- Comprendre l'orchestration de conteneurs avec Docker Compose
- Configurer des services réseau (DNS BIND9, DHCP)
- Développer une application web sécurisée avec PHP/PDO
- Documenter et tester une infrastructure complète

---

## 3. Architecture Globale

### 3.1 Schéma de l'Infrastructure

```
┌─────────────────────────────────────────────────────────────────────┐
│                        MACHINE HÔTE (Windows)                        │
│                                                                      │
│   ┌──────────────────────────────────────────────────────────────┐  │
│   │                    Docker Desktop                             │  │
│   │                                                               │  │
│   │   ┌─────────────────────────────────────────────────────┐    │  │
│   │   │           Réseau Docker Bridge                       │    │  │
│   │   │           172.20.0.0/16                              │    │  │
│   │   │                                                      │    │  │
│   │   │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐    │    │  │
│   │   │  │   DNS   │ │  DHCP   │ │   Web   │ │  MySQL  │    │    │  │
│   │   │  │ .0.10   │ │ Dynamic │ │ .0.20   │ │ .0.30   │    │    │  │
│   │   │  │ :53     │ │         │ │ :8080   │ │ :3306   │    │    │  │
│   │   │  └─────────┘ └─────────┘ └─────────┘ └─────────┘    │    │  │
│   │   │                                                      │    │  │
│   │   │  ┌─────────┐ ┌─────────┐                            │    │  │
│   │   │  │  Samba  │ │phpMyAdmin│                           │    │  │
│   │   │  │ .0.40   │ │ Dynamic │                            │    │  │
│   │   │  │ :1445   │ │ :8081   │                            │    │  │
│   │   │  └─────────┘ └─────────┘                            │    │  │
│   │   │                                                      │    │  │
│   │   └─────────────────────────────────────────────────────┘    │  │
│   │                                                               │  │
│   └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│   Ports exposés : 53 (DNS), 8080 (Web), 8081 (phpMyAdmin),          │
│                   1139/1445 (Samba)                                  │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.2 Plan d'Adressage IP

| Service | Conteneur | IP Fixe | Port(s) Exposé(s) |
|---------|-----------|---------|-------------------|
| DNS (BIND9) | `dns_campus` | 172.20.0.10 | 53/tcp, 53/udp |
| Serveur Web | `serveur_web_php` | 172.20.0.20 | 8080 |
| MySQL | `db_portail_campus` | 172.20.0.30 | - (interne) |
| Samba | `samba_campus` | 172.20.0.40 | 1139, 1445 |
| DHCP | `dhcp_campus` | Dynamique | - |
| phpMyAdmin | `phpmyadmin_portail_campus` | Dynamique | 8081 |

### 3.3 Plage DHCP

- **Début** : 172.20.0.100
- **Fin** : 172.20.0.200
- **Capacité** : 101 adresses disponibles
- **Durée de bail** : 10 minutes (défaut), 2 heures (max)

---

## 4. Services Déployés

### 4.1 Service DNS (BIND9)

#### Rôle
Le serveur DNS permet la résolution de noms de domaine internes pour la zone `portail.campus`.

#### Configuration (`dns/named.conf`)
```conf
options {
    directory "/var/cache/bind";
    allow-query { any; };
    recursion yes;
    forwarders {
        8.8.8.8;    # Google DNS
        1.1.1.1;    # Cloudflare DNS
    };
};

zone "portail.campus" {
    type master;
    file "/etc/bind/db.portail.campus";
};
```

#### Enregistrements DNS (`dns/db.portail.campus`)
| Nom | Type | Valeur |
|-----|------|--------|
| dns.portail.campus | A | 172.20.0.10 |
| web.portail.campus | A | 172.20.0.20 |
| db.portail.campus | A | 172.20.0.30 |
| samba.portail.campus | A | 172.20.0.40 |

#### Pourquoi les Forwarders ?
Sans forwarders, le DNS ne pourrait résoudre que les noms de la zone `portail.campus`. Les forwarders permettent de résoudre les domaines externes (google.com, github.com, etc.).

---

### 4.2 Service DHCP

#### Rôle
Distribution automatique de configurations réseau aux machines se connectant au réseau.

#### Configuration (`dhcp/dhcpd.conf`)
```conf
default-lease-time 600;      # 10 minutes
max-lease-time 7200;         # 2 heures

subnet 172.20.0.0 netmask 255.255.0.0 {
  range 172.20.0.100 172.20.0.200;
  option routers 172.20.0.1;
  option domain-name-servers 172.20.0.10;
}
```

#### Options distribuées
- **Adresse IP** : Dans la plage 172.20.0.100-200
- **Passerelle** : 172.20.0.1 (gateway Docker)
- **DNS** : 172.20.0.10 (notre serveur DNS)

#### Processus DHCP (4 étapes)
1. **DISCOVER** : Le client diffuse "Y a-t-il un serveur DHCP ?"
2. **OFFER** : Le serveur propose une IP
3. **REQUEST** : Le client accepte l'IP proposée
4. **ACK** : Le serveur confirme l'attribution

---

### 4.3 Service HTTP (Apache + PHP)

#### Rôle
Héberger l'application web CRUD du portail campus.

#### Configuration Docker
```yaml
serveur_web_php:
  build:
    context: .
    dockerfile: Dockerfile
  ports:
    - "8080:80"
  volumes:
    - ./Portail-campus-CRUD:/var/www/html
```

#### Dockerfile
```dockerfile
FROM php:8.2-apache
RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
EXPOSE 80
```

#### Extensions PHP installées
- `mysqli` : Connexion MySQL procédurale
- `pdo` : PHP Data Objects (abstraction)
- `pdo_mysql` : Driver PDO pour MySQL

---

### 4.4 Service MySQL

#### Rôle
Stocker les données de l'application (étudiants, administrateurs).

#### Configuration
```yaml
db_portail_campus:
  image: mysql:8.0
  environment:
    MYSQL_ROOT_PASSWORD: rootpass
    MYSQL_DATABASE: portail_campus_db
    MYSQL_USER: campus_user
    MYSQL_PASSWORD: campus_pass
```

#### Schéma de la Base de Données

**Table `etudiants`**
| Colonne | Type | Description |
|---------|------|-------------|
| id | INT AUTO_INCREMENT | Clé primaire |
| matricule | VARCHAR(20) UNIQUE | Identifiant unique |
| nom | VARCHAR(100) | Nom de famille |
| prenom | VARCHAR(100) | Prénom |
| email | VARCHAR(150) UNIQUE | Email |
| telephone | VARCHAR(20) | Numéro de téléphone |
| filiere | VARCHAR(50) | Filière d'études |
| niveau | ENUM | L1, L2, L3, M1, M2 |
| date_naissance | DATE | Date de naissance |
| adresse | TEXT | Adresse postale |
| created_at | TIMESTAMP | Date de création |
| updated_at | TIMESTAMP | Date de modification |

**Table `admins`**
| Colonne | Type | Description |
|---------|------|-------------|
| id | INT AUTO_INCREMENT | Clé primaire |
| username | VARCHAR(50) UNIQUE | Nom d'utilisateur |
| password | VARCHAR(255) | Hash bcrypt du mot de passe |
| nom | VARCHAR(100) | Nom |
| prenom | VARCHAR(100) | Prénom |
| last_login | TIMESTAMP | Dernière connexion |

---

### 4.5 Service Samba (SMB)

#### Rôle
Partage de fichiers accessible depuis Windows, Linux et macOS.

#### Configuration
```yaml
samba:
  image: dperson/samba
  command: >
    -u "campus;campus"
    -s "PartageCampus;/partage;yes;no;yes;campus"
  ports:
    - "1139:139"
    - "1445:445"
```

#### Accès au partage
- **Windows** : `\\localhost:1445\PartageCampus`
- **Utilisateur** : campus
- **Mot de passe** : campus

---

## 5. Application CRUD - Génie Logiciel

### 5.1 Structure de l'Application

```
Portail-campus-CRUD/
├── index.php                 # Page d'accueil avec statistiques
├── config/
│   └── database.php          # Connexion PDO à MySQL
├── includes/
│   ├── header.php            # En-tête HTML + navbar
│   ├── footer.php            # Pied de page
│   ├── session.php           # Gestion des sessions PHP
│   └── functions.php         # Fonctions utilitaires
├── auth/
│   ├── login.php             # Page de connexion
│   └── logout.php            # Déconnexion
├── students/
│   ├── list.php              # Liste avec pagination et recherche
│   ├── create.php            # Formulaire de création
│   ├── edit.php              # Formulaire de modification
│   ├── view.php              # Détails d'un étudiant
│   └── delete.php            # Suppression
├── assets/
│   └── css/
│       └── style.css         # Styles personnalisés
└── sql/
    └── init.sql              # Script d'initialisation BDD
```

### 5.2 Fonctionnalités Implémentées

#### 5.2.1 Authentification et Sessions

```php
// includes/session.php
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}
```

**Sécurité :**
- Mots de passe hashés avec `password_hash()` (bcrypt)
- Vérification avec `password_verify()`
- Protection des routes avec `requireLogin()`

#### 5.2.2 CRUD Étudiants

**CREATE** (`students/create.php`)
- Formulaire avec validation côté serveur
- Génération automatique du matricule
- Vérification de l'unicité (matricule, email)

**READ** (`students/list.php`, `students/view.php`)
- Liste paginée des étudiants
- Affichage détaillé d'un étudiant

**UPDATE** (`students/edit.php`)
- Modification des informations
- Validation des données

**DELETE** (`students/delete.php`)
- Suppression avec confirmation JavaScript

#### 5.2.3 Pagination

```php
// students/list.php
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM etudiants {$whereClause} 
        ORDER BY nom, prenom 
        LIMIT {$perPage} OFFSET {$offset}";
```

**Caractéristiques :**
- 10 étudiants par page
- Navigation précédent/suivant
- Affichage des numéros de page

#### 5.2.4 Recherche Multi-critères

```php
// Construction dynamique de la requête
if (!empty($search)) {
    $where[] = '(nom LIKE ? OR prenom LIKE ? OR matricule LIKE ? OR email LIKE ?)';
}
if (!empty($filterFiliere)) {
    $where[] = 'filiere = ?';
}
if (!empty($filterNiveau)) {
    $where[] = 'niveau = ?';
}
```

**Filtres disponibles :**
- Recherche textuelle (nom, prénom, matricule, email)
- Filtre par filière
- Filtre par niveau (L1-M2)

### 5.3 Sécurité Implémentée

| Vulnérabilité | Protection |
|---------------|------------|
| Injection SQL | Requêtes préparées PDO |
| XSS | `htmlspecialchars()` via fonction `e()` |
| CSRF | Token de session (prévu) |
| Brute Force | Limitation possible avec compteur |

### 5.4 Design et Interface

**Technologies utilisées :**
- Bootstrap 5.3 (framework CSS)
- Bootstrap Icons (icônes)
- Google Fonts (Poppins)
- CSS personnalisé avec dégradés et animations

**Caractéristiques du design :**
- Dégradés de couleurs modernes (violet/bleu)
- Cards avec ombres et effets hover
- Boutons avec animations
- Interface responsive (mobile-friendly)
- Statistiques sur le dashboard

---

## 6. Administration Réseau - Tests

### 6.1 Tests DNS

#### Test 1 : Résolution Interne
```powershell
nslookup web.portail.campus 127.0.0.1
```
**Résultat attendu :** `172.20.0.20`

#### Test 2 : Résolution Externe (Forwarders)
```powershell
nslookup google.com 127.0.0.1
```
**Résultat attendu :** Adresse IP de Google

### 6.2 Tests DHCP

#### Test 1 : Vérifier le serveur
```powershell
docker logs dhcp_campus
```
**Résultat attendu :** `Server starting service.`

#### Test 2 : Attribution d'IP
```powershell
docker run -it --rm --network portail-campus-docker_portail-campus-network alpine sh -c "ip addr show eth0"
```
**Résultat attendu :** IP dans la plage 172.20.0.100-200

#### Test 3 : Vérifier les baux
```powershell
docker exec dhcp_campus cat /var/lib/dhcp/dhcpd.leases
```

### 6.3 Tests de Connectivité

#### Test HTTP
```powershell
Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing
```
**Résultat attendu :** StatusCode 200

#### Test MySQL
```powershell
docker exec db_portail_campus mysql -u campus_user -pcampus_pass -e "SHOW DATABASES;"
```

### 6.4 Tableau Récapitulatif des Tests

| Test | Commande | Résultat |
|------|----------|----------|
| DNS interne | `nslookup dns.portail.campus 127.0.0.1` | ✅ 172.20.0.10 |
| DNS web | `nslookup web.portail.campus 127.0.0.1` | ✅ 172.20.0.20 |
| DNS externe | `nslookup google.com 127.0.0.1` | ✅ IP Google |
| DHCP actif | `docker logs dhcp_campus` | ✅ Server starting |
| HTTP | `curl localhost:8080` | ✅ Status 200 |
| MySQL | `docker exec db_portail_campus mysql...` | ✅ Connexion OK |
| Samba | `docker logs samba_campus` | ✅ smbd started |

---

## 7. Problèmes Rencontrés et Solutions

### 7.1 Problème 1 : Faute de frappe dans le Dockerfile

**Symptôme :** Extension PDO MySQL non installée
```dockerfile
# AVANT (incorrect)
RUN apt-get update && apt-get upgrdade -y
```

**Solution :**
```dockerfile
# APRÈS (corrigé)
RUN apt-get update && apt-get upgrade -y
```

**Impact :** Le serveur web ne pouvait pas se connecter à MySQL.

---

### 7.2 Problème 2 : Hash du mot de passe admin incorrect

**Symptôme :** Connexion impossible avec admin/admin123

**Cause :** Le hash dans `init.sql` correspondait à "password" et non "admin123"

**Solution :**
```php
$hash = password_hash('admin123', PASSWORD_DEFAULT);
// Mise à jour dans la BDD
```

---

### 7.3 Problème 3 : Fichier de leases DHCP manquant

**Symptôme :** 
```
Can't open lease database /var/lib/dhcp/dhcpd.leases: No such file or directory
```

**Solution :**
```powershell
docker run --rm -v portail-campus-docker_dhcp_leases:/data alpine sh -c "touch /data/dhcpd.leases"
docker-compose restart dhcp
```

---

### 7.4 Problème 4 : Incohérence des IPs DNS

**Symptôme :** Le DNS configuré dans DHCP (172.20.0.2) ne correspondait pas à l'IP réelle du serveur DNS (172.20.0.10)

**Solution :** Harmonisation à 172.20.0.10 partout

---

### 7.5 Problème 5 : Absence d'IPs fixes pour les conteneurs

**Symptôme :** Les enregistrements DNS ne correspondaient pas aux vraies IPs des conteneurs

**Solution :** Ajout d'IPs statiques dans `docker-compose.yml`
```yaml
networks:
  portail-campus-network:
    ipv4_address: 172.20.0.20
```

---

### 7.6 Problème 6 : Conflits de ports Samba

**Symptôme :** Les ports 139 et 445 étaient déjà utilisés sur Windows

**Solution :** Utilisation de ports alternatifs
```yaml
ports:
  - "1139:139"
  - "1445:445"
```

---

## 8. Guide d'Installation

### 8.1 Prérequis

- Windows 10/11 avec WSL2
- Docker Desktop installé et configuré
- Ports 53, 8080, 8081, 1139, 1445 disponibles
- 4 Go de RAM minimum

### 8.2 Étapes d'Installation

#### Étape 1 : Cloner le projet
```powershell
git clone <url-du-repo>
cd Portail-Campus-Docker
```

#### Étape 2 : Démarrer Docker Desktop
Lancer Docker Desktop et attendre qu'il soit opérationnel.

#### Étape 3 : Lancer l'infrastructure
```powershell
docker-compose up -d
```

#### Étape 4 : Créer le fichier de leases DHCP
```powershell
docker run --rm -v portail-campus-docker_dhcp_leases:/data alpine sh -c "touch /data/dhcpd.leases"
docker-compose restart dhcp
```

#### Étape 5 : Initialiser la base de données
```powershell
Get-Content "Portail-campus-CRUD/sql/init.sql" | docker exec -i db_portail_campus mysql -u root -prootpass portail_campus_db
```

#### Étape 6 : Accéder à l'application
- **Portail** : http://localhost:8080
- **phpMyAdmin** : http://localhost:8081
- **Identifiants** : admin / admin123

### 8.3 Commandes Utiles

```powershell
# Voir l'état des conteneurs
docker-compose ps

# Voir les logs
docker-compose logs -f

# Arrêter l'infrastructure
docker-compose down

# Arrêter et supprimer les volumes
docker-compose down -v

# Reconstruire une image
docker-compose build serveur_web_php
```

---

## 9. Guide d'Utilisation

### 9.1 Connexion

1. Accéder à http://localhost:8080
2. Cliquer sur "Se connecter"
3. Entrer : `admin` / `admin123`

### 9.2 Gestion des Étudiants

#### Ajouter un étudiant
1. Cliquer sur "Ajouter" dans la navbar
2. Remplir le formulaire (nom et prénom obligatoires)
3. Le matricule est généré automatiquement si non fourni
4. Cliquer sur "Créer l'étudiant"

#### Rechercher un étudiant
1. Aller sur "Étudiants"
2. Utiliser le champ de recherche ou les filtres
3. Cliquer sur "Filtrer"

#### Modifier un étudiant
1. Dans la liste, cliquer sur l'icône crayon
2. Modifier les champs souhaités
3. Cliquer sur "Enregistrer les modifications"

#### Supprimer un étudiant
1. Cliquer sur l'icône poubelle
2. Confirmer la suppression

### 9.3 Accès phpMyAdmin

1. Aller sur http://localhost:8081
2. Se connecter avec `campus_user` / `campus_pass`
3. Sélectionner la base `portail_campus_db`

### 9.4 Accès au Partage Samba

#### Windows
1. Ouvrir l'explorateur de fichiers
2. Entrer `\\localhost:1445` dans la barre d'adresse
3. Se connecter avec `campus` / `campus`

---

## 10. Conclusion

### 10.1 Objectifs Atteints

✅ **Services Communs**
- DNS fonctionnel avec résolution interne et externe
- DHCP distribuant des IPs avec options DNS
- Serveur HTTP avec PHP 8.2
- Base de données MySQL 8.0
- Partage de fichiers Samba

✅ **Génie Logiciel**
- Application CRUD complète
- Pagination (10 résultats/page)
- Recherche multi-critères
- Authentification avec sessions
- Interface moderne et responsive

✅ **Administration Réseau**
- Options DNS dans DHCP
- Résolution interne fonctionnelle
- Tests d'attribution IP validés

### 10.2 Compétences Acquises

- **Docker** : Création d'images, orchestration avec Compose, gestion des volumes et réseaux
- **Réseau** : Configuration DNS BIND9, serveur DHCP, protocole SMB
- **Développement Web** : PHP 8.2, PDO, sessions, sécurité
- **DevOps** : Debugging, logs, tests d'infrastructure

### 10.3 Améliorations Possibles

- [ ] Ajouter HTTPS avec certificat SSL
- [ ] Implémenter la protection CSRF complète
- [ ] Ajouter des rôles utilisateurs (admin, lecteur)
- [ ] Export des données en CSV/PDF
- [ ] Backup automatique de la base de données
- [ ] Monitoring avec Prometheus/Grafana
- [ ] CI/CD avec GitHub Actions

### 10.4 Remerciements

Ce projet a été réalisé dans le cadre de la formation L3 GLAR. Merci aux enseignants pour leur accompagnement dans les modules de Virtualisation, Administration Réseau et Génie Logiciel.

---

## 📎 Annexes

### A. Fichiers de Configuration Complets

Voir les fichiers dans le dépôt :
- `docker-compose.yml`
- `Dockerfile`
- `dhcp/dhcpd.conf`
- `dns/named.conf`
- `dns/db.portail.campus`

### B. Captures d'Écran

Les captures d'écran de l'application sont disponibles dans le dossier `screenshots/` (à créer).

### C. Références

- [Documentation Docker](https://docs.docker.com/)
- [BIND9 Administrator Reference Manual](https://bind9.readthedocs.io/)
- [ISC DHCP Server](https://www.isc.org/dhcp/)
- [PHP Documentation](https://www.php.net/docs.php)
- [Bootstrap 5](https://getbootstrap.com/docs/5.3/)

---

**Fin du Rapport**

*Document généré le 10 Janvier 2026*
