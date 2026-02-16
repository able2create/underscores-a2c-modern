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
 * Add `defer` to non-critical frontend scripts.
 */
add_filter( 'script_loader_tag', function ( string $tag, string $handle ): string {
	if ( is_admin() ) {
		return $tag;
	}
	$excluded = [ 'jquery-core', 'comment-reply' ];
	if ( ! in_array( $handle, $excluded, true ) && ! str_contains( $tag, 'defer' ) ) {
		$tag = str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}, 10, 2 );

/**
 * Remove query strings from static resources (improves proxy/CDN cache hit rate).
 */
add_filter( 'script_loader_src', function ( string $src ): string {
	return ! is_admin() ? remove_query_arg( 'ver', $src ) : $src;
}, 15 );

add_filter( 'style_loader_src', function ( string $src ): string {
	return ! is_admin() ? remove_query_arg( 'ver', $src ) : $src;
}, 15 );

/**
 * Disable Gutenberg block library CSS when no blocks are used on the page.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! has_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
	}
}, 100 );

/**
 * Remove Gutenberg classic-themes compatibility CSS.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

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

/**
 * Remove Speculation Rules API (unnecessary for small sites).
 */
add_action( 'wp_footer', function (): void {
	remove_action( 'wp_footer', 'wp_print_link_tag', 10 );
}, 0 );

add_action( 'wp_enqueue_scripts', function (): void {
	wp_dequeue_script( 'wp-speculation-rules' );
}, 999 );

// ─── Heartbeat & Autosave ─────────────────────────────────────────────────────

/**
 * Disable WordPress heartbeat API on frontend (reduces AJAX requests).
 */
add_action( 'init', function (): void {
	if ( ! is_admin() ) {
		wp_deregister_script( 'heartbeat' );
	}
} );

if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', 3 );
}

if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
	define( 'AUTOSAVE_INTERVAL', 300 );
}

// ─── DNS Prefetch ─────────────────────────────────────────────────────────────

/**
 * Remove unnecessary DNS prefetches added by WordPress core.
 */
add_action( 'init', function (): void {
	remove_action( 'wp_head', 'wp_resource_hints', 2 );
} );

// ─── Miscellaneous ────────────────────────────────────────────────────────────

/**
 * Enable native lazy loading for images.
 */
add_filter( 'wp_lazy_loading_enabled', '__return_true' );
