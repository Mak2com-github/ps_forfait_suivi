# PrestaShop Forfait Suivi

[![PrestaShop](https://img.shields.io/badge/PrestaShop-1.6%20--%201.8-blue.svg)](https://www.prestashop.com/)
[![PHP](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-AFL%203.0-green.svg)](http://opensource.org/licenses/afl-3.0.php)
[![Version](https://img.shields.io/badge/Version-1.8.1-orange.svg)](https://github.com/mak2com/ps_forfait_suivi)

> Module PrestaShop professionnel pour la gestion et le suivi des forfaits d'intervention MCO (Maintien en Condition Opérationnelle) et des tâches associées.

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Configuration technique](#-configuration-technique)
- [FAQ](#-faq)
- [Contribution](#-contribution)
- [Licence](#-licence)
- [Auteur](#-auteur)

## 🎯 À propos

**PS Forfait Suivi** est un module PrestaShop conçu pour gérer efficacement les forfaits d'intervention web et les tâches associées pour vos clients. Il permet de créer des forfaits avec un crédit temps (sans limitation à 24h), puis de déduire du temps en créant des tâches jusqu'à épuisement du forfait.

Ce module est particulièrement utile pour :
- Les agences web gérant des contrats de maintenance
- Les freelances offrant des forfaits d'heures mensuels
- Les équipes techniques suivant leur temps d'intervention
- Toute structure proposant des prestations au forfait horaire

## ✨ Fonctionnalités

### Gestion des forfaits
- ✅ Création de forfaits avec nom, description et crédit temps
- ✅ Support des forfaits **supérieurs à 24h** (ex: 30h, 100h, etc.)
- ✅ Affichage du temps restant en temps réel
- ✅ Modification des forfaits existants
- ✅ Suivi des dates de création et modification
- ✅ Interface multilingue (support complet i18n)

### Gestion des tâches
- ✅ Création de tâches liées à un forfait
- ✅ Déduction automatique du temps du forfait
- ✅ Statut actif/inactif pour chaque tâche
- ✅ Validation du temps disponible avant création
- ✅ Modification et suppression de tâches
- ✅ Réattribution du temps au forfait lors de la suppression

### Sécurité et performance
- ✅ Protection contre les injections SQL
- ✅ Validation des données côté serveur
- ✅ Support des transactions multilingues
- ✅ Interface Bootstrap responsive
- ✅ Code conforme aux standards PrestaShop

## 📦 Prérequis

- **PrestaShop** : Version 1.6 ou supérieure (testé jusqu'à 1.8)
- **PHP** : Version 7.0 ou supérieure
- **MySQL** : Version 5.6 ou supérieure
- Droits d'administration sur le back-office PrestaShop

## 🚀 Installation

### Méthode 1 : Installation via ZIP

1. **Téléchargez** le module en créant une archive ZIP du répertoire
2. **Connectez-vous** au back-office PrestaShop
3. Naviguez vers **Modules > Gestionnaire de modules**
4. Cliquez sur **"Installer un module"** (bouton en haut à droite)
5. **Glissez-déposez** le fichier ZIP ou cliquez pour le sélectionner
6. Attendez la fin de l'installation
7. Cliquez sur **"Installer"** puis **"Activer"**

### Méthode 2 : Installation manuelle

1. Téléchargez le module
2. Décompressez l'archive dans `/modules/ps_forfait_suivi/`
3. Connectez-vous au back-office
4. Allez dans **Modules > Gestionnaire de modules**
5. Recherchez "Forfait Suivi"
6. Cliquez sur **"Installer"**

### Vérification de l'installation

Après installation, deux nouveaux menus apparaissent dans :
```
Back Office > Configurer > Paramètres de la boutique
├── Gestion des forfaits
└── Gestion des tâches
```

## 📖 Utilisation

### 1️⃣ Créer un forfait

1. Accédez à **Gestion des forfaits** dans le menu
2. Cliquez sur **"Ajout nouveau forfait"**
3. Remplissez le formulaire :
   - **Titre** : Nom du forfait (ex: "Forfait MCO Janvier 2026")
   - **Temps total** : Crédit temps au format `HH:mm` (ex: `30:00` pour 30h)
   - **Description** : Détails du forfait
4. Cliquez sur **"Enregistrer"**

> **💡 Astuce** : Le format de temps accepte les valeurs supérieures à 24h. Exemples valides : `02:30`, `30:00`, `100:15`

### 2️⃣ Créer une tâche

1. Accédez à **Gestion des tâches**
2. Cliquez sur **"Ajout nouvelle tâche"**
3. Remplissez le formulaire :
   - **Forfait** : Sélectionnez le forfait à imputer
   - **Nom** : Intitulé de la tâche
   - **Temps** : Durée de la tâche au format `HH:mm`
   - **Description** : Détails de l'intervention
4. Cliquez sur **"Enregistrer"**

> **⚠️ Important** : Le temps de la tâche ne peut pas dépasser le temps restant du forfait. Un message d'erreur s'affichera si c'est le cas.

### 3️⃣ Modifier un forfait

1. Dans **Gestion des forfaits**, cliquez sur l'icône **"Éditer"**
2. Modifiez les champs souhaités
3. Cliquez sur **"Enregistrer"**

> **⚠️ Note** : Si vous modifiez le temps total d'un forfait, toutes les tâches associées seront automatiquement désactivées (statut `current = 0`).

### 4️⃣ Supprimer une tâche

1. Dans **Gestion des tâches**, cliquez sur l'icône **"Supprimer"**
2. Confirmez la suppression

> **💡 Info** : Si la tâche était active (`current = 1`), son temps sera automatiquement recrédité au forfait.

## ⚙️ Configuration technique

### Structure de la base de données

Le module crée 4 tables SQL :

#### `ps_forfaits`
```sql
- id_psforfait    INT (PK, AUTO_INCREMENT)
- total_time      INT (temps en secondes)
- created_at      DATETIME
- updated_at      DATETIME
```

#### `ps_forfaits_lang`
```sql
- id_psforfait    INT (FK)
- id_lang         INT (FK)
- title           VARCHAR(255)
- description     TEXT
```

#### `ps_tasks`
```sql
- id_pstask       INT (PK, AUTO_INCREMENT)
- id_psforfait    INT (FK, ON DELETE CASCADE)
- total_time      INT (temps en secondes)
- current         TINYINT(1) (statut actif/inactif)
- created_at      DATETIME
- updated_at      DATETIME
```

#### `ps_tasks_lang`
```sql
- id_pstask       INT (FK)
- id_lang         INT (FK)
- title           VARCHAR(255)
- description     TEXT
```

### Architecture du code

```
ps_forfait_suivi/
├── classes/
│   ├── Forfaits.php              # Modèle ObjectModel pour les forfaits
│   └── Tasks.php                 # Modèle ObjectModel pour les tâches
├── controllers/admin/
│   ├── AdminForfaitController.php # Contrôleur admin des forfaits
│   └── AdminTaskController.php    # Contrôleur admin des tâches
├── views/
│   └── js/
│       └── task-time-input.js     # Script de validation côté client
├── config/
├── ps_forfait_suivi.php           # Fichier principal du module
├── logo.png                       # Logo du module
└── README.md                      # Documentation
```

### Sécurité implémentée

- ✅ **Protection SQL** : Utilisation de `pSQL()` et casting `(int)` sur toutes les requêtes
- ✅ **Validation des données** : Vérification du format de temps avec regex
- ✅ **Protection HTML** : `pSQL($value, true)` pour le contenu HTML
- ✅ **Clés étrangères** : Cascade de suppression entre forfaits et tâches
- ✅ **Contrôle métier** : Vérification du temps disponible avant création de tâche

## ❓ FAQ

### Le module supporte-t-il plusieurs forfaits simultanés ?
Oui, vous pouvez créer autant de forfaits que nécessaire. Chaque tâche est liée à un seul forfait.

### Que se passe-t-il si je supprime un forfait avec des tâches associées ?
Un message d'avertissement s'affiche. Les tâches sont supprimées automatiquement grâce à la contrainte `ON DELETE CASCADE`.

### Puis-je créer un forfait de plus de 24h ?
Oui ! Le module supporte les forfaits illimités. Exemples : `30:00`, `100:00`, `999:59`.

### Comment convertir des heures en format HH:mm ?
- 2h30 = `02:30`
- 30h = `30:00`
- 100h15min = `100:15`

### Le module est-il multilingue ?
Oui, le module utilise le système i18n de PrestaShop. Les titres et descriptions sont stockés pour chaque langue active.

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

### Standards de code
- Respectez les standards PrestaShop
- Utilisez PSR-2 pour le formatage PHP
- Commentez les fonctions complexes
- Testez sur PrestaShop 1.6 et 1.8

## 📄 Licence

Ce projet est sous licence **Academic Free License (AFL 3.0)**.

Voir le fichier [LICENSE](http://opensource.org/licenses/afl-3.0.php) pour plus de détails.

## 👤 Auteur

**Alexandre Celier - Mak2com**

- 🌐 Website: [Mak2com](https://www.mak2com.fr)
- 📧 Email: contact@mak2com.fr

---

<p align="center">
  Fait avec ❤️ pour la communauté PrestaShop
  <br>
  <sub>Si ce module vous est utile, n'hésitez pas à ⭐ le projet !</sub>
</p>
