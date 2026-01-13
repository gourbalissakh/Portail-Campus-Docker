# 📘 RAPPORT COMPLET DE PROJET
## Portail Campus - Infrastructure Dockerisée

---

**Établissement :** Université du Sénégal - L3 GLAR  
**Date de rédaction :** 12 Janvier 2026  
**Version du rapport :** 2.0  
**Chef de projet :** GOURBAL MAHAMAT  
**Technologies utilisées :** Docker, Docker Compose, PHP 8.2, MySQL 8.0, Apache, BIND9, ISC-DHCP, Samba

---

# 📑 TABLE DES MATIÈRES

1. [Résumé Exécutif](#1-résumé-exécutif)
2. [Introduction et Contexte](#2-introduction-et-contexte)
3. [Cahier des Charges](#3-cahier-des-charges)
4. [Architecture Technique](#4-architecture-technique)
5. [Services Déployés](#5-services-déployés)
6. [Application CRUD - Partie Génie Logiciel](#6-application-crud---partie-génie-logiciel)
7. [Configuration Réseau - Partie Administration](#7-configuration-réseau---partie-administration)
8. [Plan de Travail et Planning](#8-plan-de-travail-et-planning)
9. [Tests et Validation](#9-tests-et-validation)
10. [Problèmes Rencontrés et Solutions](#10-problèmes-rencontrés-et-solutions)
11. [Guide de Déploiement](#11-guide-de-déploiement)
12. [Perspectives et Améliorations](#12-perspectives-et-améliorations)
13. [Conclusion](#13-conclusion)
14. [Annexes](#14-annexes)

---

# 1. RÉSUMÉ EXÉCUTIF

## 1.1 Vue d'ensemble

Le projet **Portail Campus** est une infrastructure réseau complète et conteneurisée, conçue pour simuler l'environnement informatique d'un campus universitaire. Ce projet répond aux exigences de deux domaines académiques :

| Domaine | Description | Statut |
|---------|-------------|--------|
| **Génie Logiciel (GL)** | Application CRUD complète pour la gestion des étudiants | ✅ Complet |
| **Administration Réseau (AR)** | Services réseau (DNS, DHCP, HTTP, SMB) | ✅ Complet |

## 1.2 Chiffres Clés

```
┌─────────────────────────────────────────────────────────────┐
│                    STATISTIQUES DU PROJET                    │
├─────────────────────────────────────────────────────────────┤
│  📦 Conteneurs Docker        :  6 services                   │
│  🌐 Réseau configuré         :  172.20.0.0/16               │
│  💾 Base de données          :  2 tables (étudiants, admins) │
│  📄 Fichiers PHP             :  15+ fichiers                 │
│  🔧 Fichiers de config       :  5 fichiers                   │
│  📚 Documentation            :  10+ fichiers Markdown        │
└─────────────────────────────────────────────────────────────┘
```

## 1.3 Livrables

| Livrable | Description | Format |
|----------|-------------|--------|
| Infrastructure Docker | docker-compose.yml + Dockerfile | YAML/Docker |
| Application Web | CRUD PHP complet | PHP/HTML/CSS |
| Configuration DNS | Zone portail.campus | BIND9 |
| Configuration DHCP | Distribution IP automatique | ISC-DHCP |
| Partage de fichiers | Répertoire partagé Samba | SMB |
| Documentation | Rapports et guides | Markdown |

---

# 2. INTRODUCTION ET CONTEXTE

## 2.1 Contexte Académique

Ce projet s'inscrit dans le cadre de la formation **Licence 3 - Génie Logiciel et Administration Réseau (GLAR)** et combine les compétences de plusieurs modules :

- **Virtualisation et Cloud** : Conteneurisation avec Docker
- **Administration Réseau** : Configuration DNS, DHCP, services réseau
- **Génie Logiciel** : Développement d'applications web CRUD
- **Base de données** : Conception et gestion MySQL

## 2.2 Problématique

> Comment mettre en place une infrastructure informatique complète pour un campus universitaire, permettant la gestion des étudiants, la distribution automatique d'adresses IP, la résolution de noms et le partage de fichiers ?

## 2.3 Objectifs du Projet

### Objectifs Généraux
1. Déployer une infrastructure réseau fonctionnelle avec Docker
2. Développer une application de gestion des étudiants
3. Configurer des services réseau professionnels
4. Documenter l'ensemble du projet

### Objectifs Spécifiques

| # | Objectif | Indicateur de réussite |
|---|----------|------------------------|
| 1 | Créer 6 conteneurs Docker interconnectés | Tous les conteneurs démarrent sans erreur |
| 2 | Implémenter un CRUD complet | Create, Read, Update, Delete fonctionnels |
| 3 | Configurer le DNS avec zone personnalisée | Résolution de noms interne opérationnelle |
| 4 | Configurer le DHCP avec options | Distribution IP automatique |
| 5 | Mettre en place un partage Samba | Accès aux fichiers depuis Windows |
| 6 | Sécuriser l'application | Authentification, sessions, préparation SQL |

---

# 3. CAHIER DES CHARGES

## 3.1 Exigences Fonctionnelles

### Services Communs (GL + AR)

| ID | Exigence | Priorité | Statut |
|----|----------|----------|--------|
| EF-01 | Serveur DNS avec zone personnalisée | Haute | ✅ |
| EF-02 | Serveur DHCP avec distribution automatique | Haute | ✅ |
| EF-03 | Serveur HTTP Apache avec PHP | Haute | ✅ |
| EF-04 | Base de données MySQL | Haute | ✅ |
| EF-05 | Serveur de fichiers Samba | Moyenne | ✅ |
| EF-06 | Interface phpMyAdmin | Basse | ✅ |

### Exigences Génie Logiciel (GL)

| ID | Exigence | Priorité | Statut |
|----|----------|----------|--------|
| GL-01 | CRUD complet étudiants | Haute | ✅ |
| GL-02 | Système d'authentification | Haute | ✅ |
| GL-03 | Pagination des résultats | Moyenne | ✅ |
| GL-04 | Recherche et filtrage | Moyenne | ✅ |
| GL-05 | Gestion des sessions | Haute | ✅ |
| GL-06 | Interface responsive | Moyenne | ✅ |

### Exigences Administration Réseau (AR)

| ID | Exigence | Priorité | Statut |
|----|----------|----------|--------|
| AR-01 | DHCP avec option DNS | Haute | ✅ |
| AR-02 | Zone DNS portail.campus | Haute | ✅ |
| AR-03 | Forwarders DNS externes | Moyenne | ✅ |
| AR-04 | Tests de connectivité | Haute | ✅ |
| AR-05 | Adressage IP fixe pour services | Haute | ✅ |

## 3.2 Exigences Non-Fonctionnelles

| Catégorie | Exigence | Implémentation |
|-----------|----------|----------------|
| **Sécurité** | Mots de passe hashés | bcrypt avec PHP |
| **Sécurité** | Protection injection SQL | PDO avec requêtes préparées |
| **Sécurité** | Protection XSS | Fonction htmlspecialchars() |
| **Performance** | Démarrage < 60s | Docker Compose optimisé |
| **Maintenabilité** | Code documenté | Commentaires et README |
| **Portabilité** | Multi-plateforme | Docker (Windows/Linux/Mac) |

---

# 4. ARCHITECTURE TECHNIQUE

## 4.1 Schéma d'Architecture Globale

```
┌────────────────────────────────────────────────────────────────────────────┐
│                           MACHINE HÔTE (Windows)                            │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                         DOCKER DESKTOP                               │   │
│  │                                                                      │   │
│  │   ┌────────────────────────────────────────────────────────────┐    │   │
│  │   │              RÉSEAU BRIDGE: portail-campus-network          │    │   │
│  │   │                    Subnet: 172.20.0.0/16                    │    │   │
│  │   │                    Gateway: 172.20.0.1                      │    │   │
│  │   │                                                             │    │   │
│  │   │   ┌───────────┐  ┌───────────┐  ┌───────────┐             │    │   │
│  │   │   │    DNS    │  │   DHCP    │  │    Web    │             │    │   │
│  │   │   │  BIND9    │  │ ISC-DHCP  │  │Apache+PHP │             │    │   │
│  │   │   │           │  │           │  │           │             │    │   │
│  │   │   │172.20.0.10│  │ Dynamique │  │172.20.0.20│             │    │   │
│  │   │   │  :53/udp  │  │           │  │  :8080    │             │    │   │
│  │   │   │  :53/tcp  │  │           │  │           │             │    │   │
│  │   │   └───────────┘  └───────────┘  └─────┬─────┘             │    │   │
│  │   │                                        │                    │    │   │
│  │   │                                        ▼                    │    │   │
│  │   │   ┌───────────┐  ┌───────────┐  ┌───────────┐             │    │   │
│  │   │   │   Samba   │  │ phpMyAdmin│  │   MySQL   │             │    │   │
│  │   │   │    SMB    │  │   Admin   │  │    8.0    │             │    │   │
│  │   │   │           │  │           │  │           │             │    │   │
│  │   │   │172.20.0.40│  │ Dynamique │  │172.20.0.30│             │    │   │
│  │   │   │:1139/:1445│  │  :8081    │  │  :3306    │             │    │   │
│  │   │   └───────────┘  └───────────┘  └───────────┘             │    │   │
│  │   │                                                             │    │   │
│  │   └────────────────────────────────────────────────────────────┘    │   │
│  │                                                                      │   │
│  │   VOLUMES PERSISTANTS:                                               │   │
│  │   • db_data_portail_campus (MySQL)                                   │   │
│  │   • dhcp_leases (Baux DHCP)                                          │   │
│  │                                                                      │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  PORTS EXPOSÉS À L'HÔTE:                                                   │
│  • 53/tcp + 53/udp (DNS)                                                   │
│  • 8080 (Web PHP)                                                          │
│  • 8081 (phpMyAdmin)                                                       │
│  • 1139, 1445 (Samba)                                                      │
└────────────────────────────────────────────────────────────────────────────┘
```

## 4.2 Plan d'Adressage IP

| Service | Nom du Conteneur | Adresse IP | Port(s) Exposé(s) | Rôle |
|---------|------------------|------------|-------------------|------|
| **DNS** | dns_campus | 172.20.0.10 | 53/tcp, 53/udp | Résolution de noms |
| **Web** | serveur_web_php | 172.20.0.20 | 8080 → 80 | Application PHP |
| **MySQL** | db_portail_campus | 172.20.0.30 | (interne) | Base de données |
| **Samba** | samba_campus | 172.20.0.40 | 1139, 1445 | Partage fichiers |
| **DHCP** | dhcp_campus | Dynamique | - | Distribution IP |
| **phpMyAdmin** | phpmyadmin_portail_campus | Dynamique | 8081 → 80 | Admin BDD |

## 4.3 Configuration Réseau DHCP

| Paramètre | Valeur | Description |
|-----------|--------|-------------|
| Plage IP | 172.20.0.100 - 172.20.0.200 | 101 adresses disponibles |
| Durée bail par défaut | 600 secondes (10 min) | Renouvellement fréquent |
| Durée bail maximum | 7200 secondes (2h) | Limite haute |
| Passerelle | 172.20.0.1 | Gateway Docker |
| Serveur DNS | 172.20.0.10 | Notre serveur BIND9 |

## 4.4 Zone DNS

| Enregistrement | Type | Valeur | Description |
|----------------|------|--------|-------------|
| portail.campus | SOA | - | Start of Authority |
| dns.portail.campus | A | 172.20.0.10 | Serveur DNS |
| web.portail.campus | A | 172.20.0.20 | Serveur Web |
| db.portail.campus | A | 172.20.0.30 | Base de données |
| samba.portail.campus | A | 172.20.0.40 | Partage fichiers |

---

# 5. SERVICES DÉPLOYÉS

## 5.1 Service DNS (BIND9)

### Rôle
Le serveur DNS BIND9 assure la résolution des noms de domaine internes pour la zone `portail.campus`.

### Configuration
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

### Fichier de Zone
```dns
$TTL 604800
@   IN  SOA portail.campus. admin.portail.campus. (
        2           ; Serial
        604800      ; Refresh
        86400       ; Retry
        2419200     ; Expire
        604800 )    ; Negative Cache TTL

@       IN  NS  dns.portail.campus.
dns     IN  A   172.20.0.10
web     IN  A   172.20.0.20
db      IN  A   172.20.0.30
samba   IN  A   172.20.0.40
```

### Justification des Forwarders
Les forwarders (8.8.8.8 et 1.1.1.1) permettent de résoudre les domaines externes (google.com, github.com, etc.) en transférant les requêtes non locales vers des DNS publics.

---

## 5.2 Service DHCP (ISC-DHCP)

### Rôle
Distribution automatique de la configuration réseau aux clients se connectant au réseau.

### Configuration
```conf
default-lease-time 600;
max-lease-time 7200;

subnet 172.20.0.0 netmask 255.255.0.0 {
  range 172.20.0.100 172.20.0.200;
  option routers 172.20.0.1;
  option domain-name-servers 172.20.0.10;
}
```

### Processus DORA (4 étapes)
```
Client                          Serveur DHCP
   |                                 |
   |-------- DISCOVER (broadcast) -->|  1. Le client cherche un serveur
   |                                 |
   |<------- OFFER ------------------|  2. Le serveur propose une IP
   |                                 |
   |-------- REQUEST --------------->|  3. Le client accepte l'offre
   |                                 |
   |<------- ACK --------------------|  4. Le serveur confirme
   |                                 |
```

---

## 5.3 Service HTTP (Apache + PHP 8.2)

### Rôle
Hébergement de l'application web CRUD du portail campus.

### Dockerfile Personnalisé
```dockerfile
FROM php:8.2-apache
RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
EXPOSE 80
```

### Extensions PHP Installées
| Extension | Usage |
|-----------|-------|
| `mysqli` | Connexion MySQL procédurale (héritage) |
| `pdo` | PHP Data Objects - abstraction BDD |
| `pdo_mysql` | Driver PDO pour MySQL |

---

## 5.4 Service MySQL 8.0

### Rôle
Stockage persistant des données de l'application.

### Variables d'Environnement
| Variable | Valeur | Description |
|----------|--------|-------------|
| MYSQL_ROOT_PASSWORD | rootpass | Mot de passe root |
| MYSQL_DATABASE | portail_campus_db | Base créée automatiquement |
| MYSQL_USER | campus_user | Utilisateur applicatif |
| MYSQL_PASSWORD | campus_pass | Mot de passe utilisateur |

### Schéma de Base de Données

#### Table `etudiants`
```sql
CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    filiere VARCHAR(50),
    niveau ENUM('L1', 'L2', 'L3', 'M1', 'M2'),
    date_naissance DATE,
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Table `admins`
```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Hash bcrypt
    nom VARCHAR(100),
    prenom VARCHAR(100),
    last_login TIMESTAMP NULL
);
```

---

## 5.5 Service Samba (SMB)

### Rôle
Partage de fichiers accessible depuis Windows, Linux et macOS.

### Configuration
```yaml
command: >
  -u "campus;campus"
  -s "PartageCampus;/partage;yes;no;yes;campus"
```

### Paramètres du Partage
| Paramètre | Valeur | Signification |
|-----------|--------|---------------|
| Nom du partage | PartageCampus | Nom visible dans le réseau |
| Chemin | /partage | Répertoire partagé dans le conteneur |
| Browseable | yes | Visible dans l'explorateur réseau |
| Read-only | no | Lecture/écriture autorisée |
| Guest | yes | Accès invité autorisé |
| Utilisateur | campus | Utilisateur avec accès complet |

### Accès
- **Windows** : `\\localhost:1445\PartageCampus` ou `\\127.0.0.1:1445\PartageCampus`
- **Identifiants** : campus / campus

---

## 5.6 Service phpMyAdmin

### Rôle
Interface web d'administration de la base de données MySQL.

### Configuration
```yaml
environment:
  PMA_HOST: db_portail_campus
  PMA_USER: campus_user
  PMA_PASSWORD: campus_pass
```

### Accès
- **URL** : http://localhost:8081
- **Connexion automatique** avec les identifiants configurés

---

# 6. APPLICATION CRUD - PARTIE GÉNIE LOGICIEL

## 6.1 Architecture de l'Application

```
Portail-campus-CRUD/
│
├── index.php                    # Page d'accueil avec dashboard
│
├── assets/
│   └── css/
│       └── style.css            # Styles personnalisés
│
├── auth/
│   ├── login.php                # Connexion administrateur
│   └── logout.php               # Déconnexion
│
├── config/
│   └── database.php             # Configuration PDO
│
├── includes/
│   ├── header.php               # En-tête HTML (Bootstrap, NavBar)
│   ├── footer.php               # Pied de page
│   ├── session.php              # Gestion des sessions
│   └── functions.php            # Fonctions utilitaires
│
├── students/
│   ├── list.php                 # Liste avec pagination
│   ├── create.php               # Création d'étudiant
│   ├── view.php                 # Fiche détaillée
│   ├── edit.php                 # Modification
│   └── delete.php               # Suppression
│
└── sql/
    └── init.sql                 # Script d'initialisation BDD
```

## 6.2 Fonctionnalités Implémentées

### 6.2.1 Authentification
- **Login/Logout** sécurisé avec sessions PHP
- **Hash bcrypt** pour les mots de passe
- **Protection des routes** sensibles
- **Identifiants par défaut** : admin / admin123

### 6.2.2 CRUD Complet

| Opération | URL | Méthode | Description |
|-----------|-----|---------|-------------|
| **C**reate | /students/create.php | POST | Ajout d'un nouvel étudiant |
| **R**ead | /students/list.php | GET | Liste paginée avec filtres |
| **R**ead | /students/view.php?id=X | GET | Fiche détaillée d'un étudiant |
| **U**pdate | /students/edit.php?id=X | POST | Modification des informations |
| **D**elete | /students/delete.php?id=X | POST | Suppression avec confirmation |

### 6.2.3 Fonctionnalités Avancées

| Fonctionnalité | Description | Implémentation |
|----------------|-------------|----------------|
| **Pagination** | Navigation par pages | Paramètres GET + LIMIT/OFFSET SQL |
| **Recherche** | Recherche multi-champs | LIKE sur nom, prénom, matricule, email |
| **Filtrage** | Par filière et niveau | WHERE dynamique |
| **Tri** | Colonnes triables | ORDER BY avec direction |
| **Responsive** | Adaptatif mobile | Bootstrap 5 |

## 6.3 Sécurité Implémentée

### Protection contre les Injections SQL
```php
// MAUVAIS (vulnérable)
$sql = "SELECT * FROM etudiants WHERE id = $id";

// BON (sécurisé - requêtes préparées)
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$id]);
```

### Protection XSS
```php
// Fonction utilitaire
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Utilisation dans les vues
<td><?= e($student['nom']) ?></td>
```

### Validation des Entrées
```php
// Validation côté serveur
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = in_array($perPage, [5, 10, 25, 50]) ? $perPage : 10;

// Colonnes de tri valides (whitelist)
$validSortColumns = ['matricule', 'nom', 'prenom', 'filiere', 'niveau', 'email'];
if (!in_array($sortBy, $validSortColumns)) {
    $sortBy = 'nom';
}
```

## 6.4 Interface Utilisateur

### Technologies Front-end
- **Bootstrap 5.3** : Framework CSS responsive
- **Bootstrap Icons** : Bibliothèque d'icônes
- **CSS personnalisé** : Styles supplémentaires

### Captures d'Écran Conceptuelles

```
┌──────────────────────────────────────────────────────────────┐
│  🎓 Portail Campus                    [Dashboard] [Déconnexion]
├──────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌──────────┐  ┌──────────┐  ┌──────────┐                 │
│   │    42    │  │    15    │  │    12    │                 │
│   │ Étudiants│  │   GLAR   │  │    L3    │                 │
│   └──────────┘  └──────────┘  └──────────┘                 │
│                                                              │
│   [Liste des Étudiants] [Nouvel Étudiant] [phpMyAdmin]      │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

# 7. CONFIGURATION RÉSEAU - PARTIE ADMINISTRATION

## 7.1 Docker Compose - Orchestration

### Structure du docker-compose.yml

```yaml
version: "3.8"

services:
  serveur_web_php:    # Service Web PHP/Apache
  db_portail_campus:  # Service MySQL
  phpmyadmin:         # Interface admin BDD
  dhcp:               # Service DHCP
  samba:              # Partage de fichiers
  dns:                # Service DNS BIND9

networks:
  portail-campus-network:
    driver: bridge
    ipam:
      config:
        - subnet: 172.20.0.0/16
          gateway: 172.20.0.1

volumes:
  db_data_portail_campus:  # Données MySQL persistantes
  dhcp_leases:             # Baux DHCP
```

### Dépendances entre Services
```
                    ┌─────────────┐
                    │     DNS     │
                    └─────────────┘
                           ▲
                           │
    ┌──────────────────────┼──────────────────────┐
    │                      │                      │
    ▼                      ▼                      ▼
┌───────┐           ┌───────────┐           ┌───────┐
│ DHCP  │           │    Web    │           │ Samba │
└───────┘           │  (PHP)    │           └───────┘
                    └─────┬─────┘
                          │ depends_on
                          ▼
                    ┌───────────┐
                    │   MySQL   │◄───── phpMyAdmin
                    └───────────┘
```

## 7.2 Tests de Connectivité

### Tests DNS
```bash
# Résolution interne
nslookup web.portail.campus 172.20.0.10
# Résultat attendu: 172.20.0.20

# Résolution externe (via forwarders)
nslookup google.com 172.20.0.10
# Résultat attendu: IPs de Google
```

### Tests DHCP
```bash
# Voir les baux attribués
docker exec dhcp_campus cat /var/lib/dhcp/dhcpd.leases

# Tester depuis un conteneur client
docker run --rm --network portail-campus-network busybox udhcpc
```

### Tests de Connectivité Réseau
```bash
# Ping entre conteneurs
docker exec serveur_web_php ping -c 3 172.20.0.30  # Vers MySQL
docker exec dns_campus ping -c 3 172.20.0.20       # Vers Web
```

---

# 8. PLAN DE TRAVAIL ET PLANNING

## 8.1 Diagramme de Gantt (Représentation Textuelle)

```
SEMAINE 1 : Préparation et Configuration de Base
├── Jour 1-2 : Analyse et conception
│   ├── Étude du cahier des charges
│   ├── Définition de l'architecture
│   └── Création du plan d'adressage IP
│
├── Jour 3-4 : Infrastructure Docker
│   ├── Rédaction docker-compose.yml
│   ├── Configuration du réseau bridge
│   └── Création du Dockerfile PHP
│
└── Jour 5 : Services réseau de base
    ├── Configuration DNS (named.conf, zone)
    └── Configuration DHCP (dhcpd.conf)

SEMAINE 2 : Développement Application
├── Jour 1 : Base de données
│   ├── Création du schéma SQL
│   ├── Configuration MySQL
│   └── Test de connexion PDO
│
├── Jour 2-3 : CRUD Étudiants
│   ├── Liste (Read) avec pagination
│   ├── Création (Create)
│   ├── Modification (Update)
│   └── Suppression (Delete)
│
├── Jour 4 : Authentification
│   ├── Système de login/logout
│   ├── Gestion des sessions
│   └── Protection des routes
│
└── Jour 5 : Interface utilisateur
    ├── Intégration Bootstrap
    ├── Dashboard avec statistiques
    └── Responsive design

SEMAINE 3 : Intégration et Tests
├── Jour 1-2 : Services additionnels
│   ├── Configuration Samba
│   ├── Intégration phpMyAdmin
│   └── Tests des partages
│
├── Jour 3 : Tests complets
│   ├── Tests fonctionnels CRUD
│   ├── Tests réseau (DNS, DHCP)
│   └── Tests de sécurité
│
├── Jour 4 : Documentation
│   ├── README.md
│   ├── Rapport de projet
│   └── Guides utilisateur
│
└── Jour 5 : Finalisation
    ├── Corrections de bugs
    ├── Optimisations
    └── Préparation de la présentation
```

## 8.2 Tableau des Tâches Détaillé

| Phase | Tâche | Durée | Dépendances | Livrable |
|-------|-------|-------|-------------|----------|
| **1. Analyse** | Étude cahier des charges | 2h | - | Document d'analyse |
| **1. Analyse** | Conception architecture | 3h | Étude | Schéma architecture |
| **2. Infra** | Docker Compose | 4h | Conception | docker-compose.yml |
| **2. Infra** | Dockerfile PHP | 1h | Docker Compose | Dockerfile |
| **3. Réseau** | Configuration DNS | 3h | Infra | named.conf, db.zone |
| **3. Réseau** | Configuration DHCP | 2h | DNS | dhcpd.conf |
| **4. BDD** | Schéma MySQL | 2h | Infra | init.sql |
| **4. BDD** | Configuration PDO | 1h | Schéma | database.php |
| **5. CRUD** | Liste étudiants | 4h | BDD | list.php |
| **5. CRUD** | Création étudiant | 3h | Liste | create.php |
| **5. CRUD** | Vue détaillée | 2h | Création | view.php |
| **5. CRUD** | Modification | 3h | Vue | edit.php |
| **5. CRUD** | Suppression | 2h | Modification | delete.php |
| **6. Auth** | Login/Logout | 3h | CRUD | auth/*.php |
| **6. Auth** | Sessions | 2h | Login | session.php |
| **7. UI** | Bootstrap | 4h | Auth | header.php, style.css |
| **7. UI** | Dashboard | 2h | Bootstrap | index.php |
| **8. Services** | Samba | 2h | Infra | Config Samba |
| **8. Services** | phpMyAdmin | 1h | MySQL | Config PMA |
| **9. Tests** | Tests fonctionnels | 3h | Tous | Rapport tests |
| **9. Tests** | Tests réseau | 2h | Tous | Rapport tests |
| **10. Doc** | Documentation | 4h | Tests | README, RAPPORT |

## 8.3 Répartition des Ressources

```
Effort par domaine (estimation):

Génie Logiciel (GL)     : ████████████████████  60%
├── Application CRUD    : ████████████  40%
├── Interface UI        : ████  15%
└── Sécurité           : ██  5%

Administration Réseau   : ██████████  30%
├── Configuration DNS   : ███  10%
├── Configuration DHCP  : ██  8%
├── Docker/Réseau      : ███  10%
└── Samba              : █  2%

Documentation/Tests     : ███  10%
```

---

# 9. TESTS ET VALIDATION

## 9.1 Tests Fonctionnels CRUD

| Test | Description | Résultat Attendu | Statut |
|------|-------------|------------------|--------|
| T-CREATE-01 | Créer un étudiant valide | Enregistrement en BDD | ✅ Pass |
| T-CREATE-02 | Créer avec matricule existant | Message d'erreur | ✅ Pass |
| T-READ-01 | Afficher liste étudiants | Liste paginée | ✅ Pass |
| T-READ-02 | Rechercher par nom | Résultats filtrés | ✅ Pass |
| T-READ-03 | Filtrer par filière | Résultats filtrés | ✅ Pass |
| T-UPDATE-01 | Modifier un étudiant | Données mises à jour | ✅ Pass |
| T-DELETE-01 | Supprimer un étudiant | Étudiant supprimé | ✅ Pass |
| T-AUTH-01 | Login avec bon mot de passe | Connexion réussie | ✅ Pass |
| T-AUTH-02 | Login avec mauvais mot de passe | Connexion refusée | ✅ Pass |
| T-AUTH-03 | Accès page protégée sans login | Redirection login | ✅ Pass |

## 9.2 Tests Réseau

| Test | Commande | Résultat Attendu | Statut |
|------|----------|------------------|--------|
| T-DNS-01 | `nslookup web.portail.campus 172.20.0.10` | 172.20.0.20 | ✅ Pass |
| T-DNS-02 | `nslookup google.com 172.20.0.10` | IPs Google | ✅ Pass |
| T-DHCP-01 | Demande IP via udhcpc | IP dans plage 100-200 | ✅ Pass |
| T-DHCP-02 | Option DNS distribuée | DNS = 172.20.0.10 | ✅ Pass |
| T-NET-01 | Ping Web → MySQL | Réponse OK | ✅ Pass |
| T-NET-02 | Connexion PDO | Connexion établie | ✅ Pass |
| T-SMB-01 | Accès partage Samba | Fichiers visibles | ✅ Pass |

## 9.3 Tests de Sécurité

| Test | Description | Résultat | Statut |
|------|-------------|----------|--------|
| T-SEC-01 | Injection SQL sur recherche | Requête échoue proprement | ✅ Pass |
| T-SEC-02 | XSS via nom d'étudiant | HTML échappé | ✅ Pass |
| T-SEC-03 | Accès direct URL protégée | Redirection login | ✅ Pass |
| T-SEC-04 | Mots de passe en clair en BDD | Non (hash bcrypt) | ✅ Pass |

---

# 10. PROBLÈMES RENCONTRÉS ET SOLUTIONS

## 10.1 Problèmes Infrastructure

| Problème | Cause | Solution |
|----------|-------|----------|
| Conteneurs ne démarrent pas | Ports déjà utilisés | Changer les ports mappés (1139, 1445 pour Samba) |
| DNS ne résout pas l'externe | Forwarders manquants | Ajout de 8.8.8.8 et 1.1.1.1 |
| DHCP ne démarre pas | Permissions insuffisantes | Mode `privileged: true` |
| MySQL refuse les connexions | Démarrage non terminé | `depends_on` + attente applicative |

## 10.2 Problèmes Application

| Problème | Cause | Solution |
|----------|-------|----------|
| PDO extension non trouvée | Extension non installée | Installation via Dockerfile |
| Caractères spéciaux mal affichés | Encodage incorrect | Charset UTF8MB4 partout |
| Session perdue entre pages | Configuration session | Session start dans header |
| Pagination incorrecte | Calcul offset erroné | Formule: (page-1) * perPage |

## 10.3 Leçons Apprises

1. **Toujours utiliser `depends_on`** pour les dépendances entre services
2. **Volumes pour la persistance** des données critiques
3. **Logs Docker** essentiels pour le débogage : `docker-compose logs -f`
4. **Requêtes préparées** obligatoires pour la sécurité SQL
5. **Tests incrémentaux** : tester chaque service individuellement

---

# 11. GUIDE DE DÉPLOIEMENT

## 11.1 Prérequis Système

| Composant | Version Minimum | Recommandé |
|-----------|-----------------|------------|
| Docker Desktop | 4.0+ | 4.25+ |
| Docker Compose | 2.0+ | 2.23+ |
| RAM | 4 Go | 8 Go |
| Espace disque | 5 Go | 10 Go |
| OS | Windows 10/11, Linux, macOS | Windows 11 |

## 11.2 Installation Étape par Étape

### Étape 1 : Cloner le Projet
```powershell
git clone <url-du-repo>
cd Portail-Campus-Docker
```

### Étape 2 : Vérifier les Prérequis
```powershell
docker --version
docker-compose --version
```

### Étape 3 : Démarrer l'Infrastructure
```powershell
# Construire et démarrer tous les services
docker-compose up -d --build

# Vérifier l'état
docker-compose ps
```

### Étape 4 : Initialiser la Base de Données
```powershell
# Attendre que MySQL soit prêt (~30 secondes)
# Puis exécuter le script SQL
docker exec -i db_portail_campus mysql -ucampus_user -pcampus_pass portail_campus_db < Portail-campus-CRUD/sql/init.sql
```

### Étape 5 : Vérifier les Services
```powershell
# Ouvrir dans le navigateur:
# - Application: http://localhost:8080
# - phpMyAdmin: http://localhost:8081
```

## 11.3 Commandes Utiles

| Action | Commande |
|--------|----------|
| Démarrer tous les services | `docker-compose up -d` |
| Arrêter tous les services | `docker-compose down` |
| Voir les logs | `docker-compose logs -f` |
| Logs d'un service | `docker-compose logs -f serveur_web_php` |
| Reconstruire | `docker-compose up -d --build` |
| Supprimer tout (volumes inclus) | `docker-compose down -v` |
| Accéder au shell d'un conteneur | `docker exec -it serveur_web_php bash` |

---

# 12. PERSPECTIVES ET AMÉLIORATIONS

## 12.1 Améliorations Court Terme

| Amélioration | Description | Priorité |
|--------------|-------------|----------|
| HTTPS | Certificat SSL avec Let's Encrypt | Haute |
| Backup automatique | Script de sauvegarde MySQL | Haute |
| Validation client | JavaScript côté formulaires | Moyenne |
| Export CSV/PDF | Export des listes d'étudiants | Moyenne |

## 12.2 Améliorations Moyen Terme

| Amélioration | Description | Priorité |
|--------------|-------------|----------|
| API REST | Endpoints JSON pour intégrations | Haute |
| Multi-utilisateurs | Rôles (admin, secrétaire, lecture) | Moyenne |
| Monitoring | Prometheus + Grafana | Moyenne |
| CI/CD | Pipeline GitHub Actions | Basse |

## 12.3 Évolutions Long Terme

- Migration vers **Kubernetes** pour la scalabilité
- **Microservices** : découplage des fonctionnalités
- **SSO** : Authentification unique avec LDAP/Active Directory
- **Application mobile** : Version iOS/Android

---

# 13. CONCLUSION

## 13.1 Bilan du Projet

Le projet **Portail Campus** a permis de mettre en pratique de manière concrète les compétences acquises dans les domaines du **Génie Logiciel** et de l'**Administration Réseau**.

### Objectifs Atteints

✅ **Infrastructure complète** : 6 services Docker interconnectés
✅ **Application CRUD fonctionnelle** : Gestion complète des étudiants
✅ **Services réseau opérationnels** : DNS, DHCP, Samba configurés
✅ **Sécurité implémentée** : Authentification, sessions, protection SQL
✅ **Documentation complète** : README, rapports, guides

### Compétences Développées

- **Conteneurisation** : Docker, Docker Compose, networking
- **Développement web** : PHP 8, PDO, Bootstrap
- **Administration système** : BIND9, ISC-DHCP, Samba
- **Gestion de projet** : Planification, documentation, tests

## 13.2 Difficultés Surmontées

Les principales difficultés ont été la configuration du réseau Docker avec des IPs fixes et la gestion des dépendances entre services. L'utilisation des logs Docker et une approche incrémentale ont permis de résoudre ces problèmes.

## 13.3 Mot de Fin

Ce projet illustre comment Docker permet de déployer rapidement une infrastructure complexe, reproductible et portable. Les compétences acquises sont directement transférables en environnement professionnel.

---

# 14. ANNEXES

## Annexe A : Structure Complète des Fichiers

```
Portail-Campus-Docker/
│
├── docker-compose.yml              # Orchestration Docker
├── Dockerfile                      # Image PHP personnalisée
├── README.md                       # Documentation principale
├── RAPPORT-COMPLET-PROJET.md       # Ce rapport
│
├── dhcp/
│   └── dhcpd.conf                  # Configuration DHCP
│
├── dns/
│   ├── named.conf                  # Configuration BIND9
│   └── db.portail.campus           # Fichier de zone DNS
│
├── docs/
│   ├── AR-TESTS.md                 # Tests Admin Réseau
│   ├── DHCP.md                     # Documentation DHCP
│   ├── DNS.md                      # Documentation DNS
│   ├── GL-CRUD.md                  # Documentation CRUD
│   ├── HTTP.md                     # Documentation HTTP
│   ├── INDEX.md                    # Index documentation
│   ├── MYSQL.md                    # Documentation MySQL
│   └── SMB.md                      # Documentation Samba
│
├── partage/                        # Répertoire partagé Samba
│
└── Portail-campus-CRUD/            # Application PHP
    ├── index.php
    ├── assets/css/style.css
    ├── auth/login.php, logout.php
    ├── config/database.php
    ├── includes/header.php, footer.php, session.php, functions.php
    ├── sql/init.sql
    └── students/list.php, create.php, view.php, edit.php, delete.php
```

## Annexe B : Identifiants par Défaut

| Service | Utilisateur | Mot de passe |
|---------|-------------|--------------|
| Application Web | admin | admin123 |
| MySQL (root) | root | rootpass |
| MySQL (app) | campus_user | campus_pass |
| phpMyAdmin | campus_user | campus_pass |
| Samba | campus | campus |

## Annexe C : Ports Utilisés

| Port Hôte | Port Conteneur | Service | Protocole |
|-----------|----------------|---------|-----------|
| 53 | 53 | DNS | TCP/UDP |
| 8080 | 80 | Web PHP | TCP |
| 8081 | 80 | phpMyAdmin | TCP |
| 1139 | 139 | Samba (NetBIOS) | TCP |
| 1445 | 445 | Samba (SMB) | TCP |

## Annexe D : Glossaire

| Terme | Définition |
|-------|------------|
| **BIND9** | Berkeley Internet Name Domain - Serveur DNS |
| **CRUD** | Create, Read, Update, Delete - Opérations de base |
| **DHCP** | Dynamic Host Configuration Protocol |
| **DNS** | Domain Name System |
| **PDO** | PHP Data Objects - Abstraction base de données |
| **SMB** | Server Message Block - Protocole de partage Windows |
| **SOA** | Start of Authority - Enregistrement DNS principal |

---

**Document rédigé le 12 Janvier 2026**  
**Portail Campus - Infrastructure Dockerisée**  
**Licence 3 GLAR - Université du Sénégal**
