# 🔍 Service DNS - BIND9

## 📌 Utilité du Service DNS

Le **DNS (Domain Name System)** est le "annuaire d'Internet". Il traduit les noms de domaine lisibles par l'homme (comme `web.portail.campus`) en adresses IP (comme `172.20.0.20`).

### Pourquoi c'est indispensable ?

- ✅ **Facilité d'utilisation** : Plus facile de retenir `web.portail.campus` que `172.20.0.20`
- ✅ **Flexibilité** : Changer l'IP d'un serveur sans modifier les applications
- ✅ **Professionnalisme** : Simule un environnement d'entreprise réel
- ✅ **Services internes** : Permet aux conteneurs de se trouver entre eux

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│  Client (navigateur, conteneur, etc.)  │
│         Demande: web.portail.campus     │
└────────────────┬────────────────────────┘
                 │
                 ▼
        ┌────────────────┐
        │   DNS Server   │
        │  172.20.0.10   │
        │   BIND9        │
        └────────┬───────┘
                 │
                 ▼
      Réponse: 172.20.0.20
```

---

## 📁 Structure des Fichiers

### 1. Configuration Principale - `named.conf`

**Emplacement :** `dns/named.conf`

```conf
options {
    directory "/var/cache/bind";    # Répertoire de travail BIND
    allow-query { any; };            # Accepte les requêtes de tous
    recursion yes;                   # Active la résolution récursive
    forwarders {
        8.8.8.8;                     # Google DNS (résolution externe)
        1.1.1.1;                     # Cloudflare DNS (backup)
    };
};

zone "portail.campus" {
    type master;                     # Serveur autoritaire pour cette zone
    file "/etc/bind/db.portail.campus";  # Fichier de zone
};
```

**Explication des options :**

| Option | Valeur | Signification |
|--------|--------|---------------|
| `directory` | `/var/cache/bind` | Dossier de travail pour les fichiers temporaires |
| `allow-query` | `any` | Autorise toutes les sources à interroger le DNS |
| `recursion` | `yes` | Le serveur peut résoudre des domaines externes |
| `forwarders` | `8.8.8.8; 1.1.1.1` | DNS externes pour résoudre les domaines inconnus |

---

### 2. Fichier de Zone - `db.portail.campus`

**Emplacement :** `dns/db.portail.campus`

```dns
; Durée de vie par défaut des enregistrements (7 jours)
$TTL 604800

; SOA (Start Of Authority) - Informations sur la zone DNS
@   IN  SOA portail.campus. admin.portail.campus. (
        2           ; Serial - Numéro de version de la zone
        604800      ; Refresh - Intervalle de rafraîchissement (7 jours)
        86400       ; Retry - Intervalle de nouvelle tentative (1 jour)
        2419200     ; Expire - Expiration de la zone (4 semaines)
        604800 )    ; Negative Cache TTL - Cache négatif (7 jours)

; Serveur de noms pour la zone portail.campus
@       IN  NS  dns.portail.campus.

; Enregistrements A - Association nom d'hôte -> adresse IP
dns     IN  A   172.20.0.10    ; Serveur DNS
web     IN  A   172.20.0.20    ; Serveur web (Apache/PHP)
db      IN  A   172.20.0.30    ; Serveur base de données (MySQL)
samba   IN  A   172.20.0.40    ; Serveur de fichiers (Samba)
```

**Explication des enregistrements :**

### 🔹 SOA (Start of Authority)
Définit les informations d'autorité pour la zone :
- **Serial** : Numéro de version (incrémentez-le à chaque modification)
- **Refresh** : Fréquence à laquelle les serveurs secondaires vérifient les mises à jour
- **Retry** : Délai avant nouvelle tentative si refresh échoue
- **Expire** : Durée de validité des données en cas d'indisponibilité du primaire
- **TTL négatif** : Durée de mise en cache des réponses "non trouvé"

### 🔹 NS (Name Server)
Désigne le serveur DNS autoritaire pour la zone.

### 🔹 A (Address)
Associe un nom d'hôte à une adresse IPv4.

---

## 🐳 Configuration Docker

### Dans `docker-compose.yml`

```yaml
dns:
  image: ubuntu/bind9:latest
  container_name: dns_campus
  volumes:
    - ./dns/named.conf:/etc/bind/named.conf
    - ./dns/db.portail.campus:/etc/bind/db.portail.campus
  ports:
    - "53:53/udp"    # DNS sur UDP (requêtes standard)
    - "53:53/tcp"    # DNS sur TCP (transferts de zone)
  networks:
    portail-campus-network:
      ipv4_address: 172.20.0.10    # IP fixe du DNS
