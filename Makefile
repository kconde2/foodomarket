.PHONY: up down restart status logs clean shell help

up: ## Démarre les services Docker pour le projet Symfony
	docker compose up -d --build

down: ## Arrête les services Docker pour le projet Symfony
	docker compose down

restart: down up ## Redémarre les services Docker pour le projet Symfony

status: ## Affiche le statut des services Docker pour le projet Symfony
	docker compose ps

logs: ## Affiche les logs des services Docker pour le projet Symfony
	docker compose logs -f

clean: ## Nettoie les volumes Docker pour le projet Symfony
	docker compose down -v

shell: ## Entre dans le conteneur app
	docker compose exec app bash

ccache: ## Entre dans le conteneur app
	docker compose exec app php bin/console cache:clear

console: ## Exécute une commande console dans le conteneur app
	docker compose exec app php bin/console $(filter-out $@,$(MAKECMDGOALS))

migrate: ## Exécute une commande migrate dans le conteneur app
	docker compose exec app php bin/console doctrine:migrations:migrate -n

fix: ## Corrige les erreurs de formattage
	docker compose exec app vendor/bin/php-cs-fixer fix --verbose --show-progress=dots

consume: ## Corrige les erreurs de formattage
	docker compose exec app php bin/console messenger:consume async -vv

tests: ## Lance les tests
	docker compose exec app php bin/phpunit

init: ## Exécute une commande pour initialiser toutes les données du projet
	docker compose exec app bin/console doctrine:database:drop --force --if-exists --env=test
	docker compose exec app bin/console doctrine:database:create --env=test
	docker compose exec app bin/console doctrine:migrations:migrate --no-interaction --env=test
	docker compose exec app bin/console doctrine:database:drop --force --if-exists
	docker compose exec app bin/console doctrine:database:create
	docker compose exec app bin/console doctrine:migrations:migrate --no-interaction

help: ## Affiche les commandes disponibles
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

# Pour éviter les erreurs "No rule to make target"
%:
	@:
