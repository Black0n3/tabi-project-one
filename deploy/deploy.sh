#!/usr/bin/env bash
#
# Deploy the current working tree to the demo VPS (project-one.tabardi.hr).
#
# Modeled on grancaffe's deploy/deploy.sh (sibling project on the same VPS),
# with two fixes learned while setting this one up:
#  - rsync --exclude='public/' matches a directory named "public" at ANY
#    depth, not just the webroot. This app has app/Livewire/Public and
#    resources/views/livewire/public, which that pattern silently ate.
#    Always anchor with a leading slash: --exclude='/public/'.
#  - Virtualmin's per-domain layout puts the served docroot at
#    $APP_DIR/public_html, separate from $APP_DIR/public. Laravel's
#    public_path() (used by storage:link and the @vite manifest lookup)
#    always resolves to $APP_DIR/public, which nothing ever serves. So the
#    build output and the storage symlink must exist in BOTH places:
#    once in public_html (what Apache actually serves) and once in
#    $APP_DIR/public (what artisan/Vite's helpers read from disk).
#  - The same unanchored-pattern trap applies to tar: --exclude='vendor'
#    also drops resources/views/vendor (published package views). All tar
#    excludes below are anchored with './'.
#
# Usage:  bash deploy/deploy.sh
# Env overrides: SSH_HOST SSH_USER SSH_KEY APP_DIR WEB_DIR SITE_USER CF_ZONE CF_HOST CF_TOKEN_FILE
set -euo pipefail

SSH_HOST="${SSH_HOST:-194.163.189.128}"
SSH_USER="${SSH_USER:-root}"
SSH_KEY="${SSH_KEY:-$HOME/.ssh/centar_plese_vps}"
APP_DIR="${APP_DIR:-/home/project-one}"
WEB_DIR="${WEB_DIR:-/home/project-one/public_html}"
SITE_USER="${SITE_USER:-project-one}"
CF_ZONE="${CF_ZONE:-d7ad9ef657e6d821612a6466c4c63be6}"
CF_HOST="${CF_HOST:-project-one.tabardi.hr}"
CF_TOKEN_FILE="${CF_TOKEN_FILE:-$HOME/.cloudflare/api_token}"

SSH="ssh -i $SSH_KEY -o StrictHostKeyChecking=no $SSH_USER@$SSH_HOST"
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

say() { printf '\n\033[1;36m▶ %s\033[0m\n' "$*"; }

say "Building assets locally"
npm run build

say "Packing working tree + build output"
tar -czf /tmp/p1-src.tar.gz \
  --exclude='./.git' --exclude='./.claude' --exclude='./vendor' --exclude='./node_modules' --exclude='./public/build' --exclude='./public/storage' --exclude='./public/hot' \
  --exclude='./database/*.sqlite' \
  --exclude='./.env' --exclude='./storage/framework/views/*.php' \
  --exclude='./storage/logs/*' --exclude='./.phpunit.result.cache' \
  .
tar -czf /tmp/p1-build.tar.gz -C public build

say "Uploading"
scp -i "$SSH_KEY" -o StrictHostKeyChecking=no /tmp/p1-src.tar.gz /tmp/p1-build.tar.gz "$SSH_USER@$SSH_HOST:/tmp/"

say "Extract + sync on the server"
$SSH APP_DIR="$APP_DIR" WEB_DIR="$WEB_DIR" SITE_USER="$SITE_USER" 'bash -s' <<'REMOTE'
set -euo pipefail
rm -rf /tmp/p1stage && mkdir -p /tmp/p1stage
tar -xzf /tmp/p1-src.tar.gz -C /tmp/p1stage

# App code — additive only. NEVER add --delete here (wipes storage/uploads).
# NOTE the leading slash: '/public/' anchors to the top level only.
rsync -a --exclude='/public/' /tmp/p1stage/ "$APP_DIR/"

# Front controller + static assets: the real webroot Apache serves.
rsync -a --exclude='build/' /tmp/p1stage/public/ "$WEB_DIR/"
rm -rf "$WEB_DIR/build"
tar -xzf /tmp/p1-build.tar.gz -C "$WEB_DIR/"

# Laravel's own public_path() (storage:link, @vite manifest lookup) resolves
# to $APP_DIR/public, which nothing serves directly — keep it in sync too.
rsync -a --exclude='build/' /tmp/p1stage/public/ "$APP_DIR/public/"
rm -rf "$APP_DIR/public/build"
tar -xzf /tmp/p1-build.tar.gz -C "$APP_DIR/public/"

chown -R "$SITE_USER:$SITE_USER" "$APP_DIR"

ln -sfn "$APP_DIR/storage/app/public" "$WEB_DIR/storage"
ln -sfn "$APP_DIR/storage/app/public" "$APP_DIR/public/storage"
chown -h "$SITE_USER:$SITE_USER" "$WEB_DIR/storage" "$APP_DIR/public/storage"

mkdir -p "$APP_DIR/tmp" && chown "$SITE_USER:$SITE_USER" "$APP_DIR/tmp"
cd "$APP_DIR"
run() { sudo -u "$SITE_USER" env TMPDIR="$APP_DIR/tmp" php artisan "$@"; }
composer_bin() { sudo -u "$SITE_USER" env TMPDIR="$APP_DIR/tmp" COMPOSER_HOME="$APP_DIR/tmp" composer "$@"; }
composer_bin install --optimize-autoloader --no-interaction
run migrate --force
run optimize:clear
run config:cache
run route:cache
run view:cache
run event:cache
echo "server steps done"
REMOTE

if [ -f "$CF_TOKEN_FILE" ]; then
  say "Purging Cloudflare cache for $CF_HOST"
  curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$CF_ZONE/purge_cache" \
    -H "Authorization: Bearer $(cat "$CF_TOKEN_FILE")" -H "Content-Type: application/json" \
    --data "{\"hosts\":[\"$CF_HOST\"]}" | grep -o '"success":[a-z]*' || true
fi

say "Smoke test"
for path in / /projekti /jedinice /login; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://$CF_HOST$path")
  printf '  %s  %s\n' "$code" "$path"
done

say "Done — https://$CF_HOST"
