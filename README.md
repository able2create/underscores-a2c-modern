Modernized _s Starter Theme
===

This is a **modernized fork** of the original Underscores (_s) starter theme, updated for 2025+ with:

- **PHP 8.4+** with strict types, type hints, and modern syntax
- **theme.json V3** for modern WordPress features
- **Modern CSS** with Custom Properties, logical properties, Container Queries, and fluid typography
- **Modern JavaScript** (ES6+) without jQuery
- **No build process required** - pure PHP, CSS, and JS
- **Performance-optimized** with lazy loading, resource hints, and deferred scripts
- **Security-hardened** with modern security headers and best practices

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

### 🔒 Security Hardened
- Modern security headers (X-Frame-Options, CSP, etc.)
- XML-RPC disabled
- WordPress version hidden
- Login error messages sanitized
- Unnecessary meta tags removed
- File editing disabled in admin

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
* Full WooCommerce plugin integration
* Jetpack compatibility
* Translation-ready with `.pot` file
* RTL language support with logical CSS properties
* Print stylesheet included
* Licensed under GPLv2 or later

Requirements
---------------

- **PHP 8.4+**
- **WordPress 6.4+**
- Composer (optional, for development tools only)

No Node.js, no build process, no compilation required!

Installation
---------------

### Quick Start

1. Clone or download this repository into your `wp-content/themes/` directory
2. Rename the folder to your theme name (e.g., `megatherium-is-awesome`)
3. Do a find and replace on the following:
   - `'_s'` → `'megatherium-is-awesome'` (text domain)
   - `_s_` → `megatherium_is_awesome_` (function names)
   - `Text Domain: _s` → `Text Domain: megatherium-is-awesome`
   - <code>&nbsp;_s</code> → <code>&nbsp;Megatherium_Is_Awesome</code> (DocBlocks)
   - `_s-` → `megatherium-is-awesome-` (handles)
   - `_S_` → `MEGATHERIUM_IS_AWESOME_` (constants)
4. Update the stylesheet header in `style.css` with your theme information
5. Update or delete this README
6. Activate the theme in WordPress

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

RTL Support
---------------

The theme uses CSS logical properties (like `margin-inline` instead of `margin-left`) which automatically adapt to RTL languages. A minimal `style-rtl.css` file is included for edge cases.

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
- Modern security headers
- WordPress version hidden
- XML-RPC disabled (can be re-enabled)
- Login error messages sanitized
- Unnecessary meta tags removed
- File editing disabled in WordPress admin

File Structure
---------------

```
_s/
├── inc/
│   ├── custom-header.php    # Custom header implementation
│   ├── customizer.php        # Theme customizer settings
│   ├── template-tags.php     # Custom template tags
│   ├── template-functions.php # Template helper functions
│   ├── jetpack.php           # Jetpack compatibility
│   ├── woocommerce.php       # WooCommerce compatibility
│   ├── performance.php       # Performance optimizations
│   └── security.php          # Security enhancements
├── js/
│   ├── navigation.js         # Mobile navigation
│   └── customizer.js         # Customizer preview JS
├── template-parts/           # Template part files
├── functions.php             # Theme functions
├── style.css                 # Main stylesheet
├── style-rtl.css             # RTL overrides
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
