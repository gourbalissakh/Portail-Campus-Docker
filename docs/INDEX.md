# 📚 Documentation Portail Campus - Index

Bienvenue dans la documentation complète du projet **Portail Campus** !

---

## 🎯 Vue d'Ensemble du Projet

Ce projet déploie une **infrastructure complète** pour un portail campus universitaire avec Docker, incluant :
- Services réseau (DNS, DHCP, SMB)
- Stack web (Apache, PHP, MySQL)
- Application CRUD de gestion des étudiants

---

## 📖 Table des Matières

### 🌐 Services Réseau

#### [1. DNS - Service de Noms de Domaine](DNS.md)
- 🔍 Résolution des noms `portail.campus`
- ⚙️ Configuration BIND9
- 🧪 Tests de résolution interne et externe
- 📋 Gestion de la zone DNS

#### [2. DHCP - Attribution Automatique d'IP](DHCP.md)
- 📡 Distribution d'adresses IP (172.20.0.100-200)
- ⚙️ Configuration du serveur DHCP
- 🔧 Options DNS automatiques
- 📋 Gestion des baux (leases)

#### [3. SMB/Samba - Partage de Fichiers](SMB.md)
- 📁 Partage réseau `PartageCampus`
- 🔐 Authentification utilisateurs
- 🪟 Accès depuis Windows/Linux/macOS
- 📤 Transfert de fichiers

---

### 💻 Stack Applicative

#### [4. HTTP - Serveur Web Apache + PHP](HTTP.md)
- 🌐 Serveur Apache 2.4
- 🐘 PHP 8.2 avec extensions
- ⚙️ Configuration Docker
- 🔧 Optimisations et sécurité

#### [5. MySQL - Base de Données](MYSQL.md)
- 🗄️ MySQL 8.0
- 📊 Schéma de base de données
- 🔐 Gestion des utilisateurs
- 💾 Sauvegarde et restauration

---

### 🎓 Application Métier

#### [6. GL - Application CRUD Étudiants](GL-CRUD.md)
- ✏️ Créer, Lire, Modifier, Supprimer des étudiants
- 📄 Pagination (20 résultats/page)
- 🔍 Recherche multi-critères
- 🔐 Authentification et sessions
- 🛡️ Sécurité (PDO, XSS protection)

---

### 🔬 Tests et Validation

#### [7. AR - Administration Réseau et Tests](AR-TESTS.md)
- 🧪 Plan de tests complet
- ✅ Validation DNS/DHCP/Connectivité
- 📊 Tests de performance
- 🔒 Tests de sécurité
- 📝 Rapport de tests

---

## 🗺️ Architecture Globale

```
┌─────────────────────────────────────────────────────────┐
│                  NAVIGATEUR WINDOWS                     │
│        http://localhost:8080 | localhost:8081           │
└──────────────────────┬──────────────────────────────────┘
                       │
         ┌─────────────┴──────────────┐
         │     Réseau Docker Bridge   │
         │     172.20.0.0/16          │
         └─────────────┬──────────────┘
                       │
      ┌────────────────┼────────────────┐
      │                │                │
      ▼                ▼                ▼
┌──────────┐    ┌──────────┐    ┌──────────┐
│   DNS    │    │   DHCP   │    │  Samba   │
│ .10:53   │    │  Serveur │    │ .40:1445 │
└──────────┘    └──────────┘    └──────────┘
      │                                 │
      └─────────────┬───────────────────┘
                    │
      ┌─────────────┴────────────┐
      │                          │
      ▼                          ▼
┌──────────┐              ┌──────────┐
│   Web    │─── PDO ────→ │  MySQL   │
│ .20:8080 │              │ .30:3306 │
└──────────┘              └──────────┘
      │                          │
      └──────────────┬───────────┘
                     │
              phpMyAdmin :8081
```

---

## 🚀 Guide de Démarrage Rapide

### 1. Prérequis
- Docker Desktop installé
- Ports 53, 1139, 1445, 8080, 8081 disponibles
- Git (pour cloner le projet)

### 2. Démarrer l'Infrastructure

```powershell
# Cloner le projet (si applicable)
git clone https://github.com/votre-username/Portail-campus-Docker.git
cd Portail-campus-Docker

# Démarrer tous les services
docker-compose up -d

# Vérifier l'état
docker-compose ps
```

### 3. Initialiser la Base de Données

```powershell
# Exécuter le script SQL
Get-Content sql/init.sql | docker exec -i db_portail_campus mysql -u root -prootpass portail_campus_db
```

### 4. Accéder aux Services

- **Application Web** : http://localhost:8080
- **phpMyAdmin** : http://localhost:8081
  - Serveur : `db_portail_campus`
  - User : `campus_user`
  - Pass : `campus_pass`
- **Partage Samba** : `\\172.20.0.40\PartageCampus`
  - User : `campus`
  - Pass : `campus`

---

## 📋 Plan d'Adressage

