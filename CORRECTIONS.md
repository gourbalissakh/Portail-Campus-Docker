# 🔧 Corrections Apportées au Projet Portail Campus

Ce document liste toutes les corrections et modifications apportées au projet pour le rendre fonctionnel.

---

## 📋 Résumé des Problèmes

Au démarrage du projet, **6 problèmes critiques** empêchaient le bon fonctionnement de l'infrastructure Docker :

1. ❌ Erreur de syntaxe dans `docker-compose.yml`
2. ❌ Nom de fichier DHCP incorrect
3. ❌ Incohérence des adresses IP DNS
4. ❌ Absence d'IPs fixes pour les conteneurs
5. ❌ Forwarders DNS manquants
6. ❌ Image Docker DNS introuvable
7. ❌ Conflit de ports Samba
8. ❌ Fichier de leases DHCP manquant

---

## 🛠️ Corrections Détaillées

### 1. Indentation du Service DNS dans `docker-compose.yml`

**🔴 Problème :**
```yaml
    networks:
      - portail-campus-network
    dns:                          # ❌ Mal indenté (sous networks au lieu d'être au même niveau)
    image: internetsystemsconsortium/bind9
```

**❌ Conséquence :**
Le service DNS était considéré comme un enfant de `networks:` au lieu d'être un service indépendant. Docker Compose ne pouvait pas parser le fichier correctement.

**✅ Solution :**
```yaml
    networks:
      - portail-campus-network

  # Serveur DNS                   # ✅ Au même niveau que les autres services
  dns:
    image: internetsystemsconsortium/bind9
```

---

### 2. Nom de Fichier DHCP Incorrect

**🔴 Problème :**
- Fichier existant : `dhcp/dhcp.conf`
- Mapping Docker : `./dhcp/dhcpd.conf:/etc/dhcp/dhcpd.conf`

**❌ Conséquence :**
Docker ne trouvait pas le fichier et créait un répertoire vide à la place, causant l'erreur :
```
error mounting: cannot create subdirectories in "/etc/dhcp/dhcpd.conf": not a directory
```

**✅ Solution :**
Renommé `dhcp/dhcp.conf` → `dhcp/dhcpd.conf`

---

### 3. Incohérence des Adresses IP DNS

**🔴 Problème :**
- Dans `dhcp/dhcp.conf` : DNS configuré à `172.20.0.2`
- Dans `dns/db.portail.campus` : DNS déclaré à `172.20.0.10`

**❌ Conséquence :**
Les clients DHCP recevaient l'adresse IP `172.20.0.2` comme serveur DNS, mais le serveur DNS était réellement à `172.20.0.10`. Aucune résolution de noms n'était possible.

**✅ Solution :**
Harmonisé l'IP DNS à `172.20.0.10` partout :

```conf
# dhcp/dhcpd.conf
option domain-name-servers 172.20.0.10;  # ✅ Corrigé de 172.20.0.2
```

---

### 4. Absence d'IPs Fixes pour les Conteneurs

**🔴 Problème :**
Les fichiers de zone DNS définissaient des IPs fixes :
```
dns.portail.campus   → 172.20.0.10
web.portail.campus   → 172.20.0.20
db.portail.campus    → 172.20.0.30
samba.portail.campus → 172.20.0.40
```

Mais aucune IP statique n'était configurée dans `docker-compose.yml`.

**❌ Conséquence :**
Docker assignait des IPs aléatoires aux conteneurs, rendant le DNS complètement inutile car les enregistrements DNS ne correspondaient pas aux vraies IPs.

**✅ Solution :**
Ajouté des IPs fixes pour chaque service et configuré le réseau avec `ipam` :

```yaml
services:
  serveur_web_php:
    networks:
      portail-campus-network:
        ipv4_address: 172.20.0.20

  db_portail_campus:
    networks:
      portail-campus-network:
        ipv4_address: 172.20.0.30

  samba:
    networks:
      portail-campus-network:
        ipv4_address: 172.20.0.40

  dns:
    networks:
      portail-campus-network:
        ipv4_address: 172.20.0.10

networks:
  portail-campus-network:
    driver: bridge
    ipam:
      config:
        - subnet: 172.20.0.0/16
          gateway: 172.20.0.1
```

---

### 5. Forwarders DNS Manquants

**🔴 Problème :**
Le fichier `named.conf` avait `recursion yes` mais aucun forwarder configuré.

**❌ Conséquence :**
Le serveur DNS pouvait uniquement résoudre le domaine `portail.campus` mais pas les domaines externes (google.com, github.com, etc.).

**✅ Solution :**
Ajouté Google DNS et Cloudflare comme forwarders :

```conf
options {
    directory "/var/cache/bind";
    allow-query { any; };
    recursion yes;
    forwarders {
        8.8.8.8;      # Google DNS
        1.1.1.1;      # Cloudflare DNS
    };
};
```

---

### 6. Image Docker DNS Introuvable

**🔴 Problème :**
```yaml
dns:
  image: internetsystemsconsortium/bind9  # ❌ Image inexistante
```

**❌ Conséquence :**
Erreur Docker :
```
failed to resolve reference "docker.io/internetsystemsconsortium/bind9:latest": not found
```

**✅ Solution :**
Remplacé par l'image officielle Ubuntu :

```yaml
dns:
  image: ubuntu/bind9:latest  # ✅ Image officielle
```

---

### 7. Conflit de Ports Samba

**🔴 Problème :**
```yaml
samba:
  ports:
    - "139:139"
    - "445:445"  # ❌ Port 445 déjà utilisé par Windows
```

