# Réserva'Classe 2.0 : Gestion des créneaux parents-enseignants lors d'activités scolaires

![Screenshot de l'application ReservaClasse](screenshot.webp)

Application web développée avec Symfony 7.3 pour la gestion d'inscription de parents d'élèves lors d'évènements scolaires. Cette version 2.0 apporte une refonte complète avec une meilleure gestion des utilisateurs, une interface moderne et des cas d'utilisation étendus.

## Évolution du projet

### Version 1.0

La première version est disponible sur GitHub : [ReservaClasse v1](https://github.com/Alex-Web-Github/ReservaClasse.git)

Caractéristiques de la v1 :

* Application mono-utilisateur (1 seul enseignant)
* Interface simple de réservation
* Gestion basique des créneaux
* Pas d'espace d'administration

### Version 2.0 (actuelle)

Améliorations majeures :

* Support multi-utilisateurs avec rôles
* Interface d'administration complète
* Configuration flexible des créneaux
* Design moderne avec Tailwind et daisyUI
* Structure de code optimisée
* Nouvelles fonctionnalités (import d'élèves, gestion des dépendances...)

## Cas d'utilisation

### Réunions Parents-Enseignants

* Gestion des créneaux multiples sur plusieurs jours
* Configuration flexible des durées d'entretien (15, 20, 30 minutes...)
* Possibilité d'ajouter des pauses entre les créneaux
* Visualisation en temps réel des disponibilités
* Interface simplifiée pour les réservations

### Sorties Scolaires

* Gestion des besoins en accompagnateurs (piscine, musée, etc.)
* Un créneau unique par jour avec places limitées

## Fonctionnalités techniques

### Gestion des utilisateurs

* Support multi-utilisateurs avec différents rôles :
  * ADMIN : Gestion complète de l'application au niveau d'une Établissement
  * TEACHER : Gestion des evènements et des élèves par l'enseignant
  * USER : Réservation par mot de passe/envoi de notifications (fonctionnalité à venir)
* Interface d'administration sécurisée (EasyAdmin)
* Validation stricte des mots de passe

### Gestion des evènements et créneaux

* Evènements paramétrables par l'enseignant (durée, intervalle entre les rendez-vous, dates multiples)
* Visualisation du taux d'occupation (places disponibles restantes)

### Gestion des élèves

* Import d'une liste d'élèves
* Suppression intelligente (gestion des dépendances)
* Suivi des réservations par élève

### Interface utilisateur moderne

* Design responsive avec Tailwind CSS v4
* Bibliothèque de composants daisyUI
* Messages flash contextuels
* Tableaux de bord adaptés aux rôles

## Installation

### Prérequis

* PHP 8.1 ou supérieur

* Composer
* MySQL/MariaDB
* Symfony CLI (recommandé)

### Installation en LOCAL

1. Cloner le projet

```bash
git clone https://github.com/votre-repo/ReservaClasse_v2.git
cd ReservaClasse
```

2. Installer les dépendances

```bash
composer install
```

3. Configurer la base de données dans .env.local

```.env.local
DATABASE_URL="mysql://user:password@127.0.0.1:3306/reservaclasse"
```

4. Configurer la base de données

```bash
# Création de la base
symfony console doctrine:database:create

# Validation du schéma (cohérence entre vos entités et la base de données)
symfony console doctrine:schema:validate

# Si le schéma est invalide -> générer une migration puis appliquer les migrations
symfony console make:migration
symfony console doctrine:migrations:migrate
```

5. Charger les fixtures pour tester

```bash
symfony console doctrine:fixtures:load
```

6. Utilisation des fixtures

Les fixtures permettent de peupler la base de données avec des données de test :

* Un compte administrateur
* Un compte enseignant
* 10 élèves de test
* 5 évènements avec dates et créneaux
* Configuration des durées et intervalles aléatoires

7. Après le chargement des fixtures, vous pouvez vous connecter avec :

* Admin : <admin@test.com> / Password123
* Enseignant : <teacher@test.com> / Password123

### Déploiement en PRODUCTION

> ⚠️ **Note** : Si Symfony CLI n'est pas installé, remplacez `symfony console` par `php bin/console`

#### Première installation

> ⚠️ Pour importer le projet depuis le dépôt distant en SSH (exemple avec GitHub) :

```bash
git clone git@github.com:votre-repo/ReservaClasse_v2.git
cd ReservaClasse_v2
```

> ⚠️ Pour actualiser les versions du projet, pensez à exécuter les commandes suivantes :

```bash
git pull
composer install --no-dev --optimize-autoloader
php bin/console asset-map:compile
```

1. Configuration de l'environnement

```bash
# Créer et configurer le fichier .env.local
APP_ENV=prod
APP_SECRET=votre_secret_securise
DATABASE_URL="mysql://user:password@127.0.0.1:3306/reservaclasse"
```

2. Installation des dépendances

```bash
# Installation optimisée des dépendances
composer install --no-dev --optimize-autoloader

# Optimisation supplémentaire de l'autoloader
composer dump-autoload --optimize --no-dev --classmap-authoritative
```

3. Initialisation de la base de données

```bash
# Création de la base
php bin/console doctrine:database:create --env=prod

# Application des migrations
php bin/console doctrine:migrations:migrate --env=prod --no-interaction
```

4. Configuration du cache

```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

#### Mises à jour ultérieures

> ⚠️ Pour les mises à jour suivantes, commencer par sauvegarder la base :

```bash
mysqldump -u user -p reservaclasse > backup_$(date +%Y%m%d).sql
```

6. Création d'un utilisateur ADMIN

Pour créer votre premier administrateur en production, utilisez la commande interactive :

```bash
symfony console app:create-admin
```

La commande vous demandera de saisir :

* Prénom de l'administrateur
* Nom de l'administrateur
* Email
* Mot de passe (minimum 8 caractères, avec majuscule, minuscule et chiffre)
* Code public (optionnel)

Exemple d'utilisation :

```bash
symfony console app:create-admin

Prénom de l'administrateur: John
Nom de l'administrateur: Doe
Email de l'administrateur: admin@ecole.fr
Mot de passe: 
Code public (optionnel): ADMIN2025

[OK] Compte administrateur créé avec succès !
```

Note : Cette commande validera toutes les entrées avant la création du compte.

#### Configuration du serveur mutualisé (exemple: o2switch)

Si vous déployez l'application sur un serveur mutualisé, le "Build des assets" avec Tailwind ne semble pas fonctionner correctement... Une solution simple consiste à importer les "/assets" en manuel.

1. Compiler les 'assets' sur la version LOCALE

```bash
php bin/console tailwind:build --minify
php bin/console asset-map:compile
```

2. Connectez-vous à votre serveur en FTP puis transférez le contenu (dossier compressé) du répertoire '/public/assets/' vers le serveur, au même emplacement

3. Mettre à jour l'application

```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
composer dump-autoload --optimize --no-dev --classmap-authoritative
```

## Contributions

Les contributions sont les bienvenues ! Vous pouvez :

* Signaler des bugs via les Issues GitHub
* Proposer des améliorations
* Soumettre des pull requests
* Partager vos retours d'expérience

## License

Ce projet est sous licence MIT.
