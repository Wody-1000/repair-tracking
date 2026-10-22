#!/usr/bin/env bash
# Restore a backup into a NEW database and prove it by comparing row counts.
# Usage: ./scripts/restore.sh backups/repair_tracking_YYYYMMDD_HHMMSS.dump [target_db]
# Needs a role that can create databases (e.g. postgres). Set RESTORE_USER / RESTORE_PASSWORD.
set -euo pipefail

FILE="${1:?Usage: restore.sh <backup.dump> [target_db]}"
TARGET="${2:-repair_tracking_restored}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
SOURCE="${DB_DATABASE:-repair_tracking}"
RUSER="${RESTORE_USER:-postgres}"
export PGPASSWORD="${RESTORE_PASSWORD:-}"

PSQL="psql -h $DB_HOST -p $DB_PORT -U $RUSER -tA"

$PSQL -d postgres -c "DROP DATABASE IF EXISTS \"$TARGET\""
$PSQL -d postgres -c "CREATE DATABASE \"$TARGET\""
pg_restore -h "$DB_HOST" -p "$DB_PORT" -U "$RUSER" -d "$TARGET" --no-owner --exit-on-error "$FILE"

LOG="$(cd "$(dirname "$0")/.." && pwd)/backups/restore-verification.log"
mkdir -p "$(dirname "$LOG")"
{
  echo "Restore check: $(date '+%Y-%m-%d %H:%M:%S')"
  echo "Backup file:   $FILE"
  echo "Restored into: $TARGET"
  echo "Row counts (table, source, restored):"
} > "$LOG"

STATUS=0
for T in users customers repair_statuses tickets ticket_status_histories part_categories parts ticket_parts low_stock_alerts; do
  A=$($PSQL -d "$SOURCE" -c "SELECT count(*) FROM $T")
  B=$($PSQL -d "$TARGET" -c "SELECT count(*) FROM $T")
  if [ "$A" = "$B" ]; then R="OK"; else R="MISMATCH"; STATUS=1; fi
  printf "  %-26s %6s %6s  %s\n" "$T" "$A" "$B" "$R" >> "$LOG"
done
if [ $STATUS -eq 0 ]; then echo "RESULT: RESTORE VERIFIED" >> "$LOG"; else echo "RESULT: RESTORE FAILED" >> "$LOG"; fi

cat "$LOG"
[ $STATUS -eq 0 ] || exit 1
