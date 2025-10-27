#!/bin/bash

# Modern _s Theme Setup Script
# Run this AFTER cloning/copying the theme to wp-content/themes/
# Usage: ./setup.sh <theme-slug> "<Theme Name>" "<Author>" "<Author URI>"
# Example: ./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com"

set -e

# Check if required arguments are provided
if [ "$#" -lt 4 ]; then
    echo "Usage: $0 <theme-slug> \"<Theme Name>\" \"<Author>\" \"<Author URI>\""
    echo "Example: $0 sundp \"a2c - Theme\" \"Martin\" \"https://able2create.com\""
    exit 1
fi

THEME_SLUG="$1"
THEME_NAME="$2"
AUTHOR="$3"
AUTHOR_URI="$4"

# Derived variables
THEME_SLUG_UPPER=$(echo "$THEME_SLUG" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
THEME_SLUG_UNDER=$(echo "$THEME_SLUG" | tr '-' '_')
THEME_NAME_SAFE=$(echo "$THEME_NAME" | sed 's/ /_/g')

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Modern _s Theme Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Theme Slug:    $THEME_SLUG"
echo "Theme Name:    $THEME_NAME"
echo "Author:        $AUTHOR"
echo "Author URI:    $AUTHOR_URI"
echo ""
echo "This will modify all files in the current directory."
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Setup cancelled."
    exit 1
fi

echo ""
echo "Performing find and replace in all files..."

# Get the directory where this script is located
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Function to perform replacements in a file
replace_in_file() {
    local file="$1"

    # Skip this script itself
    if [ "$file" = "$0" ] || [ "$file" = "${THEME_DIR}/setup.sh" ] || [ "$file" = "${THEME_DIR}/install-theme.sh" ]; then
        return
    fi

    # Text domain
    sed -i "s/'_s'/'$THEME_SLUG'/g" "$file"

    # Function names
    sed -i "s/_s_/${THEME_SLUG_UNDER}_/g" "$file"

    # Text Domain in style.css
    sed -i "s/Text Domain: _s/Text Domain: $THEME_SLUG/g" "$file"

    # DocBlocks
    sed -i "s/ _s/ $THEME_NAME_SAFE/g" "$file"

    # Handles
    sed -i "s/_s-/${THEME_SLUG}-/g" "$file"

    # Constants
    sed -i "s/_S_/${THEME_SLUG_UPPER}_/g" "$file"

    # Package name
    sed -i "s/@package _s/@package $THEME_SLUG/g" "$file"
}

# Find all PHP, CSS, and JS files and replace
find "$THEME_DIR" -type f \( -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" \) | while read file; do
    echo "  Processing: $(basename $file)"
    replace_in_file "$file"
done

# Update style.css header
echo "  Updating theme header..."
sed -i "s/Theme Name: _s/Theme Name: $THEME_NAME/g" "$THEME_DIR/style.css"
sed -i "s/Author: Automattic/Author: $AUTHOR/g" "$THEME_DIR/style.css"
sed -i "s|Author URI: https://automattic.com/|Author URI: $AUTHOR_URI|g" "$THEME_DIR/style.css"

# Rename the .pot file
if [ -f "$THEME_DIR/languages/_s.pot" ]; then
    echo "  Renaming translation file..."
    mv "$THEME_DIR/languages/_s.pot" "$THEME_DIR/languages/$THEME_SLUG.pot"
fi

# Get parent directory name
CURRENT_DIR=$(basename "$THEME_DIR")

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  ✓ Setup Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Ask about cleanup
echo "Clean up development files?"
echo "(Removes: .git, .github, .gitignore, composer.json, README files, etc.)"
echo ""
read -p "Clean up? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "Cleaning up development files..."

    # Remove Git
    rm -rf "$THEME_DIR/.git"
    rm -rf "$THEME_DIR/.github"
    rm -f "$THEME_DIR/.gitignore"

    # Remove documentation
    rm -f "$THEME_DIR/README.md"
    rm -f "$THEME_DIR/readme.txt"
    rm -f "$THEME_DIR/INSTALL.md"
    rm -f "$THEME_DIR/SCHNELLSTART.md"
    rm -f "$THEME_DIR/LICENSE"

    # Remove development files
    rm -f "$THEME_DIR/composer.json"
    rm -f "$THEME_DIR/phpcs.xml.dist"
    rm -f "$THEME_DIR/install-theme.sh"

    # Remove this script
    rm -f "$THEME_DIR/setup.sh"

    echo "✓ Cleanup complete!"
    echo ""
fi

if [ "$CURRENT_DIR" != "$THEME_SLUG" ]; then
    echo "IMPORTANT: Rename this directory from '$CURRENT_DIR' to '$THEME_SLUG'"
    echo ""
    echo "Run this command from the themes directory:"
    echo "  mv $CURRENT_DIR $THEME_SLUG"
    echo ""
fi

echo "Next steps:"
echo "1. Rename the directory to '$THEME_SLUG' (if not already done)"
echo "2. Activate the theme: wp theme activate $THEME_SLUG"
echo "3. Customize theme.json and style.css"
echo ""
