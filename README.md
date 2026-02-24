## Lancer le projet
1. Installer les dépendances
`composer i`

2. Lancer le projet Docker
`docker compose up --build -d`

3. Exécuter les migrations :
`docker compose exec web php migrate.php`

## Run le linter en local
`composer phpstan`
