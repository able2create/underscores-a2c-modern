<?php

declare(strict_types=1);

/**
 * Performance optimizations
 *
 * @package _s
 */

/**
 * Add lazy loading to post thumbnails
 *
 * @param array<string,string> $attr Image attributes.
 * @return array<string,string>
 */
function _s_add_lazy_loading( array $attr ): array {
	$attr['loading'] = 'lazy';
	$attr['decoding'] = 'async';
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', '_s_add_lazy_loading' );

/**
 * Disable emoji script
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

/**
 * Remove query strings from static resources
 *
 * @param string $src Source URL.
 * @return string
 */
function _s_remove_query_strings( string $src ): string {
	if ( str_contains( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'script_loader_src', '_s_remove_query_strings', 15 );
add_filter( 'style_loader_src', '_s_remove_query_strings', 15 );

/**
 * Add preconnect for external domains
 */
function _s_add_resource_hints(): void {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
	<link rel="dns-prefetch" href="https://fonts.googleapis.com">
	<?php
}
add_action( 'wp_head', '_s_add_resource_hints', 1 );

/**
 * Defer non-critical CSS
 *
 * @param string $html HTML tag.
 * @param string $handle Style handle.
 * @return string
 */
function _s_defer_non_critical_css( string $html, string $handle ): string {
	// Add handles that should be deferred
	$defer_styles = array();

	if ( in_array( $handle, $defer_styles, true ) ) {
		$html = str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $html );
	}

	return $html;
}
add_filter( 'style_loader_tag', '_s_defer_non_critical_css', 10, 2 );

/**
 * Add async/defer attributes to scripts
 *
 * @param string $tag Script tag.
 * @param string $handle Script handle.
 * @return string
 */
function _s_add_async_defer_attributes( string $tag, string $handle ): string {
	// Scripts that should be async
	$async_scripts = array();

	// Scripts that should be deferred (already handled by wp_enqueue_script strategy param)
	$defer_scripts = array();

	if ( in_array( $handle, $async_scripts, true ) && ! is_admin() ) {
		return str_replace( ' src', ' async src', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', '_s_add_async_defer_attributes', 10, 2 );
