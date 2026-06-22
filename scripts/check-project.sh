#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

printf 'Checking PHP syntax...\n'
while IFS= read -r -d '' file; do
  php -l "$file" >/dev/null
  printf '  OK %s\n' "${file#$ROOT/}"
done < <(find "$ROOT" -type f -name '*.php' -not -path "$ROOT/build/*" -print0)

printf 'Checking JavaScript syntax...\n'
node --check "$ROOT/assets/js/admin.js"

printf 'Checking version consistency...\n'
PLUGIN_VERSION="$(php -r '$f=file_get_contents($argv[1]); preg_match("/Version:\\s*([0-9.]+)/",$f,$m); echo $m[1] ?? "";' "$ROOT/mailhilfe-order-note-manager.php")"
CONSTANT_VERSION="$(awk -F"'" '/define\( .MHONT_VERSION./ { print $4; exit }' "$ROOT/mailhilfe-order-note-manager.php")"
STABLE_TAG="$(awk -F': ' '/^Stable tag:/{print $2; exit}' "$ROOT/readme.txt")"

if [[ -z "$PLUGIN_VERSION" || "$PLUGIN_VERSION" != "$CONSTANT_VERSION" || "$PLUGIN_VERSION" != "$STABLE_TAG" ]]; then
  printf 'Version mismatch: header=%s constant=%s stable=%s\n' "$PLUGIN_VERSION" "$CONSTANT_VERSION" "$STABLE_TAG" >&2
  exit 1
fi

printf 'Building release package...\n'
"$ROOT/scripts/build-release.sh"

printf 'Checking release exclusions...\n'
ZIP="$ROOT/build/mailhilfe-order-note-manager-${PLUGIN_VERSION}.zip"
if unzip -Z1 "$ZIP" | grep -Eq '(^|/)(\.github|docs|scripts|build)(/|$)|(^|/)README\.md$|(^|/)CONTRIBUTING\.md$'; then
  printf 'Repository-only files found in release ZIP.\n' >&2
  exit 1
fi

printf 'All checks passed.\n'
