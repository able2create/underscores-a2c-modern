#!/bin/bash

# Modern _s Theme Installation Script
# Usage: ./install-theme.sh <theme-slug> "<Theme Name>" "<Author>" "<Author URI>"
# Example: ./install-theme.sh sundp "a2c - Theme" "Martin" "https://able2create.com"

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
echo "  Modern _s Theme Installation"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Theme Slug:    $THEME_SLUG"
echo "Theme Name:    $THEME_NAME"
echo "Author:        $AUTHOR"
echo "Author URI:    $AUTHOR_URI"
echo ""

# Get the directory where this script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Target directory (WordPress themes directory)
if [ -n "$WP_THEMES_DIR" ]; then
    TARGET_DIR="$WP_THEMES_DIR/$THEME_SLUG"
else
    # Try to auto-detect WordPress installation
    if [ -d "wp-content/themes" ]; then
        TARGET_DIR="wp-content/themes/$THEME_SLUG"
    else
        echo "Error: Could not find WordPress installation."
        echo "Please set WP_THEMES_DIR environment variable or run from WordPress root."
        exit 1
    fi
fi

echo "Target:        $TARGET_DIR"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Installation cancelled."
    exit 1
fi

echo ""
echo "Creating theme directory..."
mkdir -p "$TARGET_DIR"

echo "Copying theme files..."
rsync -av --exclude='.git' --exclude='install-theme.sh' --exclude='README.md' --exclude='.gitignore' "$SCRIPT_DIR/" "$TARGET_DIR/"

echo "Performing find and replace..."

# Function to perform replacements in a file
replace_in_file() {
    local file="$1"
    
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
find "$TARGET_DIR" -type f \( -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" \) | while read file; do
    replace_in_file "$file"
done

# Update style.css header
sed -i "s/Theme Name: _s/Theme Name: $THEME_NAME/g" "$TARGET_DIR/style.css"
sed -i "s/Author: Automattic/Author: $AUTHOR/g" "$TARGET_DIR/style.css"
sed -i "s|Author URI: https://automattic.com/|Author URI: $AUTHOR_URI|g" "$TARGET_DIR/style.css"

# Rename the .pot file
if [ -f "$TARGET_DIR/languages/_s.pot" ]; then
    mv "$TARGET_DIR/languages/_s.pot" "$TARGET_DIR/languages/$THEME_SLUG.pot"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  ✓ Installation Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Theme installed at: $TARGET_DIR"
echo ""
echo "Next steps:"
echo "1. Activate the theme in WordPress admin"
echo "2. Customize colors/typography in theme.json"
echo "3. Edit CSS variables in style.css"
echo ""
echo "To activate via WP-CLI:"
echo "  wp theme activate $THEME_SLUG"
echo ""
