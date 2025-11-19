<?php
/**
 * Basic security improvements (always active)
 */
function _s_add_security_headers(): void {
	// Remove WordPress version from head
	remove_action( 'wp_head', 'wp_generator' );

	// Disable XML-RPC (security risk)
	// Note: Some plugins (like Jetpack) require XML-RPC to be enabled
	// Comment out the line below if you need XML-RPC
	add_filter( 'xmlrpc_enabled', '__return_false' );

	// Disable file editing in admin (production security)
	if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}
}
add_action( 'init', '_s_add_security_headers' );

/**
 * Send security headers
 *
 * Note: These headers are generally safe but some plugins might not work with them.
 * Adjust as needed for your use case.
 */
function _s_send_security_headers(): void {
	// Only send headers if not in admin
	if ( is_admin() ) {
		return;
	}

	// Safe headers that should work with all plugins
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Optional headers (uncomment if needed)
	// Note: X-XSS-Protection is deprecated in modern browsers
	// header( 'X-XSS-Protection: 1; mode=block' );

	// Permissions-Policy might interfere with some plugins (Google Maps, etc.)
	// Adjust the directives based on your needs
	// header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
}
add_action( 'send_headers', '_s_send_security_headers' );

/**
 * Remove WordPress version from RSS feeds
 *
 * @return string
 */
function _s_remove_version_rss(): string {
	return '';
}
add_filter( 'the_generator', '_s_remove_version_rss' );

/**
 * Disable login hints
 *
 * @return string
 */
function _s_disable_login_errors(): string {
	return esc_html__( 'Login failed. Please try again.', '_s' );
}
add_filter( 'login_errors', '_s_disable_login_errors' );

/**
 * Remove unnecessary meta tags
 */
function _s_remove_meta_tags(): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
}
add_action( 'init', '_s_remove_meta_tags' );
