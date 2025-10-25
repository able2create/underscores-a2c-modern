<?php

declare(strict_types=1);

/**
 * Security enhancements
 *
 * @package _s
 */

/**
 * Add security headers
 */
function _s_add_security_headers(): void {
	// Remove WordPress version from head
	remove_action( 'wp_head', 'wp_generator' );

	// Disable XML-RPC if not needed
	add_filter( 'xmlrpc_enabled', '__return_false' );

	// Disable file editing in admin
	if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}
}
add_action( 'init', '_s_add_security_headers' );

/**
 * Send security headers
 */
function _s_send_security_headers(): void {
	// Only send headers if not in admin
	if ( is_admin() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'X-XSS-Protection: 1; mode=block' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
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
