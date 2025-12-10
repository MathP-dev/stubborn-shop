# Stubborn Shop

Application e-commerce développée avec Symfony 7.

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- Node.js et Yarn
- MySQL 8.0 ou supérieur
- Extension PHP : `pdo_mysql`, `intl`, `zip`, `gd`

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/MathP-dev/stubborn-shop.git
cd stubborn-shop
```

### 2. Installer les dépendances PHP

```bash
composer install
yarn install
yarn encore dev
```

### 3. Configurer la base de données
Créer une base de données MySQL nommée `stubborn_shop` (ou un autre nom de votre choix).
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

```
Configurer la connexion à la base de données dans le fichier `.env && .env.local` 

### 4. Charger les données de test 

```bash
php bin/console doctrine:fixtures:load
```

Accès : /admin 
Identifiants par défaut (après fixtures) :
Email : admin@example.com
Mot de passe : admin123

### 5. Lancer le serveur de développement

```bash
symfony server:start || symfony serve || php -S localhost:8000 -t public
```

### 6. Configuration Stripe
1. Créez un compte sur Stripe
2. Récupérez vos clés API (mode test)
3. Ajoutez-les dans .env.local

### 7. Tests

```bash
php bin/phpunit
```

