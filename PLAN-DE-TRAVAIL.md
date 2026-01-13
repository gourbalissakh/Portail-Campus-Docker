# 📋 PLAN DE TRAVAIL - Projet Portail Campus

## Vue d'Ensemble du Projet

**Projet :** Portail Campus - Infrastructure Dockerisée  
**Durée estimée :** 3 semaines  
**Équipe :** 1-2 personnes  
**Technologies :** Docker, PHP 8.2, MySQL 8.0, BIND9, ISC-DHCP, Samba

---

## 🎯 Objectifs du Projet

### Objectif Principal
Déployer une infrastructure réseau complète et conteneurisée pour un portail campus universitaire.

### Objectifs Spécifiques
1. ✅ Créer une application CRUD de gestion des étudiants (GL)
2. ✅ Configurer des services réseau professionnels (AR)
3. ✅ Documenter l'ensemble du projet

---

## 📅 Planning Détaillé

### PHASE 1 : Préparation (Jours 1-3)

#### Jour 1 : Analyse et Conception
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Étude du cahier des charges | 2h | Chef de projet | Document d'analyse |
| Identification des besoins | 1h | Chef de projet | Liste des exigences |
| Recherche des solutions techniques | 2h | Développeur | Benchmark technique |
| Validation du périmètre | 1h | Équipe | PV de réunion |

#### Jour 2 : Architecture
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Conception de l'architecture | 3h | Architecte | Schéma d'architecture |
| Plan d'adressage IP | 1h | Admin réseau | Tableau IP |
| Définition des services | 2h | Équipe | Liste des conteneurs |
| Validation architecture | 1h | Chef de projet | Architecture validée |

#### Jour 3 : Configuration Environnement
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Installation Docker Desktop | 1h | Tous | Docker fonctionnel |
| Création structure projet | 1h | Développeur | Arborescence |
| Configuration Git | 1h | Développeur | Repository |
| Rédaction docker-compose initial | 3h | DevOps | docker-compose.yml v1 |

---

### PHASE 2 : Infrastructure Docker (Jours 4-6)

#### Jour 4 : Services de Base
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Configuration réseau Docker | 2h | DevOps | Network configuré |
| Service MySQL | 2h | DBA | MySQL fonctionnel |
| Service phpMyAdmin | 1h | DBA | Interface accessible |
| Tests de connexion | 1h | QA | Rapport de tests |

#### Jour 5 : Service Web
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Création Dockerfile PHP | 2h | DevOps | Dockerfile |
| Configuration Apache | 1h | Admin sys | Config Apache |
| Installation extensions PHP | 1h | Développeur | Extensions PDO |
| Test PHP → MySQL | 2h | Développeur | Connexion validée |

#### Jour 6 : Services Réseau
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Configuration DNS BIND9 | 3h | Admin réseau | named.conf, zone file |
| Configuration DHCP | 2h | Admin réseau | dhcpd.conf |
| Tests résolution DNS | 1h | QA | Rapport DNS |
| Tests distribution DHCP | 1h | QA | Rapport DHCP |

---

### PHASE 3 : Développement Application (Jours 7-11)

#### Jour 7 : Base de Données
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Conception schéma BDD | 2h | DBA | MCD/MLD |
| Création tables (init.sql) | 2h | DBA | Script SQL |
| Configuration PDO PHP | 1h | Développeur | database.php |
| Insertion données test | 1h | DBA | Données de test |

#### Jour 8 : Structure Application
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Architecture MVC simplifié | 1h | Développeur | Structure dossiers |
| Header/Footer includes | 2h | Développeur | Templates |
| Fonctions utilitaires | 2h | Développeur | functions.php |
| Intégration Bootstrap | 2h | Intégrateur | CSS/JS |

#### Jour 9 : CRUD - Lecture
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Liste des étudiants | 3h | Développeur | list.php |
| Pagination | 2h | Développeur | Pagination fonctionnelle |
| Recherche et filtres | 2h | Développeur | Filtres actifs |

