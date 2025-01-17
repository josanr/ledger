# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(DOCKER_COMP) exec -u 1000 php bash

## —— Tests ————————————————————————————————————————————————————————————————
test_unit:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/codecept run Unit

test_api:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console doctrine:database:create || true
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console doctrine:migrations:migrate --no-interaction || true
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/codecept run Api


## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer
