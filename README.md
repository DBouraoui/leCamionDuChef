# Le Camion du Chef - Site Web

Application web pour le foodtruck "Le Camion du Chef" réalisée avec Symfony, Twig et Tailwind CSS.

## 📋 Description

Ce site web permet aux clients du foodtruck "Le Camion du Chef" de :
- Consulter le menu et les spécialités du jour
- Voir les jours d'ouvertures et les emplacements du Foodtruck
- Réserver des plats à l'avance
- Suivre les actualités et promotions
- Contacter l'équipe

## 🔧 Technologies utilisées

- **[Symfony 6.4](https://symfony.com/)** - Framework PHP
- **[Twig](https://twig.symfony.com/)** - Moteur de templates
- **[Tailwind CSS 3.3](https://tailwindcss.com/)** - Framework CSS
- **[MySQL](https://www.mysql.com/)** - Base de données
- **[Doctrine ORM](https://www.doctrine-project.org/)** - ORM pour la gestion des données
- **[Webpack Encore](https://symfony.com/doc/current/frontend.html)** - Gestion des assets

## 📦 Prérequis

- PHP 8.1 ou supérieur
- Composer
- Node.js (v16+) et npm
- MySQL ou MariaDB

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/votreutilisateur/lecamionduchef.git
cd le-camion-du-chef
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Configurer la base de données

Créez un fichier `.env.local` à la racine du projet et configurez votre connexion à la base de données :

```
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/lecamionduchef?serverVersion=8.0"
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Charger les fixtures (données de démonstration)

```bash
php bin/console doctrine:fixtures:load
```

### 7. Compiler les assets

```bash
npm run build
```

### 8. Lancer le serveur de développement

```bash
symfony server:start
```

Le site sera accessible à l'adresse : [http://localhost:8000](http://localhost:8000)

## 📁 Structure du projet

```
le-camion-du-chef/
├── assets/                  # Assets frontend (CSS, JS, images)
│   ├── controllers/         # Contrôleurs JavaScript
│   ├── styles/              # Fichiers CSS et Tailwind
│   └── images/              # Images
├── config/                  # Configurations Symfony
├── migrations/              # Migrations de base de données
├── public/                  # Fichiers publics
├── src/                     # Code source PHP
│   ├── Controller/          # Contrôleurs
│   ├── Entity/              # Entités Doctrine
│   ├── Form/                # Formulaires
│   ├── Repository/          # Repositories
│   └── Service/             # Services
├── templates/               # Templates Twig
│   ├── base.html.twig       # Template de base
│   ├── menu/                # Templates pour le menu
│   ├── reservation/         # Templates pour les réservations
│   └── ...                  # Autres templates
└── ...
```

## 🔄 Développement

### Compiler les assets en temps réel

```bash
npm run watch
```

### Vider le cache

```bash
php bin/console cache:clear
```

### Créer une entité ou une migration

```bash
php bin/console make:entity
php bin/console make:migration
```

## 🌐 Déploiement en production

1. Configurez votre serveur web (Nginx/Apache)
2. Clonez le dépôt sur votre serveur
3. Installez les dépendances en mode production :

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

4. Configurez votre fichier `.env.local` avec les paramètres de production
5. Exécutez les migrations :

```bash
php bin/console doctrine:migrations:migrate --env=prod
```

## 🔒 Fonctionnalités administrateur

Un panneau d'administration est disponible à l'adresse `/admin` avec les fonctionnalités suivantes :
- Gestion du menu et des plats
- Gestion des emplacements et horaires
- Suivi des réservations
- Gestion des actualités et promotions

## 📱 Responsive Design

Le site est entièrement responsive grâce à Tailwind CSS et s'adapte à tous les appareils (desktop, tablette, mobile).

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## Visité le site web

lecamionduchef.fr
