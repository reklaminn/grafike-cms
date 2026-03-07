#!/bin/sh
# ═══════════════════════════════════════════════
# Grafike CMS - Docker Entrypoint
# ═══════════════════════════════════════════════

set -e

echo "🚀 Starting Grafike CMS..."

# ─── Generate Key if Missing ─────────────────
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "⚙️  Generating application key..."
    php artisan key:generate --force
fi

# ─── Run Migrations ──────────────────────────
echo "📦 Running database migrations..."
php artisan migrate --force 2>/dev/null || echo "⚠️  Migration skipped (database may not be ready)"

# ─── Storage Link ────────────────────────────
echo "🔗 Ensuring storage link..."
php artisan storage:link 2>/dev/null || true

# ─── CMS Optimization ───────────────────────
echo "⚡ Optimizing CMS..."
php artisan cms:optimize 2>/dev/null || echo "⚠️  CMS optimization skipped"

# ─── Permissions ─────────────────────────────
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── Create log directory for supervisor ─────
mkdir -p /var/log/supervisor

# ─── Health Check ────────────────────────────
echo "🏥 Running health check..."
php artisan cms:health || echo "⚠️  Health check reported issues"

echo "✅ Grafike CMS is ready!"
echo ""

# Execute the CMD
exec "$@"
