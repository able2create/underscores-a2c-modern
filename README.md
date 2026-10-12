Modernized _s Starter Theme
===

This is a **modernized fork** of the original Underscores (_s) starter theme, updated for 2025+ with:

- **PHP 8.4+** with type hints and modern syntax
- **WordPress 6.7+** with theme.json V3 for modern features (tested up to 7.1)
- **Modern CSS** - pure CSS with Custom Properties, no build process
- **Modern JavaScript** (ES6+) without jQuery
- **No build process required** - pure PHP, CSS, and JS
- **Performance-optimized** - Emoji disabled, lazy loading enabled
- **Security-hardened** - XML-RPC disabled, file editing disabled
- **Easy installation** with included script - no manual find/replace needed

What's Different in This Modern Version?
===

### Modern PHP (8.4+)
- Full type hints on all functions (parameters and return types)
- Array type documentation with PHPDoc generics
- Modern PHP features throughout

### Modern CSS (No Build Process!)
- **CSS Custom Properties** (CSS Variables) for theming
- **Modern CSS Reset** based on current best practices
- No SASS/SCSS - pure, modern CSS that runs in all modern browsers

Security and performance hardening details are covered in their own sections below, so they're not repeated here.

### Modern JavaScript
- The only shipped JavaScript is `js/customizer.js`, an ES6+ Customizer live-preview script (no jQuery)
- No frontend JavaScript is enqueued by default, keeping the theme dependency-free

### theme.json V3
- Complete theme.json implementation
- Color palettes and typography scales
- Spacing and layout settings
- Shadow presets
- Block-specific settings
- Fluid typography built-in

WordPress 7.1 Support
===

Tested with WordPress 7.1 "Mary Lou". The block editor is now always rendered in an iframe, also for classic themes like this one. Editor styling therefore comes exclusively from `editor-style.css` (loaded via `add_editor_style()`) and the theme.json presets — CSS printed via `admin_head` or `admin_enqueue_scripts` no longer reaches the editor canvas.

Features introduced in WordPress 7.0 "Armstrong" keep working:

- **AI Connectors** — works out of the box. The connection is configured under *Settings » Connectors*; no theme configuration is required. To disable AI support site-wide, add `define( 'WP_AI_SUPPORT', false );` to `wp-config.php`.
- **Responsive Block Visibility** — show/hide blocks per device directly in the block inspector; handled by core, no theme CSS needed.
- **Custom CSS per block** — available in the block inspector's Advanced panel; no theme setup required.
- **Font Library** — now available for classic themes like this one; manage and install fonts from the dashboard.
- **New core blocks** — `theme.json` provides matching styles for the new **Breadcrumbs** and **Icon** blocks, and adds block-level `:hover`/`:focus` states for the **Navigation** block.

Features
===

* Clean, semantic HTML5 templates
* Custom header and logo support
* Single-level navigation menu via `wp_nav_menu()`
* Custom template tags for common functions
* Performance-optimized asset loading
* Modern responsive grid layouts using CSS Grid
* Plugin-friendly (security hardening kept compatible with popular plugins)
* Translation-ready with `.pot` file
* Security-hardened and performance-optimized out of the box
* Licensed under GPLv2 or later

Requirements
---------------

- **PHP 8.4+**
- **WordPress 6.7+** (tested up to 7.1)
- Composer (optional, for development tools only)

No Node.js, no build process, no compilation required!

Installation
---------------

### Easy Installation with Script

Use the included installation script for automatic setup:

```bash
./install-theme.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
```

Replace with your own values:
- `sundp` - Your theme slug
- `"a2c - Theme"` - Your theme name
- `"Martin"` - Author name
- `"https://able2create.com"` - Author URL

The script will automatically:
- Copy files to the correct location
- Replace all placeholders
- Rename files appropriately

See [INSTALL.md](INSTALL.md) for detailed installation instructions and manual setup.

### Quick Manual Installation

1. Clone or download this repository
2. Run the install script (see above) OR
3. Manually copy to `wp-content/themes/your-theme-name/` and do find/replace:
   - `'_s'` → `'your-theme-slug'`
   - `_s_` → `your_theme_slug_`
   - Update `style.css` header
