Underscores A2C Modern
===

A modernized fork of the [Underscores](https://underscores.me) (_s) starter theme for classic PHP themes. No build process: plain PHP, CSS and one small Customizer script.

- PHP 8.4+ with type hints on all named functions
- `theme.json` V3 (color palette, fluid font sizes, spacing, shadows, block settings)
- Plain CSS with Custom Properties, no SASS and no frontend JavaScript
- Security and performance defaults in `inc/security.php` and `inc/performance.php`, partly switchable (see [Opinionated defaults](#opinionated-defaults))
- Install script that renames the theme for you

Requirements
---------------

- PHP 8.4+
- WordPress 6.7+ (tested up to 7.1)
- Composer, optional and only for the lint tools

WordPress 7.1
---------------

The block editor is always rendered in an iframe, also for classic themes. Editor styling therefore comes only from `editor-style.css` (loaded via `add_editor_style()`) and the `theme.json` presets. CSS printed via `admin_head` or `admin_enqueue_scripts` does not reach the editor canvas.

`theme.json` also styles the Breadcrumbs and Icon blocks and adds `:hover`/`:focus` states for the Navigation block.

Installation
---------------

With the install script:

```bash
./install-theme.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
```

Arguments: theme slug, theme name, author name, author URL. The script copies the files, replaces the placeholders and renames files. More detail in [INSTALL.md](INSTALL.md).

Manually: copy the theme to `wp-content/themes/your-theme-name/`, then

- replace `'_s'` with `'your-theme-slug'` and `_s_` with `your_theme_slug_`
- update the `style.css` header
- activate it in the admin or with `wp theme activate your-theme-slug`

Customizing
---------------

Colors, typography, spacing, shadows and layout are set in `theme.json`. The CSS Custom Properties in `style.css` (`:root`) read from those presets.

Development tools
---------------

```sh
composer install
composer lint:wpcs   # WordPress Coding Standards
composer lint:php    # PHP syntax
composer make-pot    # regenerate languages/_s.pot (needs WP-CLI)
```

Browser support
---------------

Current versions of Chrome, Edge, Firefox and Safari. The CSS uses Custom Properties, Grid and Flexbox `gap`; there is no polyfill or transpilation.

Performance
---------------

`inc/performance.php`:
- Emoji scripts and jQuery Migrate removed
- Emoji DNS prefetch removed (resource hints added by plugins are kept)
- Dashicons not loaded for logged-out visitors
- Heartbeat API disabled on the frontend

Left to WordPress core on purpose, because overriding it breaks plugins:
- Script loading strategy: register scripts with the `strategy` argument instead of forcing `defer` on every script
- `ver` query strings: they are the cache busting after updates
- Block and global styles: `global-styles` holds the `theme.json` CSS variables used by `style.css`
- Speculative loading (WordPress 6.8+)

Revisions and autosave interval belong in `wp-config.php` (`WP_POST_REVISIONS`, `AUTOSAVE_INTERVAL`).

Security
---------------

`inc/security.php`:
- Headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy. HSTS and Content-Security-Policy are left to the server or CDN.
- WordPress version, generator and unnecessary `<head>` tags removed
- Generic login error message
- XML-RPC and pingbacks disabled
- File editing disabled (`DISALLOW_FILE_EDIT`)
- Comments disabled site-wide
- Core `wp/v2` REST namespace limited to logged-in users. Other namespaces, such as a plugin's `wp-statistics/v2`, stay reachable for anonymous visitors. To restrict another namespace:
  ```php
  add_filter( '_s_rest_blocked_namespaces', fn( array $ns ): array => [ ...$ns, 'some-plugin/v1' ] );
  ```
- User enumeration blocked: anonymous REST user routes, numeric `?author=N` and author archives, users sitemap, author data in oEmbed, and the lost-password form no longer reveals whether an account exists

Opinionated defaults
---------------

Four defaults can be switched off with the `_s_features` filter, for example in a small plugin or a child theme:

```php
add_filter( '_s_features', fn( array $features ): array => [
	...$features,
	'disable_comments'  => false,
	'disable_heartbeat' => false,
] );
```

All keys are `true` by default:
- `disable_comments`: comments, pingbacks and the comments admin screens are disabled
- `remove_widgets`: Archives, Calendar, Categories, Meta, Recent Comments, Recent Posts, RSS and Tag Cloud widgets are unregistered
- `disable_heartbeat`: Heartbeat API is deregistered on the frontend
- `remove_dashicons`: Dashicons are not loaded for logged-out visitors

Everything else can be changed in `inc/security.php` and `inc/performance.php` directly.

File structure
---------------

```
_s/
├── inc/
│   ├── custom-header.php      # Custom header
│   ├── customizer.php         # Customizer settings
│   ├── template-tags.php      # Template tags
│   ├── template-functions.php # Template helpers
│   ├── performance.php        # Performance defaults
│   └── security.php           # Security defaults
├── js/
│   └── customizer.js          # Customizer live preview (only JS in the theme)
├── template-parts/            # Template parts
├── functions.php              # Theme setup
├── style.css                  # Stylesheet
├── editor-style.css           # Block editor styles
├── theme.json                 # Theme configuration (V3)
├── install-theme.sh           # Install script
├── INSTALL.md                 # Installation guide
└── *.php                      # Templates
```

License
---------------

GPL v2 or later, like WordPress. Based on Underscores https://underscores.me/, (C) 2012-2025 Automattic, Inc.
