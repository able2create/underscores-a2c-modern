# Installation Guide

## Quick Installation with Script

1. Download or clone this theme to your local machine
2. Run the installation script:

```bash
./install-theme.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
```

This will:
- Copy the theme to your WordPress themes directory
- Replace all placeholders with your theme details
- Rename files appropriately

## Manual Installation

### Step 1: Copy Theme Files

Copy this directory to `wp-content/themes/your-theme-name/`

### Step 2: Find and Replace

Replace the following placeholders throughout all files:

1. **Text Domain**: `'_s'` → `'your-theme-slug'`
2. **Function Prefix**: `_s_` → `your_theme_slug_`
3. **Text Domain (CSS)**: `Text Domain: _s` → `Text Domain: your-theme-slug`
4. **DocBlocks**: ` _s` → ` Your_Theme_Name`
5. **Handles**: `_s-` → `your-theme-slug-`
6. **Constants**: `_S_` → `YOUR_THEME_SLUG_`

### Step 3: Update Theme Info

Edit `style.css` header:
- Theme Name
- Author
- Author URI
- Description (optional)

### Step 4: Activate Theme

Via WP-CLI:
```bash
wp theme activate your-theme-slug
```

Or via WordPress Admin:
- Go to Appearance → Themes
- Click "Activate" on your theme

## WP-CLI Alternative (Without Script)

If you prefer using WP-CLI directly, you can do:

```bash
# Navigate to your WordPress installation
cd /path/to/wordpress

# Copy the theme
cp -r /path/to/_s wp-content/themes/sundp

# Then manually do the find/replace operations
# OR use the install-theme.sh script from the theme directory
```

## Post-Installation

1. **Customize theme.json**
   - Edit colors, typography, spacing to match your brand

2. **Edit CSS Variables**
   - Open `style.css` and modify `:root` variables

3. **Enable Optional Features**
   - Uncomment sidebar registration in `functions.php` if needed
   - Uncomment comment reply script if you need threaded comments
   - Review `inc/security.php` and `inc/performance.php` for optional features

4. **Security Hardening (Optional)**
   - Uncomment XML-RPC disable in `inc/security.php` if you don't need it
   - Uncomment file editing disable for production sites
   - Uncomment emoji disable in `inc/performance.php` for better performance

## Requirements

- PHP 8.4+
- WordPress 6.4+
- Modern browser (Chrome 88+, Firefox 87+, Safari 14+)

## No Build Process Required!

This theme doesn't need npm, webpack, or any build tools. Just:
1. Install
2. Customize
3. Activate
4. Start building!

All CSS and JavaScript work out of the box.
