#!/usr/bin/env bash
set -euo pipefail

# Usage: ./install-theme.sh <slug> "<Theme Name>" "<Author>" "<Author URI>" [-y]
# Run it from the WordPress root (or set WP_THEMES_DIR). Copies the theme to
# wp-content/themes/<slug>, replaces the _s placeholders and activates the
# theme when WP-CLI is available.
SLUG="${1:?need slug}"
THEME_NAME="${2:?need theme name}"
AUTHOR="${3:?need author}"
AUTHOR_URI="${4:?need author uri}"
AUTO="${5:-}"

command -v perl >/dev/null 2>&1 || { echo "perl is required."; exit 1; }

if [[ ! "$SLUG" =~ ^[a-z][a-z0-9-]*$ ]]; then
  echo "Slug must be lowercase letters, digits and hyphens, starting with a letter."
  exit 1
fi

SLUG_UNDER="${SLUG//-/_}"
SLUG_UPPER="$(printf '%s' "$SLUG_UNDER" | tr '[:lower:]' '[:upper:]')"

if [ -n "${WP_THEMES_DIR:-}" ]; then
  TARGET_DIR="$WP_THEMES_DIR/$SLUG"
elif [ -d "wp-content/themes" ]; then
  TARGET_DIR="wp-content/themes/$SLUG"
else
  echo "WordPress not found. Run from the WordPress root or set WP_THEMES_DIR."
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Target: $TARGET_DIR"
if [ "$AUTO" != "-y" ]; then
  read -r -p "Continue? (y/n) " -n 1 REPLY; echo
  [[ $REPLY =~ ^[Yy]$ ]] || { echo "Cancelled."; exit 1; }
fi

if [ -e "$TARGET_DIR" ] && [ -n "$(ls -A "$TARGET_DIR" 2>/dev/null)" ]; then
  echo "$TARGET_DIR already exists and is not empty. Remove it first."
  exit 1
fi

mkdir -p "$TARGET_DIR"

# Copy without development files (tar instead of rsync: available everywhere).
tar -C "$SCRIPT_DIR" \
  --exclude='./.git' --exclude='./.github' \
  --exclude='./node_modules' --exclude='./vendor' \
  --exclude='./install-theme.sh' --exclude='./setup.sh' \
  --exclude='./README.md' --exclude='./.gitignore' --exclude='.DS_Store' \
  --exclude='./composer.json' --exclude='./composer.lock' --exclude='./phpcs.xml.dist' \
  -cf - . | tar -C "$TARGET_DIR" -xf -

export SLUG SLUG_UNDER SLUG_UPPER NAME="$THEME_NAME" AUTHOR AUTHOR_URI

# Values are passed through the environment, so quotes, slashes and & in the
# theme name or author are safe. \b in front keeps words like "has_s_" intact.
while IFS= read -r -d '' f; do
  perl -pi -e '
    s/\x27_s\x27/\x27$ENV{SLUG}\x27/g;
    s/"_s"/"$ENV{SLUG}"/g;
    s/\b_s_/$ENV{SLUG_UNDER}_/g;
    s/\b_S_/$ENV{SLUG_UPPER}_/g;
    s/\b_s-/$ENV{SLUG}-/g;
    s/\@package _s\b/\@package $ENV{SLUG}/g;
    s/^( \* )_s( functions and definitions)/$1$ENV{NAME}$2/;
  ' "$f"
done < <(find "$TARGET_DIR" -type f \
  \( -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" -o -name "*.pot" \) \
  -not -path "*/.git/*" -not -path "*/node_modules/*" -not -path "*/vendor/*" -print0)

# style.css header
perl -pi -e '
  s/^Theme Name: .*/Theme Name: $ENV{NAME}/;
  s/^Author: .*/Author: $ENV{AUTHOR}/;
  s/^Author URI: .*/Author URI: $ENV{AUTHOR_URI}/;
  s/^Text Domain: .*/Text Domain: $ENV{SLUG}/;
' "$TARGET_DIR/style.css"

if [ -f "$TARGET_DIR/languages/_s.pot" ]; then
  mv "$TARGET_DIR/languages/_s.pot" "$TARGET_DIR/languages/${SLUG}.pot"
fi

if command -v wp >/dev/null 2>&1; then
  wp theme activate "$SLUG" || echo "Could not activate the theme with WP-CLI; activate it in the admin."
fi

echo "Done -> $TARGET_DIR"
echo "Slug=${SLUG}, Prefix=${SLUG_UNDER}_, Const=${SLUG_UPPER}_"
