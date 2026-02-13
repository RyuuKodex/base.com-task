# Baselinker Integration Module

## About The Project

Integration module for helpdesk system that connects to Baselinker API, allowing to fetch and process orders from multiple marketplaces (Allegro, Amazon, eBay, etc.).

### Features

- **Baselinker API Integration**: Fetching orders and detailed order data.
- **Marketplace Specific Processing**: Strategy pattern for handling different marketplace data (Allegro, Amazon, eBay).
- **Queued Processing**: Using Symfony Messenger for scalable order synchronization.
- **Monitoring & Logging**: Performance tracking (request duration) and error logging via Decorator pattern.
- **Filtering**: Ability to list and filter processed orders via API.

### Built With

- [PHP 8.3](https://www.php.net/)
- [Symfony 8.0](https://symfony.com/)
- [MySQL 8.2](https://www.mysql.com/)
- [Docker](https://www.docker.com/)
- [Messenger](https://symfony.com/doc/current/messenger.html)

## Getting Started

### Installation

Follow these simple steps

#### Clone repository

```bash
git clone git@github.com:RyuuKodex/base.com---task.git
```

## Building environment for development

First things first you have to copy the development environment template. It's located in `.devcontainer`, I'd recommend
to leave it there and create a symbolic link.

```shell
ln -s ./etc/envs/compose.dev.yaml .
mv compose.dev.yaml compose.override.yaml
```

Now we'll use `docker` to build our image locally, with local architecture:

```shell
docker compose build
```

It may take few seconds, when it's completed proceed with running the container:

```shell
docker compose up --detach
```

Remember that you have installed the vendors in an image, however while creating container you've replaced built app
folder with empty one (repository has no `vendor` folder intentionally). So, we have to proceed once again with app
configuration:

```shell
docker compose exec app bash -ce "
    composer install
    chown -R $(id -u):$(id -g) .
  "
```

Configure your `.env` file with Baselinker API token:

```bash
# app/.env
BASELINKER_API_TOKEN=your_token_here
```

Now you're all set, you can visit [localhost](http://localhost), you should
see the Symfony default application web page.

# Endpoints & Commands

### API Endpoints

#### List Orders
```http
  GET /api/orders?marketplace=<marketplace>&limit=<limit>&offset=<offset>

  Parameters:
  - marketplace (optional): allegro, amazon, ebay, other
  - limit (optional): default 10
  - offset (optional): default 0
```

### Console Commands

#### Database Migrations
Runs all pending database migrations.
```bash
docker compose exec app bin/console d:m:m
```

#### Synchronize Orders
Synchronizes all orders from Baselinker API and puts them into the processing queue.
```bash
docker compose exec app bin/console app:orders:sync
```

## Maintenance Commands

#### Start the project
```bash
docker compose up -d
```

#### Connect to app container
```bash
docker compose exec app bash
```

#### Stop project
```bash
docker compose down --remove-orphans
```

#### CS-fixer
```bash
docker compose exec app composer run-lint-fix
```

#### PHP-Stan
```bash
docker compose exec app composer run-phpstan-analysis
```

### Testing

#### Unit Tests
```bash
docker compose exec app composer run-unit-tests
```

#### Integration Tests
```bash
docker compose exec app composer run-integration-tests
```

#### Functional Tests
```bash
docker compose exec app composer run-functional-tests
```

#### Run All Tests
```bash
docker compose exec app composer run-all-tests
```
