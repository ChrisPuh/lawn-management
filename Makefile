# Default shell with strict error handling
SHELL := /bin/bash
.SHELLFLAGS := -eu -o pipefail -c

# Environment variables
export NODE_ENV ?= development
export COMPOSE_PROJECT_NAME ?= lawn-management
TEST_FILTER ?=

# Validation checks
REQUIRED_BINS := npm php composer
$(foreach bin,$(REQUIRED_BINS),\
    $(if $(shell command -v $(bin) 2> /dev/null),,$(error Please install `$(bin)`)))

# Safety checks
guard-root:
	@if [ $$(id -u) = 0 ]; then \
		echo "Error: Don't run make tasks as root/sudo" >&2; \
		exit 1; \
	fi

guard-php-version:
	@if ! php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);'; then \
		echo "Error: PHP 8.2 or higher is required" >&2; \
		exit 1; \
	fi

guard-all: guard-root guard-php-version

# Ensure .env exists
.env:
	@if [ ! -f .env ]; then \
		echo "Creating .env file from .env.example"; \
		cp .env.example .env; \
	fi

# Development commands
dev: guard-all .env
	npm run dev

watch: guard-all .env
	npm run watch

install: guard-all .env
	@echo "Installing dependencies..."
	composer install --no-interaction --no-scripts
	npm ci
	php artisan key:generate --force
	php artisan storage:link

serve: guard-all .env
	php artisan serve --port=8000

# Test commands
test: guard-all .env
	@if [ -n "$(TEST_FILTER)" ]; then \
		echo "Running filtered tests: $(TEST_FILTER)"; \
		php artisan test $(TEST_FILTER); \
	else \
		echo "Running all tests"; \
		php artisan test; \
	fi

test-filter:
	@echo "Enter test path or filter (e.g., Tests/Unit/Traits/CanGetTableNameStaticallyTest.php):" && \
	read -r filter && \
	$(MAKE) test TEST_FILTER="$$filter"

test-parallel: guard-all .env
	@if ! composer show brianium/paratest >/dev/null 2>&1; then \
		echo "Installing paratest..."; \
		composer require --dev brianium/paratest; \
	fi
	@if [ -n "$(TEST_FILTER)" ]; then \
		echo "Running filtered tests in parallel: $(TEST_FILTER)"; \
		php artisan test $(TEST_FILTER) --parallel; \
	else \
		echo "Running all tests in parallel"; \
		php artisan test --parallel; \
	fi

# Maintenance commands
clean:
	@echo "Cleaning caches..."
	rm -rf node_modules/.cache
	rm -rf bootstrap/cache/*.php
	@composer clear-cache

autoload: guard-all
	composer dump-autoload -o

route: guard-all
	php artisan route:list --columns=Name --columns=Action --columns=Uri

ide-helper: guard-all
	@echo "Generating IDE helpers..."
	@for file in .phpstorm.meta.php _ide_helper.php _ide_helper_models.php; do \
		[ -f $$file ] && cp $$file $$file.bak || true; \
	done
	php artisan ide-helper:meta
	php artisan ide-helper:generate
	php artisan ide-helper:models --nowrite

migrate-refresh: guard-all
	@echo "Warning: This will reset the database. Are you sure? [y/N] " && read ans && [ $${ans:-N} = y ]
	php artisan migrate:refresh --seed

analyse: guard-all
	./vendor/bin/psalm --show-info=true

# Help commands
test-help:
	@echo "Test commands:"
	@echo "  make test                    - Run tests sequentially"
	@echo "  make test-parallel           - Run tests in parallel (requires paratest)"
	@echo "  make test TEST_FILTER=<path> - Run specific tests"
	@echo "  make test-filter             - Interactive prompt for test filter"
	@echo ""
	@echo "Examples:"
	@echo "  make test TEST_FILTER=\"Tests/Unit/Traits/CanGetTableNameStaticallyTest.php\""
	@echo "  make test TEST_FILTER=\"--filter=test_get_table_name_returns_correct_name\""

help: test-help
	@echo "Available commands:"
	@echo "  make dev              - Start development server"
	@echo "  make watch           - Watch assets for changes"
	@echo "  make install         - Install dependencies"
	@echo "  make test            - Run tests sequentially"
	@echo "  make test-parallel   - Run tests in parallel"
	@echo "  make autoload        - Optimize autoloader"
	@echo "  make route           - List routes"
	@echo "  make ide-helper      - Generate IDE helpers"
	@echo "  make migrate-refresh - Refresh database"
	@echo "  make analyse         - Run static analysis"
	@echo "  make serve           - Start development server"
	@echo "  make clean           - Clean caches"

# Mark all targets as PHONY
.PHONY: guard-root guard-php-version guard-all dev watch install serve \
        test test-filter test-parallel clean autoload route ide-helper \
        migrate-refresh analyse test-help help
