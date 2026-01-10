# 📁 Service SMB - Samba (Partage de Fichiers)

## 📌 Utilité du Service SMB/Samba

**SMB (Server Message Block)** / **Samba** permet de :
- Partager des fichiers et dossiers sur le réseau
- Accéder aux ressources depuis Windows, Linux, macOS
- Gérer des permissions d'accès
- Créer des dossiers partagés pour le travail collaboratif

### Pourquoi Samba ?

- ✅ **Interopérabilité** : Compatible Windows, Linux, macOS
- ✅ **Standard** : Protocole SMB/CIFS utilisé partout
- ✅ **Simplicité** : Montage de partages comme un disque réseau
- ✅ **Performance** : Transferts rapides sur réseau local

---

## 🏗️ Architecture SMB

```
┌──────────────────────────────────────┐
│     Client Windows (Explorateur)     │
│     \\172.20.0.40\PartageCampus      │
└──────────────────┬───────────────────┘
                   │ SMB/CIFS
                   │ Ports 139, 445
                   ▼
        ┌──────────────────────┐
        │   Serveur Samba      │
        │   172.20.0.40        │
        │   Container: samba   │
        └──────────┬───────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  Dossier partagé     │
        │  ./partage/          │
        │  (Volume monté)      │
        └──────────────────────┘
```

---

## 🐳 Configuration Docker

### Dans `docker-compose.yml`

```yaml
samba:
  image: dperson/samba              # Image Samba populaire
  container_name: samba_campus
  environment:
    - USER=campus;campus            # Utilisateur:Mot de passe
  volumes:
    - ./partage:/partage            # Dossier local monté
  command: >
    -u "campus;campus"              # Créer utilisateur
    -s "PartageCampus;/partage;yes;no;yes;campus"  # Configurer partage
  ports:
    - "1139:139"                    # NetBIOS (port alternatif)
    - "1445:445"                    # SMB (port alternatif)
  networks:
    portail-campus-network:
      ipv4_address: 172.20.0.40     # IP fixe
```

### Explication des Paramètres

**Commande `-u` (User) :**
```
-u "campus;campus"
   │       │
   │       └─ Mot de passe
   └───────── Nom d'utilisateur
```

**Commande `-s` (Share) :**
```
-s "PartageCampus;/partage;yes;no;yes;campus"
     │            │         │   │  │   │
     │            │         │   │  │   └─ Utilisateurs autorisés
     │            │         │   │  └───── Accès public (yes/no)
     │            │         │   └──────── Lecture seule (yes/no)
     │            │         └──────────── Accessible (yes/no)
     │            └────────────────────── Chemin du dossier
     └─────────────────────────────────── Nom du partage
```

---

## 🔐 Utilisateurs et Permissions

### Utilisateur Samba

```
Username: campus
Password: campus
Access: Lecture + Écriture sur /partage
```

**⚠️ En production, utilisez un mot de passe fort !**

---

## 📁 Structure du Dossier Partagé

```
partage/
├── Documents/
│   ├── Cours/
│   ├── TD/
│   └── TP/
├── Projets/
│   ├── Projet1/
│   └── Projet2/
├── Ressources/
│   ├── Livres/
│   └── Videos/
└── README.txt
```

Ce dossier est accessible depuis :
- **Windows** : `\\172.20.0.40\PartageCampus`
- **Linux** : `smb://172.20.0.40/PartageCampus`
- **macOS** : `smb://172.20.0.40/PartageCampus`

---

## 🔌 Ports SMB

| Port | Protocole | Usage | Notre Config |
|------|-----------|-------|--------------|
| 139 | NetBIOS | Sessions SMB anciennes | 1139 (alternatif) |
| 445 | SMB | Sessions SMB modernes | 1445 (alternatif) |

**Pourquoi des ports alternatifs ?**
Windows utilise déjà les ports 139 et 445 pour son propre service SMB. On mappe donc vers 1139 et 1445.

---

## 🪟 Accès depuis Windows

### Méthode 1 : Explorateur de fichiers

1. Ouvrir l'Explorateur Windows
2. Dans la barre d'adresse, taper :
   ```
   \\172.20.0.40\PartageCampus
   ```
