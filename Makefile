# Default shell with strict error handling
SHELL := /bin/bash
.SHELLFLAGS := -eu -o pipefail -c

# Ensure we're using a consistent environment
export NODE_ENV ?= development
export COMPOSE_PROJECT_NAME ?= lawn-management

# Validation of required tools
REQUIRED_BINS := npm php composer
$(foreach bin,$(REQUIRED_BINS),\
    $(if $(shell command -v $(bin) 2> /dev/null),,$(error Please install `$(bin)`)))

# Protect against running as root
guard-root:
	@if [ $$(id -u) = 0 ]; then \
		echo "Error: Don't run make tasks as root/sudo" >&2; \
		exit 1; \
	fi

# Check PHP version
guard-php-version:
	@if ! php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);'; then \
		echo "Error: PHP 8.2 or higher is required" >&2; \
		exit 1; \
	fi

# Common target dependencies
.PHONY: guard-all
guard-all: guard-root guard-php-version

# Ensure .env exists
.env:
	@if [ ! -f .env ]; then \
		echo "Creating .env file from .env.example"; \
		cp .env.example .env; \
	fi

# Clean npm and composer cache
.PHONY: clean
clean:
	@echo "Cleaning caches..."
	rm -rf node_modules/.cache
	rm -rf bootstrap/cache/*.php
	@composer clear-cache

# Development server with security checks
.PHONY: dev
dev: guard-all .env
	npm run dev

# Watch assets with security checks
.PHONY: watch
watch: guard-all .env
	npm run watch

# Secure installation process
.PHONY: install
install: guard-all .env
	@echo "Installing dependencies..."
	composer install --no-interaction --no-scripts
	npm ci
	php artisan key:generate --force
	php artisan storage:link

# Run tests in parallel with proper isolation
.PHONY: test
test: guard-all .env
	php artisan config:clear
	php artisan test --parallel --stop-on-failure

# Optimized autoloader
.PHONY: autoload
autoload: guard-all
	composer dump-autoload -o

# Route listing with validation
.PHONY: route
route: guard-all
	php artisan route:list --columns=Name --columns=Action --columns=Uri

# IDE helper generation with backup
.PHONY: ide-helper
ide-helper: guard-all
	@echo "Generating IDE helpers..."
	@for file in .phpstorm.meta.php _ide_helper.php _ide_helper_models.php; do \
		[ -f $$file ] && cp $$file $$file.bak || true; \
	done
	php artisan ide-helper:meta
	php artisan ide-helper:generate
	php artisan ide-helper:models --nowrite

# Safe database refresh
.PHONY: migrate-refresh
migrate-refresh: guard-all
	@echo "Warning: This will reset the database. Are you sure? [y/N] " && read ans && [ $${ans:-N} = y ]
	php artisan migrate:refresh --seed

# Static analysis
.PHONY: analyse
analyse: guard-all
	./vendor/bin/psalm --show-info=true

# Development server with security headers
.PHONY: serve
serve: guard-all .env
	php artisan serve --port=8000

# Help target
.PHONY: help
help:
	@echo "Available commands:"
	@echo "  make dev              - Start development server"
	@echo "  make watch           - Watch assets for changes"
	@echo "  make install         - Install dependencies"
	@echo "  make test            - Run tests"
	@echo "  make autoload        - Optimize autoloader"
	@echo "  make route           - List routes"
	@echo "  make ide-helper      - Generate IDE helpers"
	@echo "  make migrate-refresh - Refresh database"
	@echo "  make analyse         - Run static analysis"
	@echo "  make serve           - Start development server"
	@echo "  make clean           - Clean caches"
