#!/bin/bash

# Path to the example and actual testing environment files
EXAMPLE_ENV=".env.testing.example"
ACTUAL_ENV=".env.testing"

# Ensure the example file exists
if [ ! -f "$EXAMPLE_ENV" ]; then
    echo "Error: $EXAMPLE_ENV not found"
    exit 1
fi

# Create .env.testing if it doesn't exist
if [ ! -f "$ACTUAL_ENV" ]; then
    echo "Creating .env.testing from .env.testing.example..."
    cp "$EXAMPLE_ENV" "$ACTUAL_ENV"
fi

# Generate app key
echo "Generating test application key..."
php artisan key:generate --env=testing

# Set permissions
chmod 600 "$ACTUAL_ENV" 2>/dev/null

echo "Testing environment setup complete!"
