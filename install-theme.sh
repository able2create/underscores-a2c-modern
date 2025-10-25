#!/usr/bin/env bash
set -euo pipefail

# Usage: ./install-theme.sh <slug> "<Theme Name>" "<Author>" "<Author URI>" [-y]
SLUG="${1:?need slug}"
THEME_NAME="${2:?need theme name}"
AUTHOR="${3:?need author}"
AUTHOR_URI="${4:?need author uri}"
AUTO="${5:-}"

SLUG_UNDER="${SLUG//-/_}"
SLUG_UPPER="$(printf '%s' "$SLUG_UNDER" | tr '[:lower:]' '[:upper:]')"

# Theme-Ziel
if [ -n "${WP_THEMES_DIR:-}" ]; then
  TARGET_DIR="$WP_THEMES_DIR/$SLUG"
elif [ -d "wp-content/themes" ]; then
  TARGET_DIR="wp-content/themes/$SLUG"
else
  echo "WordPress nicht gefunden. Im WP-Root ausführen oder WP_THEMES_DIR setzen."
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Target: $TARGET_DIR"
if [ "$AUTO" != "-y" ]; then
  read -p "Continue? (y/n) " -n 1 -r; echo
  [[ $REPLY =~ ^[Yy]$ ]] || { echo "Cancelled."; exit 1; }
fi

mkdir -p "$TARGET_DIR"

# Dateien kopieren, aber Ballast weglassen
rsync -a \
  --exclude='.git' --exclude='.github' \
  --exclude='node_modules' --exclude='vendor' \
  --exclude='install-theme.sh' --exclude='README.md' \
  --exclude='.gitignore' --exclude='.DS_Store' \
  "$SCRIPT_DIR/” "$TARGET_DIR/"

# Portable sed -i
sedi() { sed -i.bak "$@"; rm -f "${@: -1}.bak"; }

# style.css Header hart setzen
if [ -f "$TARGET_DIR/style.css" ]; then
  sedi "s/^Theme Name: .*/Theme Name: ${THEME_NAME}/" "$TARGET_DIR/style.css"
  sedi "s/^Author: .*/Author: ${AUTHOR}/" "$TARGET_DIR/style.css"
  sedi "s|^Author URI: .*|Author URI: ${AUTHOR_URI}|" "$TARGET_DIR/style.css"
  # Text Domain setzen oder hinzufügen
  grep -q "^Text Domain:" "$TARGET_DIR/style.css" \
    && sedi "s/^Text Domain: .*/Text Domain: ${SLUG}/" "$TARGET_DIR/style.css" \
    || printf "Text Domain: %s\n" "$SLUG" >> "$TARGET_DIR/style.css"
fi

# Ersetzungen mit Boundaries, ohne .git/node_modules/vendor
while IFS= read -r -d '' f; do
  # Textdomain '_s' nur in Quotes
  perl -0777 -pe "s/'_s'/'${SLUG}'/g; s/\"_s\"/\"${SLUG}\"/g;" -i "$f"
  # Funktionspräfixe
  perl -0777 -pe "s/\\b_s_\\b/${SLUG_UNDER}_/g; s/\\b_S_\\b/${SLUG_UPPER}_/g;" -i "$f"
  # Handle-Namen
  perl -0777 -pe "s/\\b_s-/${SLUG}-/g;" -i "$f"
  # Paketname in Docblocks
  perl -0777 -pe "s/@package\\s+_s/@package ${SLUG}/g;" -i "$f"
done < <(find "$TARGET_DIR" -type f \
  \( -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" -o -name "*.pot" \) \
  -not -path "*/.git/*" -not -path "*/node_modules/*" -not -path "*/vendor/*" -print0)

# languages umbenennen
if [ -f "$TARGET_DIR/languages/_s.pot" ]; then
  mv "$TARGET_DIR/languages/_s.pot" "$TARGET_DIR/languages/${SLUG}.pot"
fi
mkdir -p "$TARGET_DIR/languages"

# Textdomain laden absichern
if [ -f "$TARGET_DIR/functions.php" ] && ! grep -q "load_theme_textdomain" "$TARGET_DIR/functions.php"; then
  awk -v s="$SLUG" 'NR==1{print "<?php\nadd_action('\''after_setup_theme'\'', function(){load_theme_textdomain('\''"s"'\'', get_template_directory().'\''/languages'\'');});\n"}1' \
    "$TARGET_DIR/functions.php" > "$TARGET_DIR/functions.php.tmp" && mv "$TARGET_DIR/functions.php.tmp" "$TARGET_DIR/functions.php"
fi

# Theme aktivieren, wenn wp-cli verfügbar
if command -v wp >/dev/null 2>&1; then
  wp theme activate "$SLUG" || true
fi

echo "Done → $TARGET_DIR"
echo "Slug=${SLUG}, Prefix=${SLUG_UNDER}_, Const=${SLUG_UPPER}_"
