Modernized _s Starter Theme
===

This is a **modernized fork** of the original Underscores (_s) starter theme, updated for 2025+ with:

- **PHP 8.4+** with strict types, type hints, and modern syntax
- **theme.json V3** for modern WordPress features
- **Modern CSS** with Custom Properties, logical properties, Container Queries, and fluid typography
- **Modern JavaScript** (ES6+) without jQuery
- **No build process required** - pure PHP, CSS, and JS
- **Performance-optimized** with lazy loading, resource hints, and deferred scripts
- **Security-hardened** (but plugin-friendly!) with modern security headers
- **Easy installation** with included script - no manual find/replace needed

What's Different in This Modern Version?
===

### 🚀 Modern PHP (8.4+)
- Strict type declarations (`declare(strict_types=1)`)
- Full type hints on all functions (parameters and return types)
- Array type documentation with PHPDoc generics
- Modern PHP features throughout

### 🎨 Modern CSS (No Build Process!)
- **CSS Custom Properties** (CSS Variables) for theming
- **Logical properties** (margin-inline, padding-block) for RTL support
- **Fluid typography** with `clamp()` for responsive text
- **Container Queries** for truly responsive components
- **Modern CSS Reset** based on current best practices
- **Dark mode support** via `prefers-color-scheme`
- No SASS/SCSS - pure, modern CSS that runs in all modern browsers

### ⚡ Performance Optimizations
- Lazy loading and async decoding for images
- Deferred script loading with modern WordPress `strategy` parameter
- Resource hints (preconnect, dns-prefetch)
- Removed unnecessary WordPress features (emojis, etc.)
- Query string removal from static assets
- Optimized asset loading

### 🔒 Security Hardened (But Plugin-Friendly!)
- Modern security headers (configurable)
- WordPress version hidden
- Login error messages sanitized
- Unnecessary meta tags removed
- Optional features (commented out by default):
  - XML-RPC disable (some plugins need it)
  - File editing disable (enable for production)
  - Additional permission policies

### 🎯 Modern JavaScript
- ES6+ syntax throughout
- No jQuery dependency
- Modern DOM APIs
- Event delegation
- Passive event listeners
- Proper initialization patterns

### 🎭 theme.json V3
- Complete theme.json implementation
- Color palettes and typography scales
- Spacing and layout settings
- Shadow presets
- Block-specific settings
- Fluid typography built-in

Features
===

* Clean, semantic HTML5 templates
* Custom header and logo support
* Navigation menu support with keyboard and touch accessibility
* Custom template tags for common functions
* Performance-optimized asset loading
* Modern responsive grid layouts using CSS Grid
* Plugin-friendly (no restrictive security measures by default)
* Translation-ready with `.pot` file
* Print stylesheet included
* Sidebar and comment features (commented out by default, easy to enable)
* Licensed under GPLv2 or later

Requirements
---------------

- **PHP 8.4+**
- **WordPress 6.4+**
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

This theme is designed to work with all popular WordPress plugins. Security features are conservative by default:

- XML-RPC is **enabled** (required by some plugins like Jetpack)
- File editing is **enabled** (disable in production via `inc/security.php`)
- Emoji support is **enabled** (disable for performance via `inc/performance.php`)
- Security headers are minimal and safe

All restrictive features are commented out and can be enabled by uncommenting in:
- `inc/security.php` - Security hardening options
- `inc/performance.php` - Performance optimizations

Performance
---------------

Built-in performance features:
- Lazy loading images by default
- Deferred JavaScript loading
- Resource hints for external domains
- Minimal CSS and JS (no frameworks)
- No jQuery dependency
- Efficient WordPress queries

Security
---------------

Security features enabled by default:
- Safe security headers (X-Content-Type-Options, X-Frame-Options, Referrer-Policy)
- WordPress version hidden
- Login error messages sanitized
- Unnecessary meta tags removed

Optional security features (commented out, uncomment to enable):
- XML-RPC disable (some plugins like Jetpack need it)
- File editing disable (recommended for production)
- Additional permission policies

File Structure
---------------

```
_s/
├── inc/
│   ├── custom-header.php      # Custom header implementation
│   ├── customizer.php         # Theme customizer settings
│   ├── template-tags.php      # Custom template tags
│   ├── template-functions.php # Template helper functions
│   ├── performance.php        # Performance optimizations (mostly commented)
│   └── security.php           # Security enhancements (plugin-friendly)
├── js/
│   ├── navigation.js         # Mobile navigation
│   └── customizer.js         # Customizer preview JS
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
