# 🎓 Portail Campus - Infrastructure Dockerisée

Projet d'infrastructure complète pour un portail campus, déployé avec Docker et Docker Compose.

## 📋 Description

Ce projet met en place une infrastructure réseau complète pour un portail campus incluant :
- Serveur web avec PHP
- Base de données MySQL
- Serveur DNS
- Serveur DHCP
- Serveur de fichiers (Samba)
- Interface d'administration de base de données (phpMyAdmin)

## 🏗️ Architecture

### Services Déployés

| Service | Conteneur | IP Fixe | Port(s) | Description |
|---------|-----------|---------|---------|-------------|
| **Web PHP** | `serveur_web_php` | 172.20.0.20 | 8080:80 | Serveur Apache + PHP 8.2 |
| **MySQL** | `db_portail_campus` | 172.20.0.30 | - | Base de données MySQL 8.0 |
| **phpMyAdmin** | `phpmyadmin_portail_campus` | - | 8081:80 | Interface web MySQL |
| **DNS** | `dns_campus` | 172.20.0.10 | 53:53 (TCP/UDP) | Serveur BIND9 |
| **DHCP** | `dhcp_campus` | - | - | Distribution d'adresses IP |
| **Samba** | `samba_campus` | 172.20.0.40 | 139, 445 | Partage de fichiers SMB |

### Réseau

- **Sous-réseau** : `172.20.0.0/16`
- **Gateway** : `172.20.0.1`
- **Plage DHCP** : `172.20.0.100` - `172.20.0.200`
- **DNS** : `172.20.0.10`

## 🚀 Installation et Démarrage

### Prérequis

- Docker Desktop installé
- Docker Compose installé
- Ports 53, 139, 445, 8080, 8081 disponibles

### Lancement

```powershell
# Cloner ou accéder au répertoire du projet
cd "C:\Users\HP\Desktop\Licence 3 GLAR\S5\Virtualisation & Cloud\Portail-campus(Docker)"

# Démarrer tous les services
docker-compose up -d

# Vérifier l'état des conteneurs
docker-compose ps

# Voir les logs
docker-compose logs -f
```

### Arrêt

```powershell
# Arrêter tous les services
docker-compose down

# Arrêter et supprimer les volumes
docker-compose down -v
```

## 🌐 Accès aux Services

### Serveur Web
- **URL** : http://localhost:8080
- **Description** : Application PHP du portail campus

### phpMyAdmin
- **URL** : http://localhost:8081
- **Utilisateur** : `campus_user`
- **Mot de passe** : `campus_pass`

### Partage Samba
- **Chemin Windows** : `\\localhost\PartageCampus`
- **Utilisateur** : `campus`
- **Mot de passe** : `campus`

### DNS
Les enregistrements suivants sont configurés pour le domaine `portail.campus` :
- `dns.portail.campus` → 172.20.0.10
- `web.portail.campus` → 172.20.0.20
- `db.portail.campus` → 172.20.0.30
- `samba.portail.campus` → 172.20.0.40

## 🗂️ Structure du Projet

```
Portail-campus(Docker)/
│
├── docker-compose.yml          # Orchestration des conteneurs
├── Dockerfile                  # Image personnalisée pour le serveur web
├── README.md                   # Ce fichier
│
├── dns/                        # Configuration DNS
│   ├── named.conf              # Fichier de configuration BIND
│   └── db.portail.campus       # Zone DNS du domaine portail.campus
│
├── dhcp/                       # Configuration DHCP
│   └── dhcp.conf               # Configuration du serveur DHCP
│
├── partage/                    # Répertoire partagé via Samba
│
└── Portail-campus-CRUD/        # Application web PHP
    └── index.php               # Page principale
```

## 🔧 Configuration

### Base de Données

- **Base** : `portail_campus_db`
- **Utilisateur** : `campus_user`
- **Mot de passe** : `campus_pass`
- **Root password** : `rootpass`

### DNS

Le serveur DNS BIND9 est configuré pour :
- Résoudre le domaine `portail.campus`
- Utiliser Google DNS (8.8.8.8) et Cloudflare (1.1.1.1) comme forwarders
- Accepter les requêtes de n'importe quelle source

### DHCP

Le serveur DHCP distribue :
- Plage d'adresses : 172.20.0.100 à 172.20.0.200
- Passerelle : 172.20.0.1
- Serveur DNS : 172.20.0.10
- Durée de bail par défaut : 600 secondes (10 minutes)

## 🛠️ Maintenance

### Voir les logs d'un service spécifique

```powershell
docker-compose logs -f serveur_web_php
docker-compose logs -f db_portail_campus
docker-compose logs -f dns
```

### Redémarrer un service

```powershell
docker-compose restart serveur_web_php
```

### Accéder au shell d'un conteneur

```powershell
docker exec -it serveur_web_php bash
docker exec -it db_portail_campus bash
```

### Vérifier le DNS

```powershell
# Depuis Windows
nslookup dns.portail.campus 172.20.0.10
nslookup web.portail.campus 172.20.0.10
```

## 📝 Notes Techniques

- Tous les services critiques (DNS, Web, DB, Samba) ont des **adresses IP fixes** pour garantir la stabilité
- Le serveur DNS utilise la **récursion** avec des forwarders publics pour résoudre les domaines externes
- Le volume `db_data_portail_campus` persiste les données MySQL même après l'arrêt des conteneurs
- Le serveur DHCP nécessite le mode **privileged** pour fonctionner correctement

## 🎯 Objectifs Pédagogiques

Ce projet démontre :
- ✅ L'orchestration multi-conteneurs avec Docker Compose
- ✅ La configuration d'un serveur DNS avec BIND
- ✅ La mise en place d'un serveur DHCP
- ✅ L'intégration d'une stack LAMP (Linux, Apache, MySQL, PHP)
- ✅ Le partage de fichiers avec Samba/SMB
- ✅ La gestion de réseaux Docker personnalisés avec IPs fixes

## 👥 Auteur

Projet réalisé dans le cadre du cours **Virtualisation & Cloud** - Licence 3 GLAR - S5

---

**Version** : 1.0  
**Date** : Janvier 2026
