=== Underscores A2C Modern ===

Contributors: able2create
Tags: accessibility-ready, custom-background, custom-logo, custom-menu, featured-images, translation-ready, one-column, two-columns

Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.4
Stable tag: 3.0.0
License: GNU General Public License v2 or later
License URI: LICENSE

A minimal, modern WordPress starter theme. Built with PHP 8.4+, pure CSS/JS. No build process. Plugin-friendly. Based on Underscores.

== Description ==

A modernized fork of the original Underscores (_s) starter theme, updated for 2025+ WordPress development. Built with PHP 8.4+, modern CSS (Custom Properties, Container Queries, fluid typography), and ES6+ JavaScript — no build process required. Plugin-friendly, performance-optimized, and security-hardened out of the box.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

= Does this theme support any plugins? =

This theme is designed to work with all popular WordPress plugins. Note that inc/security.php hardens the site by default — XML-RPC and pingbacks are disabled, the REST API is limited to logged-in users, and file editing is disabled. Adjust inc/security.php and inc/performance.php if a plugin needs any of these.

= Is it compatible with WordPress 7.0? =

Yes. The theme is tested with WordPress 7.0 "Armstrong". AI Connectors, Responsive Block Visibility, Custom CSS per block and the Font Library work out of the box with no theme configuration. theme.json provides styling for the new Breadcrumbs and Icon blocks.

== Changelog ==

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
