=== Underscores A2C Modern ===

Contributors: able2create
Tags: accessibility-ready, custom-background, custom-logo, custom-menu, featured-images, translation-ready, one-column

Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.4
Stable tag: 3.1.2
License: GNU General Public License v2 or later
License URI: LICENSE

A minimal, modern WordPress starter theme. Built with PHP 8.4+, pure CSS/JS. No build process. Plugin-friendly. Based on Underscores.

== Description ==

A modernized fork of the Underscores (_s) starter theme. PHP 8.4+, theme.json V3 and plain CSS; the only JavaScript is the Customizer live preview. inc/security.php and inc/performance.php ship hardening defaults, and comments, widget removal, frontend heartbeat and Dashicons removal can be switched off with the `_s_features` filter.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

= Which plugin features does the theme restrict? =

inc/security.php disables XML-RPC, pingbacks, file editing and comments, and limits the core wp/v2 REST namespace to logged-in users. REST namespaces of plugins (for example wp-statistics/v2) stay reachable for anonymous visitors; add a namespace to the `_s_rest_blocked_namespaces` filter to restrict it. Comments, widget removal, frontend heartbeat and Dashicons removal can be turned off with the `_s_features` filter. See README.md for the details.

= Is it compatible with WordPress 7.1? =

Yes, tested with WordPress 7.1. The block editor is always iframed; editor-style.css and theme.json style it. theme.json also styles the Breadcrumbs and Icon blocks.

== Changelog ==

= 3.1.2 =
* Documentation: README.md and readme.txt shortened, repeated and unverifiable statements removed, FAQ now describes the actual restrictions and the `_s_features` filter
* Removed the `two-columns` tag; the theme has no sidebar layout
* README no longer lists specific browser versions (the old numbers did not match the CSS in use)

= 3.1.1 =
* Fixed: 404 page used widgets that the theme itself unregisters; it now renders recent posts, archives and tags directly
* Fixed: the registered Footer menu is now displayed in the footer
* Fixed: ?author= redirect only applies to numeric values, so plugins can use the parameter
* Added: `_s_features` filter to switch off comments, widget removal, frontend heartbeat and Dashicons removal
* Added: responsive rules for galleries and the navigation, which previously had none
* Added return types to the Customizer partial callbacks
* composer.json authors now point to this repository

= 3.1.0 =
* WordPress 7.1 "Mary Lou" support
* Fixed: REST user routes were removed for logged-in users too, breaking the block editor's author panel
* Fixed: ?author=N leaked usernames via core's canonical redirect; author archives are now redirected before it
* Users sitemap and author data in oEmbed responses removed; lost-password form no longer reveals existing accounts
* Author byline no longer links to (redirected) author archives
* Removed forced `defer` on all scripts (broke inline scripts of plugins and core); core's loading strategy is used instead
* Removed stripping of `ver` query strings (broke cache busting after updates)
* Global styles and block library CSS are no longer dequeued (theme.json variables were missing on pages without blocks)
* Plugin resource hints are kept; only the core emoji DNS prefetch is removed
* Removed code without effect: revision/autosave constants (set them in wp-config.php), speculative loading removal, lazy loading filter, classic-theme-styles dequeue
* style.css and editor-style.css now use the theme.json palette and font families as single source
* Development tooling: WPCS 3, PHPCompatibilityWP for PHP 8.4, CI on PHP 8.4; removed CSS/JS workflows that referenced a non-existent npm setup

= 3.0.1 =
* REST API hardening now only restricts the core wp/v2 namespace instead of blocking the whole REST API, so plugins with their own REST namespace (e.g. WP-Statistics) keep working for anonymous visitors
* Blocked namespace list is filterable via `_s_rest_blocked_namespaces`
* Fixed a gap where the wp/v2 namespace index/schema route was still reachable anonymously

= 3.0.0 =
* WordPress 7.0 "Armstrong" support
* theme.json styling for the new core Breadcrumbs and Icon blocks
* Block-level :hover/:focus states for the Navigation block via theme.json
* Removed the obsolete X-XSS-Protection security header (ignored by modern browsers)
* Unified version number across style.css, readme.txt and functions.php
* Corrected outdated documentation

= 2.2.0 =
* Modernized fork with PHP 8.4+, theme.json V3, modern CSS and ES6+ JS

== Credits ==

* Based on Underscores https://underscores.me/, (C) 2012-2025 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