3. Entrer les identifiants :
   - **Utilisateur** : `campus`
   - **Mot de passe** : `campus`

### Méthode 2 : Lecteur réseau

```powershell
# Monter comme lecteur Z:
net use Z: \\172.20.0.40\PartageCampus /user:campus campus
```

**Démonter :**
```powershell
net use Z: /delete
```

---

### Méthode 3 : PowerShell

```powershell
# Lister les fichiers du partage
Get-ChildItem "\\172.20.0.40\PartageCampus"

# Copier un fichier vers le partage
Copy-Item "C:\mon-fichier.txt" "\\172.20.0.40\PartageCampus\"

# Créer un dossier
New-Item -Path "\\172.20.0.40\PartageCampus\Nouveau" -ItemType Directory
```

---

## 🐧 Accès depuis Linux

### Installer smbclient

```bash
# Ubuntu/Debian
sudo apt install smbclient cifs-utils

# CentOS/RHEL
sudo yum install samba-client cifs-utils
```

### Lister les partages

```bash
smbclient -L //172.20.0.40 -U campus
# Mot de passe : campus
```

### Se connecter au partage

```bash
smbclient //172.20.0.40/PartageCampus -U campus
# Mot de passe : campus
```

**Commandes smbclient :**
```
smb: \> ls              # Lister fichiers
smb: \> cd dossier      # Changer de dossier
smb: \> get fichier     # Télécharger
smb: \> put fichier     # Uploader
smb: \> mkdir nouveau   # Créer dossier
smb: \> exit            # Quitter
```

### Monter le partage

```bash
# Créer un point de montage
sudo mkdir /mnt/campus

# Monter
sudo mount -t cifs //172.20.0.40/PartageCampus /mnt/campus -o username=campus,password=campus

# Démonter
sudo umount /mnt/campus
```

---

## 🍎 Accès depuis macOS

### Finder

1. **Finder** → **Aller** → **Se connecter au serveur** (⌘K)
2. Entrer : `smb://172.20.0.40/PartageCampus`
3. Identifiants : `campus` / `campus`

### Terminal

```bash
# Monter le partage
mkdir ~/Bureau/Campus
mount -t smbfs //campus:campus@172.20.0.40/PartageCampus ~/Bureau/Campus
```

---

## 🧪 Tests et Vérification

### Test 1 : Vérifier que le conteneur fonctionne

```powershell
docker ps | Select-String "samba"
```

**Résultat attendu :**
```
samba_campus   Up X minutes (healthy)   0.0.0.0:1139->139/tcp, 0.0.0.0:1445->445/tcp
```

---

### Test 2 : Voir les logs Samba

```powershell
docker logs samba_campus
```

**Résultat attendu :**
```
Added user campus.
smbd version 4.x.x started.
daemon_ready: daemon 'smbd' finished starting up
```

---

### Test 3 : Accéder au partage depuis PowerShell

```powershell
# Tester l'accès
Test-Path "\\172.20.0.40\PartageCampus"
# Doit retourner: True

# Lister le contenu
Get-ChildItem "\\172.20.0.40\PartageCampus"
```

---

### Test 4 : Créer un fichier de test

```powershell
# Créer un fichier dans le dossier local
"Test de partage Samba" | Out-File -FilePath "partage\test.txt"

# Vérifier qu'il est accessible via SMB
Get-Content "\\172.20.0.40\PartageCampus\test.txt"
```

---

### Test 5 : Vérifier les permissions

```powershell
# Écrire un fichier via SMB
"Fichier créé via SMB" | Out-File "\\172.20.0.40\PartageCampus\smb-test.txt"

# Vérifier qu'il existe localement
Get-Content "partage\smb-test.txt"
```

---

## 🔧 Configuration Avancée

### Ajouter plusieurs utilisateurs

```yaml
samba:
  command: >
    -u "campus;campus"
    -u "admin;adminpass"
    -u "etudiant;etudiantpass"
    -s "PartageCampus;/partage;yes;no;yes;campus,admin"
```

### Créer plusieurs partages

```yaml
samba:
  volumes:
    - ./partage:/partage
    - ./documents:/documents
  command: >
    -u "campus;campus"
    -s "PartageCampus;/partage;yes;no;yes;campus"
    -s "Documents;/documents;yes;no;no;campus"
```

