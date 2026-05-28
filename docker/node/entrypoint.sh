#!/bin/sh
set -e

cd /var/www/html

if [ ! -d node_modules ] || [ ! -f node_modules/.install-stamp ]; then
    echo "Installing npm dependencies..."
    npm ci
    touch node_modules/.install-stamp
elif [ package-lock.json -nt node_modules/.install-stamp ]; then
    echo "package-lock.json changed, updating dependencies..."
    npm ci
    touch node_modules/.install-stamp
fi

echo "Starting Vite dev server on http://localhost:5173"
exec npm run dev
