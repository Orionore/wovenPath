# Project Name

## Prerequisites

- Docker
- Docker Compose

## Tech Stack

- PHP 8.3
- PostgreSQL 16
- Nginx
- Node.js 20
- PNPM
- Symfony 7.2

## Quick Start with Docker

1. Clone the repository
```bash
git clone <repository-url>
cd <project-directory>
```

2. Create your environment file
```bash
cp .env .env.local
```

Configure your `.env.local` with these variables (adjust values as needed):
```env
POSTGRES_DB=app
POSTGRES_PASSWORD=!ChangeMe!
POSTGRES_USER=app
POSTGRES_VERSION=16
```

3. Start the Docker environment
```bash
docker compose up -d --build
```

4. Install dependencies
```bash
# Install PHP dependencies
docker compose exec php composer install

# Install JavaScript dependencies
docker compose exec php pnpm install
```

5. Set up the database
```bash
# Create database schema
docker compose exec php bin/console doctrine:schema:create

# Run migrations if any
docker compose exec php bin/console doctrine:migrations:migrate
```

6. Build assets
```bash
docker compose exec php pnpm run build
```

Your application should now be running at `http://localhost`

## Docker Services

### PHP Service
- PHP 8.3-FPM
- Composer
- Node.js 20
- PNPM
- Extensions: zip, pdo, pdo_pgsql

### Database Service
- PostgreSQL 16
- Persistent volume for data storage
- Health checks enabled

### Nginx Service
- Alpine-based
- Port 80 exposed
- Custom configuration for Symfony

## Development Commands

### Container Management
```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs -f

# Access PHP container
docker compose exec php bash

# Access Database
docker compose exec database psql -U app -d app
```

### Symfony Commands
```bash
# Clear cache
docker compose exec php bin/console cache:clear

# Create entity
docker compose exec php bin/console make:entity

# Create migration
docker compose exec php bin/console make:migration

# Run migrations
docker compose exec php bin/console doctrine:migrations:migrate
```

### Asset Management
```bash
# Watch assets for changes
docker compose exec php pnpm run watch

# Build for production
docker compose exec php pnpm run build
```

## Project Structure

```
.
├── docker/                 # Docker configuration
│   ├── nginx/             # Nginx configuration
│   │   └── default.conf
│   └── php/              # PHP configuration
│       ├── Dockerfile
│       └── php.ini
├── assets/               # Frontend assets
├── config/              # Symfony configuration
├── public/              # Web root
├── src/                 # PHP source code
├── templates/           # Twig templates
├── tests/               # Test files
└── compose.yaml         # Docker Compose configuration
```

## Database Access

- **Host**: database
- **Port**: 5432
- **Database**: ${POSTGRES_DB:-app}
- **User**: ${POSTGRES_USER:-app}
- **Password**: ${POSTGRES_PASSWORD:-!ChangeMe!}

## Troubleshooting

### Permission Issues
If you encounter permission issues:
```bash
docker compose exec php chown -R www-data:www-data var/
```

### Database Connection Issues
Make sure your DATABASE_URL in .env.local matches your Docker configuration:
```
DATABASE_URL="postgresql://${POSTGRES_USER:-app}:${POSTGRES_PASSWORD:-!ChangeMe!}@database:5432/${POSTGRES_DB:-app}?serverVersion=${POSTGRES_VERSION:-16}&charset=utf8"
```

### Cache Issues
Clear Symfony cache:
```bash
docker compose exec php bin/console cache:clear
```

## Production Deployment

For production deployment:

1. Adjust environment variables for production
2. Build production assets:
```bash
docker compose exec php pnpm run build
```

3. Ensure proper security headers in Nginx configuration
4. Configure SSL/TLS certificates
5. Update database credentials to production values

## Contributing

1. Create a new branch
2. Make your changes
3. Submit a pull request

## License

This project is proprietary software.