4. Activate via WordPress admin or `wp theme activate your-theme-slug`

### Customizing Colors and Typography

Edit the `theme.json` file to customize:
- Color palettes
- Typography scales
- Spacing values
- Shadow presets
- Layout settings

CSS Custom Properties are defined in `style.css` under `:root` and can be customized there as well.

### Development Tools (Optional)

Install Composer dependencies for development tools:

```sh
composer install
```

Available commands:
- `composer lint:wpcs` - Check PHP against WordPress Coding Standards
- `composer lint:php` - Check PHP syntax
- `composer make-pot` - Generate translation file

No npm, no webpack, no build process needed!

Browser Support
---------------

This theme uses modern CSS and JavaScript features. It supports:
- Chrome/Edge 88+
- Firefox 87+
- Safari 14+

For older browser support, you may need to add polyfills or transpilation.

Plugin Compatibility
---------------

This theme works with all popular WordPress plugins. It ships security- and performance-hardened by default — see the [Security](#security) section below for what's restricted and how to adjust `inc/security.php` / `inc/performance.php` if a plugin needs a disabled feature re-enabled.

One exception worth calling out: the REST API restriction only applies to the core `wp/v2` namespace. Plugins with their own REST namespace (e.g. WP-Statistics' `wp-statistics/v2`) stay reachable for anonymous visitors without any changes.

Performance
---------------

Built-in performance features:
- Emoji scripts and jQuery Migrate removed
- Emoji DNS prefetch removed (resource hints added by plugins are kept)
- Dashicons dequeued for logged-out visitors, heartbeat disabled on the frontend
- Minimal CSS and JS (no frameworks, no jQuery dependency)

Left to WordPress core on purpose, because overriding it breaks plugins:
- Script loading strategy (`defer`/`async`) — register scripts with the `strategy` argument instead of forcing `defer` on every script
- `ver` query strings — they are the cache busting after updates
- Block and global styles — loaded on demand by core; `global-styles` holds the theme.json CSS variables used by `style.css`
- Speculative loading (WordPress 6.8+) — free prefetching of likely next pages

Post revisions and autosave interval belong in `wp-config.php` (`WP_POST_REVISIONS`, `AUTOSAVE_INTERVAL`); core defines both before the theme loads.

Security
---------------

Security hardening enabled by default (`inc/security.php`):
- Security headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy
- WordPress version hidden; generator and unnecessary `<head>` meta tags removed
- Login error messages sanitized
- XML-RPC and pingbacks disabled
- Core `wp/v2` REST namespace limited to logged-in users. Other REST namespaces (e.g. a plugin's own `plugin-slug/v1`) stay open for anonymous requests unless added to the `_s_rest_blocked_namespaces` filter
- User enumeration blocked: REST user routes (anonymous only — the editor keeps them), `?author=N` and author archives, users sitemap, author data in oEmbed, and the lost-password form no longer reveals whether an account exists
- File editing disabled (`DISALLOW_FILE_EDIT`)
- Comments disabled site-wide

To loosen any of these for a specific plugin, edit `inc/security.php` directly.

File Structure
---------------

```
_s/
├── inc/
│   ├── custom-header.php      # Custom header implementation
│   ├── customizer.php         # Theme customizer settings
│   ├── template-tags.php      # Custom template tags
│   ├── template-functions.php # Template helper functions
│   ├── performance.php        # Performance optimizations
│   └── security.php           # Security hardening
├── js/
│   └── customizer.js          # Customizer preview JS
├── template-parts/           # Template part files
├── functions.php             # Theme functions
├── style.css                 # Main stylesheet
├── install-theme.sh          # Easy installation script
├── INSTALL.md                # Detailed installation guide
├── theme.json                # Theme configuration (V3)
├── *.php                     # Template files
└── README.md                 # This file
```

Contributing
---------------

This is a modernized fork. For the original Underscores project, visit [underscores.me](https://underscores.me).

License
---------------

This theme, like WordPress, is licensed under the GPL v2 or later.

Use it to make something cool, have fun, and share what you've learned.

_s is based on Underscores https://underscores.me/, (C) 2012-2025 Automattic, Inc.