### Partage en lecture seule

```yaml
-s "Archives;/archives;yes;yes;yes;everyone"
#                            │   │
#                            │   └─ Lecture seule: yes
#                            └───── Accessible: yes
```

---

## 📊 Gestion des Fichiers

### Surveiller l'espace disque

```powershell
# Taille du dossier partagé
Get-ChildItem -Path "partage" -Recurse | Measure-Object -Property Length -Sum | 
Select-Object @{Name="Size (MB)"; Expression={[math]::Round($_.Sum / 1MB, 2)}}
```

### Lister les fichiers récents

```powershell
Get-ChildItem -Path "partage" -Recurse | 
Sort-Object LastWriteTime -Descending | 
Select-Object -First 10 Name, LastWriteTime, Length
```

---

## ⚠️ Troubleshooting

### Problème : "Impossible d'accéder au partage"

**Cause :** Ports non accessibles ou firewall Windows

**Solution :**
```powershell
# Vérifier que les ports sont ouverts
Test-NetConnection -ComputerName 172.20.0.40 -Port 1445

# Ajouter une règle firewall si nécessaire
New-NetFirewallRule -DisplayName "Samba Docker" -Direction Inbound -Protocol TCP -LocalPort 1139,1445 -Action Allow
```

---

### Problème : "Accès refusé"

**Cause :** Mauvais identifiants

**Solution :**
```powershell
# Vérifier les logs
docker logs samba_campus

# Recréer l'utilisateur
docker-compose restart samba
```

---

### Problème : "Modifications non visibles"

**Cause :** Cache SMB Windows

**Solution :**
```powershell
# Vider le cache SMB
net use * /delete /y
```

---

### Problème : Performance lente

**Causes :** Volume Docker sur Windows

**Solutions :**
- Utiliser WSL2 pour Docker Desktop
- Désactiver l'antivirus pour le dossier partagé (temporairement)
- Vérifier les logs : `docker logs samba_campus`

---

## 🔐 Sécurité

### Bonnes Pratiques

1. **Mots de passe forts**
   ```yaml
   -u "campus;P@ssw0rd!2026Complex"
   ```

2. **Restreindre les utilisateurs**
   ```yaml
   -s "PartageCampus;/partage;yes;no;no;campus"
   #                                    │      │
   #                                    │      └─ Utilisateurs spécifiques
   #                                    └──────── Public: no
   ```

3. **Permissions Linux**
   ```bash
   docker exec samba_campus chmod 755 /partage
   docker exec samba_campus chown -R smbuser:smbgroup /partage
   ```

---

## 💡 Cas d'Usage

### 1. Partage de documents de cours

```
partage/
├── L3-GLAR/
│   ├── Virtualisation/
│   │   ├── Cours_Docker.pdf
│   │   └── TP_Docker.pdf
│   └── Cloud/
│       └── AWS_Basics.pdf
```

### 2. Dépôt de projets

```
partage/
├── Projets/
│   ├── Groupe1/
│   ├── Groupe2/
│   └── Groupe3/
```

### 3. Sauvegarde automatique

```powershell
# Script de sauvegarde
$source = "C:\MonTravail"
$dest = "\\172.20.0.40\PartageCampus\Backups"
Copy-Item -Path $source -Destination $dest -Recurse -Force
```

---

## 🎯 Points Clés à Retenir

✅ Samba permet le partage de fichiers entre Windows/Linux/macOS
✅ Ports alternatifs (1139, 1445) pour éviter conflit avec Windows
✅ Utilisateur : `campus` / Mot de passe : `campus`
✅ Accessible via `\\172.20.0.40\PartageCampus`
✅ Le dossier `./partage/` est synchronisé avec le conteneur
✅ IP fixe (172.20.0.40) pour résolution DNS

---

## 📚 Ressources

- [Documentation Samba](https://www.samba.org/samba/docs/)
- [dperson/samba Docker Hub](https://hub.docker.com/r/dperson/samba/)
- [SMB Protocol Wiki](https://en.wikipedia.org/wiki/Server_Message_Block)
- [Windows SMB Commands](https://docs.microsoft.com/en-us/windows-server/storage/file-server/)
