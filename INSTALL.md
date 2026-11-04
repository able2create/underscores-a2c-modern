# Installation

Requirements: PHP 8.4+, WordPress 6.7+, `perl` and `tar` (present on practically every Linux/macOS host). WP-CLI is optional.

Both scripts replace the `_s` placeholders (text domain, function prefix, constants, style handles) with your theme slug. A German-language installation needs nothing extra, but the theme ships no translation: its few frontend strings stay English until you add a `.po/.mo` for your text domain.

## Option 1: install-theme.sh (copy into a WordPress install)

Run from the WordPress root, with the theme repository checked out anywhere:

```bash
/path/to/underscores-a2c-modern/install-theme.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
```

Arguments: theme slug (lowercase letters, digits, hyphens), theme name, author, author URL. Add `-y` as a fifth argument to skip the confirmation. If `WP_THEMES_DIR` is set it is used instead of `wp-content/themes`.

The script copies the theme to `wp-content/themes/<slug>` (without `.git`, `.github`, `vendor`, `README.md`, composer files and the install scripts), replaces the placeholders, sets the `style.css` header, renames `languages/_s.pot`, and runs `wp theme activate <slug>` if WP-CLI is available. It refuses to run if the target directory already exists and is not empty.

## Option 2: setup.sh (in place, on the server)

```bash
cd wp-content/themes/
git clone https://github.com/able2create/underscores-a2c-modern.git _s
cd _s
./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
cd .. && mv _s sundp
wp theme activate sundp
```

`setup.sh` modifies the files in the directory it lives in. It asks to continue and whether to remove development files (`.git`, `.github`, docs, `composer.json`, `phpcs.xml.dist`, the install scripts); `-y` as a fifth argument answers both with yes. The directory name must match the slug, so rename it afterwards as shown.

## Manual installation

1. Copy the theme to `wp-content/themes/your-theme-slug/`.
2. Replace in all `.php`, `.css`, `.js` and `.json` files:
   - `'_s'` and `"_s"` with `'your-theme-slug'` (text domain)
   - `_s_` with `your_theme_slug_` (function and filter prefix)
   - `_S_` with `YOUR_THEME_SLUG_` (constants)
   - `_s-` with `your-theme-slug-` (style and script handles)
   - `@package _s` with `@package your-theme-slug`
3. Edit the `style.css` header: Theme Name, Author, Author URI, Text Domain.
4. Rename `languages/_s.pot` to `languages/your-theme-slug.pot`.
5. Activate it in the admin (Appearance > Themes) or with `wp theme activate your-theme-slug`.

## After installing

- The prefix replacement also renames the theme's filters: `_s_features` becomes `sundp_features` and `_s_rest_blocked_namespaces` becomes `sundp_rest_blocked_namespaces`. The README describes them under their original names.
- Colors, typography, spacing and layout are set in `theme.json`.
- The security and performance defaults in `inc/security.php` and `inc/performance.php` are active from the start. Comments, widget removal, frontend heartbeat and Dashicons removal can be switched off with the features filter (see the README).
- Run `composer install` only if you want the lint tools (`composer lint:wpcs`, `composer lint:php`).
