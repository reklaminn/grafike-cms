#!/bin/bash
# ═══════════════════════════════════════════════════════════
# IRASPA CMS - Production Deployment Script
# ═══════════════════════════════════════════════════════════
set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo -e "${BLUE}  IRASPA CMS - Deployment Script${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo ""

# ─── Configuration ─────────────────────────────────────
APP_DIR=$(cd "$(dirname "$0")" && pwd)
PHP_BIN=${PHP_BIN:-php}
COMPOSER_BIN=${COMPOSER_BIN:-composer}
NPM_BIN=${NPM_BIN:-npm}

# ─── Functions ─────────────────────────────────────────
step() {
    echo -e "\n${GREEN}▸ $1${NC}"
}

warn() {
    echo -e "${YELLOW}  ⚠ $1${NC}"
}

error() {
    echo -e "${RED}  ✗ $1${NC}"
    exit 1
}

success() {
    echo -e "${GREEN}  ✓ $1${NC}"
}

# ─── Pre-checks ───────────────────────────────────────
step "Pre-flight checks..."

if [ ! -f "$APP_DIR/.env" ]; then
    error ".env file not found! Copy .env.example and configure it."
fi

$PHP_BIN -v > /dev/null 2>&1 || error "PHP not found"
$COMPOSER_BIN -V > /dev/null 2>&1 || error "Composer not found"
success "PHP and Composer found"

# ─── Mode Selection ────────────────────────────────────
MODE=${1:-full}
echo -e "\n${BLUE}Deployment mode: ${MODE}${NC}"

case $MODE in
    full)
        echo "Running full deployment (code + assets + optimize)"
        ;;
    quick)
        echo "Running quick deployment (code only, no npm build)"
        ;;
    assets)
        echo "Running assets-only deployment"
        ;;
    migrate)
        echo "Running database migration only"
        ;;
    rollback)
        echo "Rolling back last migration"
        ;;
    *)
        echo "Usage: $0 {full|quick|assets|migrate|rollback}"
        exit 1
        ;;
esac

# ─── Maintenance Mode ─────────────────────────────────
if [ "$MODE" != "assets" ] && [ "$MODE" != "rollback" ]; then
    step "Enabling maintenance mode..."
    $PHP_BIN artisan down --retry=60 --refresh=15 2>/dev/null || true
    success "Maintenance mode enabled"
fi

# ─── Git Pull ──────────────────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "quick" ]; then
    step "Pulling latest code..."
    cd "$APP_DIR"
    git pull origin main 2>/dev/null || warn "Git pull skipped (not a git repo or no remote)"
    success "Code updated"
fi

# ─── Composer Install ──────────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "quick" ]; then
    step "Installing PHP dependencies..."
    $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
    success "Composer dependencies installed"
fi

# ─── NPM Build ────────────────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "assets" ]; then
    step "Building frontend assets..."
    $NPM_BIN ci --production=false 2>/dev/null || $NPM_BIN install
    $NPM_BIN run build
    success "Frontend assets built"
fi

# ─── Database Migration ───────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "quick" ] || [ "$MODE" = "migrate" ]; then
    step "Running database migrations..."
    $PHP_BIN artisan migrate --force
    success "Migrations completed"
fi

# ─── Rollback ─────────────────────────────────────────
if [ "$MODE" = "rollback" ]; then
    step "Rolling back last migration..."
    $PHP_BIN artisan migrate:rollback --force
    success "Rollback completed"
    exit 0
fi

# ─── Storage Link ─────────────────────────────────────
if [ "$MODE" = "full" ]; then
    step "Ensuring storage link..."
    $PHP_BIN artisan storage:link 2>/dev/null || true
    success "Storage linked"
fi

# ─── CMS Optimization ─────────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "quick" ]; then
    step "Optimizing CMS..."
    $PHP_BIN artisan cms:optimize
    success "CMS optimized (config, routes, views, CMS caches warmed)"
fi

# ─── Permissions ───────────────────────────────────────
if [ "$MODE" = "full" ]; then
    step "Setting file permissions..."
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || warn "Could not change ownership (run as root)"
    success "Permissions set"
fi

# ─── Health Check ──────────────────────────────────────
step "Running health check..."
$PHP_BIN artisan cms:health

# ─── Queue Restart ─────────────────────────────────────
if [ "$MODE" = "full" ] || [ "$MODE" = "quick" ]; then
    step "Restarting queue workers..."
    $PHP_BIN artisan queue:restart 2>/dev/null || true
    success "Queue workers signaled to restart"
fi

# ─── Disable Maintenance Mode ─────────────────────────
if [ "$MODE" != "assets" ] && [ "$MODE" != "rollback" ]; then
    step "Disabling maintenance mode..."
    $PHP_BIN artisan up
    success "Application is live!"
fi

# ─── Done ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Deployment completed successfully!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════${NC}"
echo ""
echo -e "  Mode:    ${BLUE}${MODE}${NC}"
echo -e "  Time:    $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
