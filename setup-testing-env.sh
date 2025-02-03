#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function for formatted output
log() {
    local type=$1
    local message=$2
    local emoji=$3
    echo -e "${emoji} ${type}: ${message}"
}

info() {
    log "INFO" "$1" "💡"
}

success() {
    log "SUCCESS" "$1" "✅"
}

error() {
    log "ERROR" "$1" "❌"
}

# Path to the environment files
EXAMPLE_ENV=".env.testing.example"
TESTING_ENV=".env.testing"
MAIN_ENV=".env"

# Print header
echo "
🚀 Setting up Testing Environment
================================"

# List all available files
info "Available files in directory:"
ls -la *.env* 2>/dev/null

# Ensure the example file exists
if [ ! -f "$EXAMPLE_ENV" ]; then
    error "$EXAMPLE_ENV not found!"
    exit 1
fi

# Create both .env and .env.testing
info "Creating environment files from $EXAMPLE_ENV..."
cp "$EXAMPLE_ENV" "$TESTING_ENV"
cp "$EXAMPLE_ENV" "$MAIN_ENV"

# Add Docker specific settings
info "Adding Docker configuration..."
cat >> "$MAIN_ENV" << EOL

# Docker Settings
WWWGROUP=${WWWGROUP:-1000}
WWWUSER=${WWWUSER:-1000}

# Database Settings
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=${DB_DATABASE:-testing}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-password}

# Redis Settings
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Settings
MAIL_MAILER=log
EOL

# Copy Docker settings to testing env
cat "$MAIN_ENV" > "$TESTING_ENV"

# Set permissions
info "Setting file permissions..."
chmod 600 "$TESTING_ENV" 2>/dev/null
chmod 600 "$MAIN_ENV" 2>/dev/null

# Print configuration
info "Environment files created. Contents of $MAIN_ENV:"
echo "----------------------------------------"
cat "$MAIN_ENV"
echo "----------------------------------------"

success "Testing environment setup completed successfully!"
