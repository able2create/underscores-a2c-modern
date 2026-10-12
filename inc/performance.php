<?php


/**
 * Performance optimizations
 *
 * @package _s
 */

// ─── Emoji ────────────────────────────────────────────────────────────────────

/**
 * Disable WordPress emoji scripts.
 * Modern browsers handle emojis natively – saves ~20KB JS and one external request.
 */
function _s_disable_emojis(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', '_s_disable_emojis' );

add_filter( 'tiny_mce_plugins', function ( array $plugins ): array {
	return array_diff( $plugins, [ 'wpemoji' ] );
} );

// ─── Scripts & Styles ─────────────────────────────────────────────────────────

/**
 * Remove jQuery Migrate (not needed for modern code).
 */
add_action( 'wp_default_scripts', function ( $scripts ): void {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, [ 'jquery-migrate' ] );
		}
	}
} );

/**
 * Disable dashicons on frontend for non-logged-in users.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
} );

/**
 * Disable wp-embed script (not needed if embeds aren't used).
 */
add_action( 'wp_footer', function (): void {
	wp_deregister_script( 'wp-embed' );
} );

// ─── Heartbeat ────────────────────────────────────────────────────────────────

/**
 * Disable WordPress heartbeat API on frontend (reduces AJAX requests).
 * Remove this if a plugin needs heartbeat on the frontend (live chat, LMS, ...).
 */
add_action( 'init', function (): void {
	if ( ! is_admin() ) {
		wp_deregister_script( 'heartbeat' );
	}
} );

// ─── Resource Hints ───────────────────────────────────────────────────────────

/**
 * Remove the core emoji DNS prefetch (s.w.org) but keep hints added by plugins.
 */
add_filter( 'wp_resource_hints', function ( array $urls, string $relation_type ): array {
	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}
	return array_values(
		array_filter(
			$urls,
			fn( $url ): bool => ! str_contains( is_array( $url ) ? (string) ( $url['href'] ?? '' ) : (string) $url, 's.w.org' )
		)
	);
}, 10, 2 );
