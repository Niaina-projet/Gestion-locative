# Gestion Locative

Application web fullstack de gestion locative immobilière — Madagascar.

## Stack technique
- **Backend** : Symfony 7.2 / PHP 8.2
- **Frontend** : Angular 20
- **Base de données** : PostgreSQL 17 (Neon)
- **Auth** : JWT (LexikJWTBundle)
- **Email** : Symfony Mailer + Mailtrap (dev)

## Prérequis
- PHP 8.2+
- Composer 2+
- Node.js 20+ / npm
- Symfony CLI
- Angular CLI 20

## Installation

### Backend
```bash
cd backend
composer install
cp .env .env.local
# Configurer DATABASE_URL et MAILER_DSN dans .env.local
php bin/console doctrine:migrations:migrate
symfony server:start --no-tls
```

### Frontend
```bash
cd frontend
npm install
ng serve
```

## Qualité du code

### Backend
```bash
# PHP CS Fixer
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/php-cs-fixer fix

# PHPStan
vendor/bin/phpstan analyse
```

### Frontend
```bash
# ESLint
ng lint

# Prettier
npx prettier --check src/
npx prettier --write src/
```

## Comptes de démonstration
> À compléter lors du déploiement

## Déploiement
- **Frontend** : Vercel
- **Backend** : Render
- **Base de données** : Neon PostgreSQL