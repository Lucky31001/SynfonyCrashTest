# Variables
DOCKER_COMPOSE=docker compose
PHP_CONTAINER=php
DB_CONTAINER=db
PROJECT_DIR=app

install:
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) composer install

# Commandes Docker
run:
	$(DOCKER_COMPOSE) run --rm php bin/console doctrine:database:create ||true
	#$(DOCKER_COMPOSE) run --rm php sh -c "./wait-for-it.sh -t 30 database:5432 && bin/console doctrine:migrations:migrate --no-interaction"
	$(DOCKER_COMPOSE) up --remove-orphans -d

fixtures:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction

migration:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) php bin/console doctrine:migrations:diff

migrate:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

down:
	$(DOCKER_COMPOSE) down -v

stop:
	$(DOCKER_COMPOSE) stop

restart:
	$(DOCKER_COMPOSE) restart

# Connexion au conteneur PHP
cli:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) bash

# Commandes Symfony
sf:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) php bin/console $(cmd)

logs:
	$(DOCKER_COMPOSE) logs -f

version:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONTAINER) php bin/console about

prettier:
	docker run -v ${PWD}:/code ghcr.io/php-cs-fixer/php-cs-fixer:3.48-php8.2 fix -- ./src
	docker run -v ${PWD}:/code ghcr.io/php-cs-fixer/php-cs-fixer:3.48-php8.2 fix -- ./templates


ps:
	$(DOCKER_COMPOSE) ps

restart:
	$(DOCKER_COMPOSE) restart