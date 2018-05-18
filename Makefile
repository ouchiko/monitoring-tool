build: ## Builds docker images from the current project files
	docker-compose build

dev: ## Creates and starts the docker containers with development settings
	docker-compose -f docker-compose.yml -f docker-development.yml up

down:
	docker-compose down

prod:
	docker-compose -f docker-compose.yml -f docker-production.yml up -d
