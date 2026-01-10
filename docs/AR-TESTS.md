# 🔬 Administration Réseau - Tests et Validation

## 📌 Objectif AR (Administration Réseau)

Démontrer et valider le bon fonctionnement de l'infrastructure réseau avec :

✅ **DHCP** : Attribution automatique d'adresses IP
✅ **Options DNS** : Distribution automatique de la configuration DNS
✅ **Résolution interne** : Résolution des noms de domaine `portail.campus`
✅ **Tests d'attribution IP** : Vérifier que le DHCP distribue bien les IPs

---

## 🧪 Plan de Tests

### 1️⃣ Tests DNS
### 2️⃣ Tests DHCP
### 3️⃣ Tests de Connectivité Inter-Conteneurs
### 4️⃣ Tests de Performance
### 5️⃣ Tests de Sécurité

---

## 🔍 1. TESTS DNS

### Test 1.1 : Résolution DNS Depuis Windows

**Objectif :** Vérifier que le serveur DNS résout correctement les noms de domaine.

```powershell
# Tester chaque enregistrement
nslookup dns.portail.campus 127.0.0.1
nslookup web.portail.campus 127.0.0.1
nslookup db.portail.campus 127.0.0.1
nslookup samba.portail.campus 127.0.0.1
```

**Résultats Attendus :**
```
dns.portail.campus    → 172.20.0.10
web.portail.campus    → 172.20.0.20
db.portail.campus     → 172.20.0.30
samba.portail.campus  → 172.20.0.40
```

**Capture d'écran :** `screenshots/dns-resolution.png`

---

### Test 1.2 : Résolution Externe (Forwarders)

**Objectif :** Vérifier que le DNS peut résoudre des domaines externes.

```powershell
nslookup google.com 127.0.0.1
nslookup github.com 127.0.0.1
```

**Résultat Attendu :** Des adresses IP valides de Google et GitHub.

---

### Test 1.3 : Requêtes Inversées (PTR)

**Objectif :** Test avancé de résolution inverse (optionnel).

```powershell
nslookup 172.20.0.10 127.0.0.1
```

---

### Test 1.4 : Logs DNS

**Objectif :** Examiner les requêtes DNS reçues.

```powershell
# Voir les logs en temps réel
docker logs -f dns_campus

# Dans un autre terminal, faire une requête
nslookup web.portail.campus 127.0.0.1
```

**Résultat Attendu :** Les logs montrent la requête pour `web.portail.campus`.

---

## 📡 2. TESTS DHCP

### Test 2.1 : Vérifier le Serveur DHCP

**Objectif :** S'assurer que le serveur DHCP est opérationnel.

```powershell
# Vérifier que le conteneur tourne
docker ps | Select-String "dhcp"

# Voir les logs
docker logs dhcp_campus
```

**Résultat Attendu :**
```
Server starting service.
Listening on LPF/eth0/.../172.20.0.0/16
```

---

### Test 2.2 : Attribution d'IP Automatique

**Objectif :** Créer un conteneur temporaire qui obtient une IP via DHCP.

```powershell
# Créer un conteneur Alpine Linux sur le réseau
docker run -it --rm --network portail-campusdocker_portail-campus-network alpine sh
```

**Dans le conteneur :**
```sh
# Voir l'IP attribuée
ip addr show eth0
```

**Résultat Attendu :** Une IP dans la plage `172.20.0.100 - 172.20.0.200`.

---

### Test 2.3 : Vérifier les Baux (Leases)

**Objectif :** Consulter les baux DHCP actifs.

```powershell
docker exec dhcp_campus cat /var/lib/dhcp/dhcpd.leases
```

**Résultat Attendu :**
```
lease 172.20.0.150 {
  starts ...
  ends ...
  hardware ethernet 02:42:ac:14:00:96;
}
```

---

### Test 2.4 : Options DHCP (DNS)

**Objectif :** Vérifier que le DHCP distribue bien le serveur DNS.

```powershell
# Créer un conteneur et vérifier le DNS reçu
docker run --rm --network portail-campusdocker_portail-campus-network alpine cat /etc/resolv.conf
```

**Résultat Attendu :** Le serveur DNS doit contenir `172.20.0.10` ou le resolver Docker.

---

### Test 2.5 : Renouvellement de Bail

**Objectif :** Observer le renouvellement automatique.

```powershell
# Créer un conteneur qui reste actif
docker run -d --name test-dhcp --network portail-campusdocker_portail-campus-network alpine sleep 3600

# Attendre 5-6 minutes (50% du bail de 10 min)
# Vérifier les logs DHCP
docker logs dhcp_campus | Select-String "DHCPREQUEST"
```

**Résultat Attendu :** Des requêtes de renouvellement apparaissent dans les logs.

---

## 🌐 3. TESTS DE CONNECTIVITÉ

### Test 3.1 : Ping Entre Conteneurs (Par Nom)

**Objectif :** Vérifier que les conteneurs se voient via leurs noms DNS.

```powershell
docker exec serveur_web_php ping -c 3 db_portail_campus
docker exec serveur_web_php ping -c 3 dns_campus
```

**Résultat Attendu :** 0% packet loss.

---

### Test 3.2 : Ping par IP Fixe

**Objectif :** Tester la connectivité réseau directe.

```powershell
docker exec serveur_web_php ping -c 3 172.20.0.10
docker exec serveur_web_php ping -c 3 172.20.0.30
docker exec serveur_web_php ping -c 3 172.20.0.40
```

**Résultat Attendu :** Toutes les IPs répondent.

---

### Test 3.3 : Connexion MySQL

**Objectif :** Vérifier que le serveur web peut accéder à MySQL.

```powershell
docker exec serveur_web_php php -r "
try {
    \$pdo = new PDO('mysql:host=db_portail_campus;dbname=portail_campus_db', 'campus_user', 'campus_pass');
    echo 'Connexion MySQL réussie !\n';
    \$stmt = \$pdo->query('SELECT COUNT(*) FROM etudiants');
    echo 'Nombre d\'étudiants : ' . \$stmt->fetchColumn() . '\n';
} catch (PDOException \$e) {
    echo 'Erreur : ' . \$e->getMessage() . '\n';
}
"
```

**Résultat Attendu :** "Connexion MySQL réussie !".

---

### Test 3.4 : Accès HTTP

**Objectif :** Tester l'accès au serveur web.

```powershell
# Depuis Windows
Invoke-WebRequest -Uri http://localhost:8080 | Select-Object StatusCode

# Depuis un conteneur
docker exec dns_campus wget -q -O - http://172.20.0.20
```

**Résultat Attendu :** Code 200 OK.

---

### Test 3.5 : Accès SMB

**Objectif :** Vérifier l'accès au partage Samba.

```powershell
# Tester la connexion au partage
Test-Path "\\172.20.0.40\PartageCampus"

# Lister les fichiers
Get-ChildItem "\\172.20.0.40\PartageCampus"
```

**Résultat Attendu :** True et liste des fichiers.

---

## ⚡ 4. TESTS DE PERFORMANCE

### Test 4.1 : Temps de Réponse DNS

**Objectif :** Mesurer la rapidité du DNS.

```powershell
Measure-Command { nslookup web.portail.campus 127.0.0.1 }
```

**Résultat Attendu :** < 100 ms.

---

### Test 4.2 : Latence Réseau

**Objectif :** Mesurer le temps de réponse ping.

```powershell
docker exec serveur_web_php ping -c 10 172.20.0.30 | Select-String "avg"
```

**Résultat Attendu :** < 1 ms (réseau local Docker).

---

### Test 4.3 : Débit SMB

**Objectif :** Mesurer la vitesse de transfert de fichiers.

```powershell
# Créer un fichier de 100 MB
fsutil file createnew test100mb.bin 104857600

# Mesurer le temps de copie
Measure-Command { Copy-Item test100mb.bin "\\172.20.0.40\PartageCampus\" }

# Nettoyer
Remove-Item test100mb.bin
Remove-Item "\\172.20.0.40\PartageCampus\test100mb.bin"
```

---

### Test 4.4 : Charge MySQL

**Objectif :** Tester les performances de requêtes.

```powershell
docker exec db_portail_campus mysql -u root -prootpass -e "
USE portail_campus_db;
SELECT BENCHMARK(1000000, (SELECT COUNT(*) FROM etudiants));
"
```

---

## 🔒 5. TESTS DE SÉCURITÉ

### Test 5.1 : Ports Ouverts

**Objectif :** Scanner les ports exposés.

```powershell
# Tester les ports
Test-NetConnection -ComputerName localhost -Port 53
Test-NetConnection -ComputerName localhost -Port 8080
Test-NetConnection -ComputerName localhost -Port 8081
Test-NetConnection -ComputerName localhost -Port 1445
```

**Résultat Attendu :** Tous les ports configurés doivent répondre.

---

### Test 5.2 : Injection SQL

**Objectif :** Vérifier que l'application résiste aux injections SQL.

**Test manuel :** Essayer de se connecter avec :
- Username: `admin' OR '1'='1`
- Password: `anything`

**Résultat Attendu :** Connexion refusée (protection PDO).

---

### Test 5.3 : XSS (Cross-Site Scripting)

**Objectif :** Tester la protection contre XSS.

**Test :** Créer un étudiant avec le nom :
```
<script>alert('XSS')</script>
```

**Résultat Attendu :** Le script est échappé et s'affiche comme texte.

---

## 📊 Rapport de Tests

### Modèle de Rapport

```markdown
# Rapport de Tests - Infrastructure Portail Campus

**Date :** 09/01/2026
**Testeur :** [Votre nom]

## Résumé Exécutif
- Tests réussis : X/Y
- Taux de réussite : XX%

## Tests DNS
| Test | Résultat | Commentaire |
|------|----------|-------------|
| Résolution interne | ✅ PASS | Tous les enregistrements résolus |
| Résolution externe | ✅ PASS | Forwarders fonctionnels |
| Logs | ✅ PASS | Requêtes visibles dans les logs |

## Tests DHCP
| Test | Résultat | Commentaire |
|------|----------|-------------|
| Serveur actif | ✅ PASS | Logs OK |
| Attribution IP | ✅ PASS | IP dans plage 172.20.0.100-200 |
| Options DNS | ✅ PASS | DNS distribué correctement |

## Tests Connectivité
| Test | Résultat | Commentaire |
|------|----------|-------------|
| Ping inter-conteneurs | ✅ PASS | 0% packet loss |
| Connexion MySQL | ✅ PASS | PDO fonctionne |
| Accès HTTP | ✅ PASS | Code 200 |
| Accès SMB | ✅ PASS | Partage accessible |

## Problèmes Identifiés
1. [Si applicable]
2. [Si applicable]

## Recommandations
1. [Améliorations suggérées]
```

---

## 🖼️ Captures d'Écran

### Liste des Captures à Faire

1. **DNS** :
   - `dns-nslookup.png` : Résolution de tous les enregistrements
   - `dns-logs.png` : Logs du serveur DNS

2. **DHCP** :
   - `dhcp-logs.png` : Logs montrant "Server starting service"
   - `dhcp-leases.png` : Contenu du fichier dhcpd.leases
   - `dhcp-attribution.png` : IP obtenue par un conteneur

3. **Connectivité** :
   - `ping-containers.png` : Ping entre conteneurs
   - `mysql-connection.png` : Test de connexion MySQL
   - `web-access.png` : Page web affichée

4. **Application** :
   - `crud-list.png` : Liste des étudiants
   - `crud-create.png` : Formulaire d'ajout
   - `crud-search.png` : Résultats de recherche

---

## 📝 Script de Tests Automatique

**Fichier : `tests/run-all-tests.ps1`**

```powershell
#!/usr/bin/env pwsh

Write-Host "=== Tests Infrastructure Portail Campus ===" -ForegroundColor Cyan

# Test 1 : Conteneurs actifs
Write-Host "`n[TEST 1] Vérification des conteneurs..." -ForegroundColor Yellow
$containers = @("dns_campus", "dhcp_campus", "db_portail_campus", "serveur_web_php", "samba_campus")
foreach ($container in $containers) {
    $status = docker ps --filter "name=$container" --format "{{.Status}}"
    if ($status -like "*Up*") {
        Write-Host "  ✅ $container : UP" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $container : DOWN" -ForegroundColor Red
    }
}

# Test 2 : DNS
Write-Host "`n[TEST 2] Tests DNS..." -ForegroundColor Yellow
$domains = @("dns.portail.campus", "web.portail.campus", "db.portail.campus", "samba.portail.campus")
foreach ($domain in $domains) {
    $result = nslookup $domain 127.0.0.1 2>&1 | Select-String "Address:"
    if ($result) {
        Write-Host "  ✅ $domain résolu" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $domain non résolu" -ForegroundColor Red
    }
}

# Test 3 : Services HTTP
Write-Host "`n[TEST 3] Test services HTTP..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri http://localhost:8080 -UseBasicParsing
    Write-Host "  ✅ Serveur Web : $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Serveur Web inaccessible" -ForegroundColor Red
}

try {
    $response = Invoke-WebRequest -Uri http://localhost:8081 -UseBasicParsing
    Write-Host "  ✅ phpMyAdmin : $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  ❌ phpMyAdmin inaccessible" -ForegroundColor Red
}

# Test 4 : Connectivité inter-conteneurs
Write-Host "`n[TEST 4] Connectivité inter-conteneurs..." -ForegroundColor Yellow
$pingResult = docker exec serveur_web_php ping -c 2 172.20.0.30 2>&1 | Select-String "packet loss"
if ($pingResult -like "*0% packet loss*") {
    Write-Host "  ✅ Ping Web → MySQL : OK" -ForegroundColor Green
} else {
    Write-Host "  ❌ Ping Web → MySQL : FAIL" -ForegroundColor Red
}

# Test 5 : MySQL
Write-Host "`n[TEST 5] Test MySQL..." -ForegroundColor Yellow
$mysqlTest = docker exec db_portail_campus mysql -u campus_user -pcampus_pass -e "SELECT 'OK'" 2>&1
if ($mysqlTest -like "*OK*") {
    Write-Host "  ✅ Connexion MySQL : OK" -ForegroundColor Green
} else {
    Write-Host "  ❌ Connexion MySQL : FAIL" -ForegroundColor Red
}

Write-Host "`n=== Tests Terminés ===" -ForegroundColor Cyan
```

**Exécution :**
```powershell
.\tests\run-all-tests.ps1
```

---

## 🎯 Points Clés à Retenir

✅ **Tests DNS** : Résolution interne et externe fonctionnelle
✅ **Tests DHCP** : Attribution d'IP dans la plage 172.20.0.100-200
✅ **Connectivité** : Tous les conteneurs communiquent
✅ **Performance** : Latence < 1ms sur réseau Docker
✅ **Sécurité** : Protection contre SQL injection et XSS

---

## 📚 Ressources

- [nslookup Documentation](https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/nslookup)
- [Docker Network Troubleshooting](https://docs.docker.com/network/)
- [DHCP Testing](https://www.isc.org/dhcp/)
- [Network Performance Testing](https://iperf.fr/)
