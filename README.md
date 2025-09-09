# Réserva'Classe 2.0 : Gestion des créneaux parents-enseignants et activités scolaires

![Screenshot de l'application ReservaClasse](screenshot.webp)

Application web développée avec Symfony 6 pour la gestion flexible des rencontres parents-enseignants et des activités scolaires nécessitant des accompagnateurs. Cette version 2.0 apporte une refonte complète avec une meilleure gestion des utilisateurs, une interface moderne et des cas d'utilisation étendus.

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
  * TEACHER : Gestion des sessions et des élèves par l'enseignant
  * USER : Réservation par mot de passe/envoi de notifications (fonctionnalité à venir)
* Interface d'administration sécurisée (EasyAdmin)
* Validation stricte des mots de passe

### Gestion des sessions et créneaux

* Sessions personnalisables par l'enseignant
* Configuration flexible (durée, intervalle, dates multiples)
* Visualisation du taux d'occupation

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

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/reservaclasse_v2"
```

4. Créer la base de données et les tables

```bash
symfony console doctrine:database:create
symfony console doctrine:schema:create
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
* 5 sessions avec dates et créneaux
* Configuration des durées et intervalles aléatoires

7. Après le chargement des fixtures, vous pouvez vous connecter avec :

* Admin : <admin@test.com> / Password123
* Enseignant : <teacher@test.com> / Password123

### Déploiement en PRODUCTION

1. Configurer les variables d'environnement

```bash
APP_ENV=prod
APP_SECRET=votre_secret
DATABASE_URL="mysql://user:password@127.0.0.1:3306/reservaclasse"
```

2. Optimiser l'application

```bash
composer install --no-dev --optimize-autoloader
symfony console cache:clear
symfony console cache:warmup
```

3. Si besoin : pour supprimer les données de test

```bash
symfony console doctrine:database:drop --force
```

4. Pour réinitialiser l'application

```bash
symfony console doctrine:database:create
symfony console doctrine:schema:create
```

5. Mettre à jour la base de données

```bash
# Appliquer les migrations
symfony console doctrine:migrations:migrate --env=prod --no-interaction
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

Note : Cette commande est sécurisée et validera toutes les entrées avant la création du compte.

#### Configuration du serveur web (Apache)

Si vous déployez l'application sur un serveur Apache, vous devez créer un fichier de configuration pour que votre application soit accessible via un navigateur. Voici comment faire :

1. Connectez-vous à votre serveur en SSH.

2. Créez un nouveau fichier de configuration :

    ```bash
    sudo nano /etc/apache2/sites-available/reservaclasse.conf
    ```

3. Copiez cette configuration dans le fichier :

    ```apache
    <VirtualHost *:80>
        # Configuration de base
        ServerName reservaclasse.domaine.com
        DocumentRoot /var/www/reservaclasse/public

        # Règles du répertoire
        <Directory /var/www/reservaclasse/public>
            AllowOverride All
            Require all granted
            FallbackResource /index.php
        </Directory>

        # Logs
        ErrorLog ${APACHE_LOG_DIR}/reservaclasse_error.log
        CustomLog ${APACHE_LOG_DIR}/reservaclasse_access.log combined

        # Headers de sécurité
        Header set X-Content-Type-Options "nosniff"
        Header set X-Frame-Options "SAMEORIGIN"
        Header set X-XSS-Protection "1; mode=block"

        # Compression GZIP
        AddOutputFilterByType DEFLATE text/html text/css application/javascript

        # Cache navigateur
        <FilesMatch "\.(jpg|jpeg|png|gif|js|css)$">
            Header set Cache-Control "max-age=2592000, public"
        </FilesMatch>

        # Protection supplémentaire
        Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
        Header set Content-Security-Policy "default-src 'self'"
    </VirtualHost>
    ```

4. Activez la configuration et redémarrez Apache :

    ```bash
    # Activer le site
    sudo a2ensite reservaclasse.conf

    # Activer les modules nécessaires
    sudo a2enmod rewrite
    sudo a2enmod headers

    # Redémarrer Apache pour appliquer les changements
    sudo systemctl restart apache2
    ```

Cette configuration permet à Apache de :

* Servir votre application sur votre nom de domaine
* Diriger tout le trafic vers le dossier public de Symfony
* Gérer les URL propres de Symfony
* Enregistrer les erreurs dans des fichiers de log séparés

> **Note** : Cette configuration n'est nécessaire que si vous déployez sur un serveur Apache. Si vous utilisez un autre serveur web (Nginx, par exemple) ou une plateforme d'hébergement gérée, la configuration sera différente.

## Contributions

Les contributions sont les bienvenues ! Vous pouvez :

* Signaler des bugs via les Issues GitHub
* Proposer des améliorations
* Soumettre des pull requests
* Partager vos retours d'expérience

## License

Ce projet est sous licence MIT.
