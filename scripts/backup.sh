#!/usr/bin/env bash
# Backup the repair_tracking database to backups/<db>_<timestamp>.dump (custom format)
# Usage: ./scripts/backup.sh            (uses defaults below or DB_* env vars)
set -euo pipefail

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_DATABASE:-repair_tracking}"
DB_USER="${BACKUP_USER:-${DB_USERNAME:-repair_backup}}"
export PGPASSWORD="${BACKUP_PASSWORD:-${DB_PASSWORD:-}}"

DIR="$(cd "$(dirname "$0")/.." && pwd)/backups"
mkdir -p "$DIR"
FILE="$DIR/${DB_NAME}_$(date +%Y%m%d_%H%M%S).dump"

pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -Fc --no-owner --no-privileges -f "$FILE"
echo "Backup written: $FILE ($(du -h "$FILE" | cut -f1))"
