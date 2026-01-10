# 📡 Service DHCP - Dynamic Host Configuration Protocol

## 📌 Utilité du Service DHCP

Le **DHCP** distribue automatiquement des configurations réseau aux machines qui se connectent :
- Adresse IP
- Masque de sous-réseau
- Passerelle par défaut
- Serveurs DNS

### Pourquoi c'est indispensable ?

- ✅ **Automatisation** : Plus besoin de configurer manuellement chaque machine
- ✅ **Évite les conflits IP** : Le serveur gère l'attribution et évite les doublons
- ✅ **Gestion centralisée** : Modifier la configuration réseau en un seul endroit
- ✅ **Flexibilité** : Les machines obtiennent une IP temporaire (bail/lease)

---

## 🏗️ Architecture DHCP

```
┌──────────────────────────────────────────────────┐
│           Processus DHCP (4 étapes)              │
└──────────────────────────────────────────────────┘

1. DISCOVER  📡  Client → Broadcast
   "Y a-t-il un serveur DHCP ?"

2. OFFER     📨  Serveur → Client
   "Voici une IP : 172.20.0.150"

3. REQUEST   ✋  Client → Serveur
   "J'accepte cette IP"

4. ACK       ✅  Serveur → Client
   "IP confirmée + config complète"
```

---

## 📁 Configuration DHCP

### Fichier `dhcpd.conf`

**Emplacement :** `dhcp/dhcpd.conf`

```conf
default-lease-time 600;          # Durée de bail par défaut (10 min)
max-lease-time 7200;             # Durée maximale de bail (2 heures)

subnet 172.20.0.0 netmask 255.255.0.0 {
  range 172.20.0.100 172.20.0.200;      # Plage d'IPs disponibles
  option routers 172.20.0.1;             # Passerelle par défaut
  option domain-name-servers 172.20.0.10; # Serveur DNS
}
```

### Explication Détaillée

| Paramètre | Valeur | Signification |
|-----------|--------|---------------|
| `default-lease-time` | 600 | Durée de bail par défaut : 10 minutes |
| `max-lease-time` | 7200 | Durée maximale d'un bail : 2 heures |
| `subnet` | 172.20.0.0/16 | Sous-réseau géré par le DHCP |
| `range` | 172.20.0.100 - 172.20.0.200 | 101 adresses disponibles |
| `option routers` | 172.20.0.1 | Gateway du réseau Docker |
| `option domain-name-servers` | 172.20.0.10 | Notre serveur DNS BIND9 |

---

## 🔢 Plan d'Adressage

```
Réseau : 172.20.0.0/16
Masque : 255.255.0.0

┌─────────────────────────────────────────┐
│         Adresses Réservées              │
├─────────────────────────────────────────┤
│ 172.20.0.1      Gateway (Docker)        │
│ 172.20.0.10     DNS (dns_campus)        │
│ 172.20.0.20     Web (serveur_web_php)   │
│ 172.20.0.30     MySQL (db_portail)      │
│ 172.20.0.40     Samba (samba_campus)    │
├─────────────────────────────────────────┤
│      Plage DHCP Dynamique               │
├─────────────────────────────────────────┤
│ 172.20.0.100 → 172.20.0.200             │
│ (101 adresses disponibles)              │
└─────────────────────────────────────────┘
```

**Important :** Les IPs fixes (10, 20, 30, 40) sont **en dehors** de la plage DHCP pour éviter les conflits !

---

## 🐳 Configuration Docker

### Dans `docker-compose.yml`

```yaml
dhcp:
  image: networkboot/dhcpd
  container_name: dhcp_campus
  volumes:
    - ./dhcp/dhcpd.conf:/etc/dhcp/dhcpd.conf:ro  # Configuration en lecture seule
    - dhcp_leases:/var/lib/dhcp                   # Volume pour les baux
  command: dhcpd -f -d                            # Mode foreground + debug
  privileged: true                                # Nécessaire pour le DHCP
  networks:
    - portail-campus-network

volumes:
  dhcp_leases:  # Stocke les baux DHCP de manière persistante
```

### Pourquoi `privileged: true` ?

Le DHCP doit écouter sur toutes les interfaces réseau et manipuler les sockets raw. Sans privilèges élevés, il ne peut pas fonctionner.

---

## 📋 Fichier de Leases (Baux)

Le serveur DHCP stocke les baux dans `/var/lib/dhcp/dhcpd.leases`

### Structure d'un bail

```
lease 172.20.0.150 {
  starts 4 2026/01/09 20:30:00;
  ends 4 2026/01/09 20:40:00;
  cltt 4 2026/01/09 20:30:00;
  binding state active;
  next binding state free;
  hardware ethernet 02:42:ac:14:00:96;
}
```

**Signification :**
- IP attribuée : `172.20.0.150`
- Début du bail : 20h30
- Fin du bail : 20h40 (10 minutes après)
- Adresse MAC du client : `02:42:ac:14:00:96`

---

## 🧪 Tests et Vérification

### Test 1 : Vérifier que le conteneur fonctionne

```powershell
docker ps | Select-String "dhcp"
```

**Résultat attendu :**
```
dhcp_campus   Up X minutes
```

---

### Test 2 : Voir les logs DHCP

```powershell
docker logs dhcp_campus
```