```

**Pourquoi l'IP fixe ?**
Le DNS doit avoir une IP stable car c'est lui qui résout les autres noms. Si son IP change, plus rien ne fonctionne !

---

## 🧪 Tests et Vérification

### Test 1 : Vérifier que le conteneur fonctionne

```powershell
docker ps | Select-String "dns"
```

**Résultat attendu :**
```
dns_campus   Up X minutes   0.0.0.0:53->53/tcp, 0.0.0.0:53->53/udp
```

---

### Test 2 : Résolution DNS depuis Windows

```powershell
nslookup web.portail.campus 127.0.0.1
```

**Résultat attendu :**
```
Serveur :   UnKnown
Address:  127.0.0.1

Nom :    web.portail.campus
Address:  172.20.0.20
```

---

### Test 3 : Résolution de tous les enregistrements

```powershell
nslookup dns.portail.campus 127.0.0.1
nslookup web.portail.campus 127.0.0.1
nslookup db.portail.campus 127.0.0.1
nslookup samba.portail.campus 127.0.0.1
```

**Tous doivent retourner leurs IPs respectives !**

---

### Test 4 : Résolution externe (via forwarders)

```powershell
nslookup google.com 127.0.0.1
```

**Résultat attendu :** Une adresse IP de Google (prouve que les forwarders fonctionnent)

---

### Test 5 : Depuis un conteneur Docker

```powershell
docker exec serveur_web_php ping -c 2 db.portail.campus
```

**Note :** Les conteneurs Docker utilisent leur DNS interne (127.0.0.11) qui communique avec notre DNS.

---

## 🔧 Maintenance

### Ajouter un nouvel enregistrement

1. **Éditez** `dns/db.portail.campus`
2. **Ajoutez** une ligne :
   ```
   phpmyadmin  IN  A   172.20.0.50
   ```
3. **Incrémentez** le numéro Serial dans le SOA
4. **Redémarrez** le conteneur :
   ```powershell
   docker-compose restart dns
   ```

---

### Vérifier les logs DNS

```powershell
docker logs dns_campus
```

---

### Tester la configuration avant de redémarrer

```powershell
# Entrer dans le conteneur
docker exec -it dns_campus bash

# Vérifier la syntaxe
named-checkconf /etc/bind/named.conf
named-checkzone portail.campus /etc/bind/db.portail.campus
```

---

## ⚠️ Troubleshooting

### Problème : "Connection refused" lors de nslookup

**Cause :** Le conteneur DNS n'est pas démarré ou le port 53 est utilisé

**Solution :**
```powershell
# Vérifier l'état
docker ps | Select-String "dns"

# Voir les logs
docker logs dns_campus

# Redémarrer
docker-compose restart dns
```

---

### Problème : "Server failed" ou pas de réponse

**Cause :** Erreur de syntaxe dans les fichiers de configuration

**Solution :**
```powershell
# Vérifier la configuration
docker exec dns_campus named-checkconf /etc/bind/named.conf
docker exec dns_campus named-checkzone portail.campus /etc/bind/db.portail.campus
```

---

### Problème : Résolution externe ne fonctionne pas

**Cause :** Forwarders mal configurés

**Solution :** Vérifier les forwarders dans `named.conf` :
```conf
forwarders {
    8.8.8.8;
    1.1.1.1;
};
```

---

## 📊 Schéma de Résolution DNS

```
1. Client demande "web.portail.campus"
        ↓
2. DNS vérifie sa zone "portail.campus"
        ↓
3. Trouve l'enregistrement A : web → 172.20.0.20
        ↓
4. Retourne l'IP au client
        ↓
5. Client peut contacter 172.20.0.20


Pour un domaine externe (google.com) :
1. Client demande "google.com"
        ↓
2. DNS vérifie sa zone → Pas trouvé
        ↓
3. DNS interroge les forwarders (8.8.8.8)
        ↓
4. Forwarder retourne l'IP de google.com
        ↓
5. DNS transmet l'IP au client
```

---

## 🎯 Points Clés à Retenir

✅ Le DNS traduit les noms en adresses IP
✅ Notre DNS gère la zone `portail.campus`
✅ Les forwarders permettent de résoudre les domaines externes
✅ L'IP du DNS **doit être fixe** (172.20.0.10)
✅ Le port 53 est utilisé en UDP et TCP
✅ Incrémenter le Serial après chaque modification

---

## 📚 Ressources

- [Documentation BIND9](https://bind9.readthedocs.io/)
- [RFC 1035 - DNS Specification](https://www.rfc-editor.org/rfc/rfc1035)
- [DNS Record Types](https://en.wikipedia.org/wiki/List_of_DNS_record_types)