**❌ Conséquence :**
Erreur Docker :
```
ports are not available: exposing port TCP 0.0.0.0:445: bind: Only one usage 
of each socket address (protocol/network address/port) is normally permitted.
```

Le port 445 est utilisé par le service SMB natif de Windows et ne peut pas être réutilisé.

**✅ Solution :**
Mappé les ports Samba vers des ports alternatifs :

```yaml
samba:
  ports:
    - "1139:139"  # Port alternatif pour NetBIOS
    - "1445:445"  # Port alternatif pour SMB
```

**Accès :** `\\localhost:1445\PartageCampus` ou directement via IP : `\\172.20.0.40\PartageCampus`

---

### 8. Fichier de Leases DHCP Manquant

**🔴 Problème :**
Le serveur DHCP cherchait le fichier `/var/lib/dhcp/dhcpd.leases` pour stocker les baux DHCP, mais ce fichier n'existait pas.

**❌ Conséquence :**
Erreur DHCP :
```
Can't open lease database /var/lib/dhcp/dhcpd.leases: No such file or directory
```

Le conteneur DHCP démarrait puis s'arrêtait immédiatement.

**✅ Solution :**

**a) Ajouté un volume Docker persistant pour les leases :**

```yaml
dhcp:
  volumes:
    - ./dhcp/dhcpd.conf:/etc/dhcp/dhcpd.conf:ro
    - dhcp_leases:/var/lib/dhcp  # ✅ Volume pour persister les leases

volumes:
  db_data_portail_campus:
  dhcp_leases:  # ✅ Nouveau volume
```

**b) Initialisé le fichier de leases dans le volume :**

```bash
docker run --rm -v portail-campusdocker_dhcp_leases:/data busybox touch /data/dhcpd.leases
```

---

## 📊 État Final des Services

Après toutes les corrections, **tous les 6 services sont opérationnels** :

| Service | Conteneur | IP Fixe | Port(s) | Statut |
|---------|-----------|---------|---------|--------|
| **Serveur Web PHP** | `serveur_web_php` | 172.20.0.20 | 8080:80 | ✅ Running |
| **MySQL** | `db_portail_campus` | 172.20.0.30 | 3306 | ✅ Running |
| **phpMyAdmin** | `phpmyadmin_portail_campus` | - | 8081:80 | ✅ Running |
| **DNS (BIND9)** | `dns_campus` | 172.20.0.10 | 53:53 (TCP/UDP) | ✅ Running |
| **DHCP** | `dhcp_campus` | - | - | ✅ Running |
| **Samba** | `samba_campus` | 172.20.0.40 | 1139, 1445 | ✅ Running |

---

## 🎯 Points Clés à Retenir

### Architecture Réseau
- **Sous-réseau** : `172.20.0.0/16`
- **Gateway** : `172.20.0.1`
- **Plage DHCP** : `172.20.0.100` à `172.20.0.200`
- **DNS** : `172.20.0.10`

### Connexion à la Base de Données (PDO)
⚠️ **Important** : Dans les conteneurs Docker, utilisez le **nom du service** ou **l'IP fixe**, **PAS localhost** :

```php
// ✅ Recommandé : Nom du service
$pdo = new PDO("mysql:host=db_portail_campus;dbname=portail_campus_db", "campus_user", "campus_pass");

// ✅ Alternative : IP fixe
$pdo = new PDO("mysql:host=172.20.0.30;dbname=portail_campus_db", "campus_user", "campus_pass");

// ❌ NE FONCTIONNE PAS
$pdo = new PDO("mysql:host=localhost;dbname=portail_campus_db", "campus_user", "campus_pass");
```

### Accès aux Services

**Web :**
- Application : http://localhost:8080
- phpMyAdmin : http://localhost:8081

**Partage Samba :**
- `\\localhost:1445\PartageCampus`
- Ou directement : `\\172.20.0.40\PartageCampus`
- Utilisateur : `campus` / Mot de passe : `campus`

**DNS :**
Tester la résolution :
```powershell
nslookup dns.portail.campus 172.20.0.10
nslookup web.portail.campus 172.20.0.10
```

---

## 📝 Fichiers Modifiés

1. **`docker-compose.yml`**
   - Correction indentation service DNS
   - Ajout IPs fixes pour tous les services
   - Configuration réseau avec `ipam`
   - Changement image DNS
   - Modification ports Samba
   - Ajout volume pour leases DHCP

2. **`dns/named.conf`**
   - Ajout forwarders DNS (8.8.8.8, 1.1.1.1)

3. **`dhcp/dhcp.conf` → `dhcp/dhcpd.conf`**
   - Renommage du fichier
   - Correction IP DNS (172.20.0.2 → 172.20.0.10)
   - Recréation sans BOM UTF-8

4. **`dns/db.portail.campus`**
   - Ajout de commentaires explicatifs

5. **Nouveaux fichiers créés :**
   - `README.md` - Documentation complète du projet
   - `CORRECTIONS.md` - Ce fichier
   - `.gitignore` - Fichiers à exclure de Git

---

## ✅ Vérification Finale

Pour vérifier que tout fonctionne :

```powershell
# Voir l'état de tous les conteneurs
docker-compose ps

# Tous doivent être "Running" ou "Up"

# Voir les logs en temps réel
docker-compose logs -f

# Tester les services
# Web : http://localhost:8080
# phpMyAdmin : http://localhost:8081
```

---

**Date des corrections** : 9 janvier 2026  
**Projet** : Infrastructure Portail Campus - Licence 3 GLAR S5  
**Environnement** : Docker & Docker Compose