**Résultat attendu :**
```
Internet Systems Consortium DHCP Server 4.4.1
Config file: /etc/dhcp/dhcpd.conf
Database file: /var/lib/dhcp/dhcpd.leases
Listening on LPF/eth0/xx:xx:xx:xx:xx:xx/172.20.0.0/16
Sending on   LPF/eth0/xx:xx:xx:xx:xx:xx/172.20.0.0/16
Server starting service.
```

---

### Test 3 : Vérifier les baux actifs

```powershell
docker exec dhcp_campus cat /var/lib/dhcp/dhcpd.leases
```

---

### Test 4 : Simuler une demande DHCP

```powershell
# Créer un conteneur temporaire qui demande une IP
docker run --rm --network portail-campusdocker_portail-campus-network alpine sh -c "ip addr show eth0"
```

Le conteneur devrait recevoir une IP dans la plage 172.20.0.100-200.

---

### Test 5 : Vérifier la configuration DNS distribuée

Quand un conteneur obtient une IP via DHCP, il devrait aussi recevoir l'adresse du serveur DNS (172.20.0.10).

```powershell
docker run --rm --network portail-campusdocker_portail-campus-network alpine cat /etc/resolv.conf
```

---

## 🔧 Options DHCP Avancées

### Options courantes qu'on peut ajouter

```conf
subnet 172.20.0.0 netmask 255.255.0.0 {
  range 172.20.0.100 172.20.0.200;
  
  # Déjà configuré
  option routers 172.20.0.1;
  option domain-name-servers 172.20.0.10;
  
  # Options additionnelles
  option domain-name "portail.campus";
  option broadcast-address 172.20.255.255;
  option netbios-name-servers 172.20.0.40;  # Serveur WINS (Samba)
  
  # Réservation d'IP pour un équipement spécifique
  host imprimante {
    hardware ethernet 00:11:22:33:44:55;
    fixed-address 172.20.0.50;
  }
}
```

---

## 🔍 Fonctionnement Détaillé

### Phase 1 : DISCOVER (Découverte)

```
Client                           Serveur DHCP
  |                                    |
  |-------- DHCPDISCOVER (Broadcast) --->|
  |  "Je cherche un serveur DHCP"      |
  |  Source: 0.0.0.0                   |
  |  Dest: 255.255.255.255             |
```

### Phase 2 : OFFER (Offre)

```
Client                           Serveur DHCP
  |                                    |
  |<---------- DHCPOFFER ---------------|
  |  "Voici une IP : 172.20.0.150"    |
  |  + Masque, Gateway, DNS            |
```

### Phase 3 : REQUEST (Demande)

```
Client                           Serveur DHCP
  |                                    |
  |-------- DHCPREQUEST (Broadcast) --->|
  |  "J'accepte l'IP 172.20.0.150"    |
```

### Phase 4 : ACK (Confirmation)

```
Client                           Serveur DHCP
  |                                    |
  |<----------- DHCPACK ----------------|
  |  "IP confirmée + bail de 10 min"  |
  |                                    |
  [Client configure son interface]
```

---

## 🔄 Renouvellement de Bail

Quand 50% du bail est écoulé (5 min sur 10), le client tente de renouveler :

```
Client                           Serveur DHCP
  |                                    |
  |-------- DHCPREQUEST (Unicast) ----->|
  |  "Je veux prolonger mon bail"     |
  |                                    |
  |<----------- DHCPACK ----------------|
  |  "Bail renouvelé pour 10 min"     |
```

---

## ⚠️ Troubleshooting

### Problème : Le conteneur DHCP s'arrête immédiatement

**Causes possibles :**
1. Fichier de configuration invalide
2. Fichier de leases manquant

**Solution :**
```powershell
# Vérifier les logs
docker logs dhcp_campus

# Vérifier la syntaxe
docker exec dhcp_campus dhcpd -t -cf /etc/dhcp/dhcpd.conf

# Recréer le fichier de leases
docker run --rm -v portail-campusdocker_dhcp_leases:/data busybox touch /data/dhcpd.leases
docker-compose restart dhcp
```

---

### Problème : Aucun bail distribué

**Cause :** Le DHCP ne voit pas les demandes

**Solution :** Vérifier que le serveur écoute sur la bonne interface
```powershell
docker logs dhcp_campus | Select-String "Listening"
```

---

### Problème : Conflit d'adresses IP

**Cause :** Une IP fixe est dans la plage DHCP

**Solution :** Séparer les plages :
- **IPs fixes** : 172.20.0.1 → 172.20.0.99
- **Plage DHCP** : 172.20.0.100 → 172.20.0.200

---

## 📊 Statistiques DHCP

### Capacité du réseau

```
Sous-réseau : 172.20.0.0/16
Adresses totales : 65 536
Adresses réservées (réseau, broadcast) : 2
Adresses utilisables : 65 534

Notre configuration :
- Plage DHCP : 101 adresses
- IPs fixes : 5 adresses
- Disponibles pour extension : ~65 400 adresses
```

---

## 🎯 Points Clés à Retenir

✅ Le DHCP automatise l'attribution des IPs
✅ Durée de bail : 10 min (renouvelable)
✅ Plage : 172.20.0.100 → 172.20.0.200
✅ Le DNS est distribué automatiquement (172.20.0.10)
✅ Les baux sont persistés dans un volume Docker
✅ Mode `privileged` obligatoire

---

## 📚 Ressources

- [RFC 2131 - DHCP Specification](https://www.rfc-editor.org/rfc/rfc2131)
- [ISC DHCP Documentation](https://www.isc.org/dhcp/)
- [DHCP Options Reference](https://www.iana.org/assignments/bootp-dhcp-parameters/)
