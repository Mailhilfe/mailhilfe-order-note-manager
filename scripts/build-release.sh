#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SLUG="mailhilfe-order-note-manager"
VERSION="$(php -r '$f=file_get_contents($argv[1]); preg_match("/Version:\\s*([0-9.]+)/",$f,$m); echo $m[1] ?? "unknown";' "$ROOT/mailhilfe-order-note-manager.php")"
BUILD_DIR="$ROOT/build"
STAGE_DIR="$BUILD_DIR/$SLUG"
ZIP_FILE="$BUILD_DIR/${SLUG}-${VERSION}.zip"

rm -rf "$STAGE_DIR" "$ZIP_FILE"
mkdir -p "$STAGE_DIR"

rsync -a --delete --exclude-from="$ROOT/.distignore" "$ROOT/" "$STAGE_DIR/"

find "$STAGE_DIR" -type d -name '.git' -prune -exec rm -rf {} +
find "$STAGE_DIR" -type f \( -name '*.tmp' -o -name '*.log' -o -name '.DS_Store' -o -name 'Thumbs.db' \) -delete

(
  cd "$BUILD_DIR"
  zip -qr "$(basename "$ZIP_FILE")" "$SLUG"
)

unzip -t "$ZIP_FILE" >/dev/null
printf 'Created %s\n' "$ZIP_FILE"
