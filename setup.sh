#!/usr/bin/env bash
# Modern _s Theme Setup Script
# Run this AFTER cloning/copying the theme to wp-content/themes/
# Usage: ./setup.sh <theme-slug> "<Theme Name>" "<Author>" "<Author URI>" [-y]
# Example: ./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
# -y answers both questions (continue, clean up development files) with yes.

set -euo pipefail

if [ "$#" -lt 4 ]; then
    echo "Usage: $0 <theme-slug> \"<Theme Name>\" \"<Author>\" \"<Author URI>\" [-y]"
    echo "Example: $0 sundp \"a2c - Theme\" \"Martin\" \"https://able2create.com\""
    exit 1
fi

command -v perl >/dev/null 2>&1 || { echo "perl is required."; exit 1; }

THEME_SLUG="$1"
THEME_NAME="$2"
AUTHOR="$3"
AUTHOR_URI="$4"
AUTO="${5:-}"

if [[ ! "$THEME_SLUG" =~ ^[a-z][a-z0-9-]*$ ]]; then
    echo "Theme slug must be lowercase letters, digits and hyphens, starting with a letter."
    exit 1
fi

THEME_SLUG_UNDER="${THEME_SLUG//-/_}"
THEME_SLUG_UPPER="$(printf '%s' "$THEME_SLUG_UNDER" | tr '[:lower:]' '[:upper:]')"
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "----------------------------------------"
echo "  Modern _s Theme Setup"
echo "----------------------------------------"
echo "Theme Slug:    $THEME_SLUG"
echo "Theme Name:    $THEME_NAME"
echo "Author:        $AUTHOR"
echo "Author URI:    $AUTHOR_URI"
echo "Directory:     $THEME_DIR"
echo ""
echo "This will modify all files in this directory."

ask() {
    if [ "$AUTO" = "-y" ]; then
        return 0
    fi
    local reply
    read -r -p "$1 (y/n) " -n 1 reply
    echo
    [[ $reply =~ ^[Yy]$ ]]
}

ask "Continue?" || { echo "Setup cancelled."; exit 1; }

echo ""
echo "Performing find and replace..."

export SLUG="$THEME_SLUG" SLUG_UNDER="$THEME_SLUG_UNDER" SLUG_UPPER="$THEME_SLUG_UPPER" \
       NAME="$THEME_NAME" AUTHOR AUTHOR_URI

# Values are passed through the environment, so quotes, slashes and & in the
# theme name or author are safe. \b in front keeps words like "has_s_" intact.
find "$THEME_DIR" -type f \
    \( -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" \) \
    -not -path "*/.git/*" -not -path "*/node_modules/*" -not -path "*/vendor/*" \
    -print0 | while IFS= read -r -d '' file; do
    perl -pi -e '
        s/\x27_s\x27/\x27$ENV{SLUG}\x27/g;
        s/"_s"/"$ENV{SLUG}"/g;
        s/\b_s_/$ENV{SLUG_UNDER}_/g;
        s/\b_S_/$ENV{SLUG_UPPER}_/g;
        s/\b_s-/$ENV{SLUG}-/g;
        s/\@package _s\b/\@package $ENV{SLUG}/g;
        s/^( \* )_s( functions and definitions)/$1$ENV{NAME}$2/;
    ' "$file"
done

echo "  Updating style.css header..."
perl -pi -e '
    s/^Theme Name: .*/Theme Name: $ENV{NAME}/;
    s/^Author: .*/Author: $ENV{AUTHOR}/;
    s/^Author URI: .*/Author URI: $ENV{AUTHOR_URI}/;
    s/^Text Domain: .*/Text Domain: $ENV{SLUG}/;
' "$THEME_DIR/style.css"

if [ -f "$THEME_DIR/languages/_s.pot" ]; then
    echo "  Renaming translation file..."
    mv "$THEME_DIR/languages/_s.pot" "$THEME_DIR/languages/$THEME_SLUG.pot"
fi

CURRENT_DIR="$(basename "$THEME_DIR")"

echo ""
echo "Setup complete."
echo ""
echo "Clean up development files?"
echo "(Removes: .git, .github, .gitignore, documentation, composer.json, phpcs.xml.dist, install scripts)"

if ask "Clean up?"; then
    rm -rf "$THEME_DIR/.git" "$THEME_DIR/.github"
    rm -f "$THEME_DIR/.gitignore" "$THEME_DIR/README.md" "$THEME_DIR/readme.txt" \
          "$THEME_DIR/INSTALL.md" "$THEME_DIR/SCHNELLSTART.md" \
          "$THEME_DIR/composer.json" "$THEME_DIR/composer.lock" "$THEME_DIR/phpcs.xml.dist" \
          "$THEME_DIR/install-theme.sh"
    echo "Cleanup complete."
    # Last: remove this script.
    rm -f "$THEME_DIR/setup.sh"
fi

echo ""
if [ "$CURRENT_DIR" != "$THEME_SLUG" ]; then
    echo "Rename the directory from '$CURRENT_DIR' to '$THEME_SLUG':"
    echo "  cd .. && mv $CURRENT_DIR $THEME_SLUG"
    echo ""
fi
echo "Then activate the theme: wp theme activate $THEME_SLUG"
