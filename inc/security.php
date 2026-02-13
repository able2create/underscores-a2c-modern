<?php

declare(strict_types=1);

/**
 * Security enhancements for WordPress
 *
 * Designed for sites with editors/admins only (no public user registration).
 * Plugin-compatible: avoids overly restrictive rules that break common plugins.
 *
 * @package _s
 * @since 2.1.0
 * @since 2.2.0 Added CSP, Permissions-Policy, admin hardening, login protection.
 */

/**
 * Core security setup (always active)
 */
function _s_add_security_headers(): void {
	// Remove WordPress version from head
	remove_action( 'wp_head', 'wp_generator' );

	// Disable XML-RPC (security risk, not needed for editor/admin-only sites)
	// Note: Jetpack, WordPress mobile app, and some pingback services need XML-RPC.
	// Re-enable selectively via xmlrpc_methods filter if a specific plugin requires it.
	add_filter( 'xmlrpc_enabled', '__return_false' );

	// Block XML-RPC request methods entirely to reduce attack surface
	add_filter( 'xmlrpc_methods', '__return_empty_array' );

	// Disable file editing in admin (production security)
	if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}

	// Disable user registration (admin/editor-only site)
	if ( get_option( 'users_can_register' ) ) {
		update_option( 'users_can_register', 0 );
	}
}
add_action( 'init', '_s_add_security_headers' );

/**
 * Send security headers on the frontend
 *
 * These headers protect visitors against XSS, clickjacking, and MIME sniffing.
 * CSP is set to report-only by default so plugins don't break.
 * Permissions-Policy is intentionally permissive for plugin compatibility.
 */
function _s_send_security_headers(): void {
	if ( is_admin() ) {
		return;
	}

	// Prevent MIME-type sniffing
	header( 'X-Content-Type-Options: nosniff' );

	// Prevent clickjacking - SAMEORIGIN allows embeds from own domain
	header( 'X-Frame-Options: SAMEORIGIN' );

	// Control referrer information
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Permissions-Policy: permissive defaults that won't break plugins
	// Plugins like Google Maps, video embeds, payment providers need these.
	// Only restrict features that an editorial site truly never uses.
	header( 'Permissions-Policy: interest-cohort=()' );

	// Content-Security-Policy in report-only mode
	// This logs violations without blocking anything, safe for all plugins.
	// Review browser console for violations, then promote to enforcing mode.
	// To enforce: change Content-Security-Policy-Report-Only to Content-Security-Policy
	$csp_directives = array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' 'unsafe-eval'",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: https:",
		"font-src 'self' data:",
		"connect-src 'self' https:",
		"frame-src 'self' https:",
		"frame-ancestors 'self'",
		"base-uri 'self'",
		"form-action 'self'",
	);
	header( 'Content-Security-Policy-Report-Only: ' . implode( '; ', $csp_directives ) );

	// Cross-Origin headers for modern browser security
	header( 'Cross-Origin-Opener-Policy: same-origin-allow-popups' );
}
add_action( 'send_headers', '_s_send_security_headers' );

/**
 * Send security headers in admin area
 *
 * Admin-specific hardening since only trusted editors/admins use it.
 */
function _s_send_admin_security_headers(): void {
	if ( ! is_admin() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}
add_action( 'send_headers', '_s_send_admin_security_headers' );

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
 * Disable login error hints (prevents username enumeration)
 *
 * @return string
 */
function _s_disable_login_errors(): string {
	return esc_html__( 'Login failed. Please try again.', '_s' );
}
add_filter( 'login_errors', '_s_disable_login_errors' );

/**
 * Disable user enumeration via REST API
 *
 * Prevents /wp-json/wp/v2/users from exposing user data to unauthenticated requests.
 * Editors/admins can still see user data when logged in.
 *
 * @param mixed $result Current result.
 * @return mixed
 */
function _s_restrict_rest_users( mixed $result ): mixed {
	if ( ! is_user_logged_in() ) {
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( preg_match( '#/wp/v2/users#', $request_uri ) ) {
			return new \WP_Error(
				'rest_forbidden',
				esc_html__( 'Access denied.', '_s' ),
				array( 'status' => 401 )
			);
		}
	}
	return $result;
}
add_filter( 'rest_authentication_errors', '_s_restrict_rest_users' );

/**
 * Disable user enumeration via author archives (?author=1)
 *
 * Redirects unauthenticated author queries to the home page.
 */
function _s_disable_author_enum(): void {
	if ( ! is_user_logged_in() && isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', '_s_disable_author_enum' );

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

/**
 * Disable pingbacks entirely (reduces spam and DDoS vector)
 *
 * On an admin/editor-only site, pingbacks are rarely useful.
 * Re-enable selectively if you need trackbacks/pingbacks.
 */
function _s_disable_pingbacks(): void {
	// Disable self-pingbacks
	add_action(
		'pre_ping',
		static function ( array &$links ): void {
			$home_url = home_url();
			foreach ( $links as $i => $link ) {
				if ( str_starts_with( $link, $home_url ) ) {
					unset( $links[ $i ] );
				}
			}
		}
	);
}
add_action( 'init', '_s_disable_pingbacks' );

/**
 * Add security-related headers to login page
 *
 * Hardens the login page specifically since it's the main attack surface.
 */
function _s_login_security_headers(): void {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: DENY' );
	header( 'Referrer-Policy: same-origin' );
	header( 'Permissions-Policy: interest-cohort=()' );
}
add_action( 'login_init', '_s_login_security_headers' );
