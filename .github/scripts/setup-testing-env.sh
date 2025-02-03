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

info "Creating environment files..."

# Create base .env file
cat > .env << EOL
APP_NAME="Lawn Management (Test)"
APP_ENV=testing
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=${DB_DATABASE:-testing}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-password}

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync

MAIL_MAILER=array

WWWGROUP=${WWWGROUP:-1000}
WWWUSER=${WWWUSER:-1000}
EOL

# Copy to .env.testing
cp .env .env.testing

success "Environment files created successfully!"
info "Contents of .env:"
echo "----------------------------------------"
cat .env
echo "----------------------------------------"
EOL