#### Jour 10 : CRUD - Écriture
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Formulaire création | 3h | Développeur | create.php |
| Formulaire modification | 3h | Développeur | edit.php |
| Fonction suppression | 1h | Développeur | delete.php |

#### Jour 11 : Authentification
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Système de login | 3h | Développeur | login.php |
| Gestion des sessions | 2h | Développeur | session.php |
| Protection des routes | 1h | Développeur | Middleware |
| Page de logout | 1h | Développeur | logout.php |

---

### PHASE 4 : Intégration et Services Additionnels (Jours 12-14)

#### Jour 12 : Interface Utilisateur
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Dashboard accueil | 3h | Intégrateur | index.php amélioré |
| Styles personnalisés | 2h | Intégrateur | style.css |
| Vue détaillée étudiant | 2h | Développeur | view.php |

#### Jour 13 : Service Samba
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Configuration conteneur Samba | 2h | Admin sys | Config Samba |
| Création partage | 1h | Admin sys | PartageCampus |
| Tests accès Windows | 2h | QA | Rapport Samba |
| Documentation | 1h | Rédacteur | SMB.md |

#### Jour 14 : Finalisation Infrastructure
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Révision docker-compose | 2h | DevOps | Version finale |
| Optimisation des images | 2h | DevOps | Images légères |
| Configuration volumes | 1h | DevOps | Persistance |
| Tests d'intégration | 2h | QA | Rapport intégration |

---

### PHASE 5 : Tests et Documentation (Jours 15-18)

#### Jour 15 : Tests Fonctionnels
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Tests CRUD complets | 3h | QA | Rapport tests CRUD |
| Tests authentification | 2h | QA | Rapport tests auth |
| Tests de sécurité | 2h | QA | Rapport sécurité |

#### Jour 16 : Tests Réseau
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Tests DNS exhaustifs | 2h | Admin réseau | Rapport DNS |
| Tests DHCP exhaustifs | 2h | Admin réseau | Rapport DHCP |
| Tests connectivité | 2h | Admin réseau | Rapport réseau |
| Tests Samba | 1h | Admin réseau | Rapport Samba |

#### Jour 17 : Documentation
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| README.md | 3h | Rédacteur | README complet |
| Guide d'installation | 2h | Rédacteur | Installation guide |
| Documentation technique | 2h | Rédacteur | Docs techniques |

#### Jour 18 : Rapport Final
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Rédaction rapport projet | 4h | Chef de projet | RAPPORT.md |
| Annexes et captures | 2h | Rédacteur | Annexes |
| Relecture et corrections | 1h | Équipe | Version finale |

---

### PHASE 6 : Finalisation (Jours 19-21)

#### Jour 19 : Corrections
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Correction des bugs | 4h | Développeur | Code corrigé |
| Amélioration UI/UX | 2h | Intégrateur | Interface finale |
| Optimisation performances | 1h | DevOps | App optimisée |

#### Jour 20 : Préparation Présentation
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Création diaporama | 3h | Chef de projet | Slides |
| Préparation démo | 2h | Développeur | Scénario démo |
| Répétition | 2h | Équipe | - |

#### Jour 21 : Livraison
| Tâche | Durée | Responsable | Livrable |
|-------|-------|-------------|----------|
| Vérification finale | 2h | QA | Checklist validée |
| Packaging du projet | 1h | DevOps | Archive finale |
| Présentation | 2h | Équipe | Soutenance |
| Rétrospective | 1h | Équipe | Bilan |

---

## 📊 Diagramme de Gantt Simplifié

```
SEMAINE 1
├── Jours 1-3  : █████ Préparation/Conception
├── Jours 4-6  : █████ Infrastructure Docker
└── Jour 7     : ██ Base de données

SEMAINE 2  
├── Jours 8-11 : ████████ Développement CRUD
├── Jours 12-13: ███ Intégration UI + Samba
└── Jour 14    : ██ Finalisation Infra

SEMAINE 3
├── Jours 15-16: ████ Tests complets
├── Jours 17-18: ████ Documentation
├── Jours 19-20: ████ Corrections + Présentation
└── Jour 21    : ██ Livraison
```