| Service | Conteneur | IP Fixe | Ports | Description |
|---------|-----------|---------|-------|-------------|
| DNS | `dns_campus` | 172.20.0.10 | 53:53 (TCP/UDP) | Résolution de noms |
| Web | `serveur_web_php` | 172.20.0.20 | 8080:80 | Apache + PHP 8.2 |
| MySQL | `db_portail_campus` | 172.20.0.30 | 3306 (interne) | Base de données |
| Samba | `samba_campus` | 172.20.0.40 | 1139:139, 1445:445 | Partage fichiers |
| DHCP | `dhcp_campus` | Dynamique | - | Attribution IP |
| phpMyAdmin | `phpmyadmin_portail_campus` | Dynamique | 8081:80 | Admin MySQL |

**Réseau** : `172.20.0.0/16`  
**Gateway** : `172.20.0.1`  
**Plage DHCP** : `172.20.0.100 - 172.20.0.200`

---

## 🛠️ Commandes Utiles

### Gestion des Conteneurs

```powershell
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Redémarrer un service
docker-compose restart dns

# Voir les logs
docker-compose logs -f serveur_web_php

# Accéder au shell d'un conteneur
docker exec -it serveur_web_php bash
```

### Tests Rapides

```powershell
# Test DNS
nslookup web.portail.campus 127.0.0.1

# Test Web
Invoke-WebRequest http://localhost:8080

# Test MySQL
docker exec db_portail_campus mysql -u campus_user -pcampus_pass -e "SHOW DATABASES;"

# Test Connectivité
docker exec serveur_web_php ping -c 3 db_portail_campus
```

---

## 📂 Structure du Projet

```
Portail-campus(Docker)/
│
├── docker-compose.yml          # Orchestration
├── Dockerfile                  # Image web personnalisée
├── README.md                   # Documentation principale
├── CORRECTIONS.md              # Historique des corrections
│
├── docs/                       # 📚 Documentation détaillée
│   ├── INDEX.md                # Ce fichier
│   ├── DNS.md                  # Service DNS
│   ├── DHCP.md                 # Service DHCP
│   ├── HTTP.md                 # Serveur Web
│   ├── MYSQL.md                # Base de données
│   ├── SMB.md                  # Partage Samba
│   ├── GL-CRUD.md              # Application CRUD
│   └── AR-TESTS.md             # Tests réseau
│
├── dns/                        # Configuration DNS
│   ├── named.conf              # Config BIND
│   └── db.portail.campus       # Zone DNS
│
├── dhcp/                       # Configuration DHCP
│   └── dhcpd.conf              # Config serveur DHCP
│
├── partage/                    # Dossier Samba
│
├── Portail-campus-CRUD/        # 🎓 Application web
│   ├── index.php
│   ├── config/
│   ├── includes/
│   ├── auth/
│   ├── students/
│   ├── assets/
│   └── sql/
│
└── tests/                      # Scripts de tests
    └── run-all-tests.ps1       # Tests automatiques
```

---

## 🎓 Objectifs Pédagogiques Atteints

### ✅ Services Communs
- [x] DNS configuré et fonctionnel
- [x] DHCP avec attribution automatique
- [x] Serveur HTTP (Apache + PHP)
- [x] Base de données MySQL
- [x] Partage SMB/Samba

### ✅ Génie Logiciel
- [x] Application CRUD complète
- [x] Pagination des résultats
- [x] Recherche multi-critères
- [x] Gestion des sessions
- [x] Sécurité (PDO, échappement HTML)

### ✅ Administration Réseau
- [x] DHCP avec options DNS
- [x] Résolution interne fonctionnelle
- [x] Tests d'attribution IP
- [x] Documentation complète des tests

---

## 🆘 Aide et Support

### Problèmes Courants

**Q : Les conteneurs ne démarrent pas**
```powershell
# Vérifier les logs
docker-compose logs

# Rebuild si nécessaire
docker-compose build --no-cache
docker-compose up -d
```

**Q : Le DNS ne résout pas**
```powershell
# Vérifier le conteneur DNS
docker logs dns_campus

# Redémarrer
docker-compose restart dns
```

**Q : Erreur "Could not find driver" (PHP/MySQL)**
```powershell
# Rebuild l'image web avec les extensions
docker-compose build serveur_web_php
docker-compose up -d
```

---

## 📞 Contact et Contribution

**Projet :** Portail Campus - Infrastructure Dockerisée  
**Cadre :** Licence 3 GLAR - S5 - Virtualisation & Cloud  
**Année :** 2025-2026

---

## 📚 Ressources Complémentaires

- [Documentation Docker](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
- [BIND9 Documentation](https://bind9.readthedocs.io/)
- [ISC DHCP](https://www.isc.org/dhcp/)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL 8.0 Reference](https://dev.mysql.com/doc/refman/8.0/en/)

---

**Bonne exploration de la documentation ! 🚀**