---

## 🎭 Rôles et Responsabilités

| Rôle | Responsabilités | Compétences Requises |
|------|-----------------|----------------------|
| **Chef de projet** | Coordination, planning, rapport | Gestion de projet |
| **Développeur** | Application PHP/CRUD | PHP, SQL, HTML/CSS |
| **Admin réseau** | DNS, DHCP, réseau | BIND9, DHCP, TCP/IP |
| **DevOps** | Docker, infrastructure | Docker, Docker Compose |
| **DBA** | Base de données | MySQL, conception BDD |
| **QA** | Tests, qualité | Tests, documentation |
| **Intégrateur** | UI/UX | Bootstrap, CSS |
| **Rédacteur** | Documentation | Markdown, rédaction |

> **Note :** Dans un projet étudiant, une même personne peut cumuler plusieurs rôles.

---

## 📈 Indicateurs de Suivi

### KPIs du Projet

| Indicateur | Cible | Mesure |
|------------|-------|--------|
| Tâches complétées | 100% | Checklist |
| Tests passés | > 95% | Rapport tests |
| Bugs critiques | 0 | Bug tracker |
| Documentation | 100% | Review |
| Conteneurs opérationnels | 6/6 | docker-compose ps |

### Critères de Succès

- [ ] Tous les conteneurs démarrent sans erreur
- [ ] CRUD complet fonctionnel
- [ ] Authentification sécurisée
- [ ] DNS résout les noms internes
- [ ] DHCP distribue les IPs
- [ ] Samba accessible depuis Windows
- [ ] Documentation complète
- [ ] Rapport de projet livré

---

## 🔄 Processus de Gestion

### Daily Standup (si travail en équipe)
- **Durée** : 15 minutes
- **Questions** :
  1. Qu'ai-je fait hier ?
  2. Que vais-je faire aujourd'hui ?
  3. Y a-t-il des blocages ?

### Revue de Sprint (fin de chaque phase)
- Démonstration des fonctionnalités
- Validation des livrables
- Ajustement du planning si nécessaire

### Rétrospective (fin de projet)
- Ce qui a bien fonctionné
- Ce qui peut être amélioré
- Actions pour les prochains projets

---

## 📦 Livrables Attendus

### Livrables Techniques
| Livrable | Format | Date |
|----------|--------|------|
| docker-compose.yml | YAML | Jour 6 |
| Dockerfile | Docker | Jour 5 |
| Application CRUD | PHP | Jour 14 |
| Configuration DNS | conf | Jour 6 |
| Configuration DHCP | conf | Jour 6 |

### Livrables Documentation
| Livrable | Format | Date |
|----------|--------|------|
| README.md | Markdown | Jour 17 |
| Rapport de projet | Markdown | Jour 18 |
| Guide d'installation | Markdown | Jour 17 |
| Documentation technique | Markdown | Jour 17 |

### Livrables Présentation
| Livrable | Format | Date |
|----------|--------|------|
| Diaporama | PPTX/PDF | Jour 20 |
| Démo live | - | Jour 21 |

---

## ⚠️ Gestion des Risques

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| Retard développement | Moyenne | Haut | Planning avec marge |
| Bug bloquant | Moyenne | Haut | Tests réguliers |
| Problème Docker | Basse | Moyen | Documentation Docker |
| Indisponibilité équipe | Basse | Haut | Backup des connaissances |
| Conflits de ports | Moyenne | Faible | Ports non standards |

---

## 📞 Contacts et Ressources

### Ressources Techniques
- **Documentation Docker** : https://docs.docker.com
- **Documentation PHP** : https://www.php.net/docs.php
- **Bootstrap** : https://getbootstrap.com/docs
- **BIND9** : https://bind9.readthedocs.io

### Support
- Enseignant référent
- Forum de la classe
- Stack Overflow

---

**Document créé le :** 12 Janvier 2026  
**Dernière mise à jour :** 12 Janvier 2026  
**Version :** 1.0
