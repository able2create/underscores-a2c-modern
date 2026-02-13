<?php

declare(strict_types=1);

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array<string> $classes Classes for the body element.
 * @return array<string>
 */
function _s_body_classes( array $classes ): array {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', '_s_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 *
 * Disabled by default for admin/editor-only sites (reduces spam/DDoS vector).
 * Uncomment the add_action line below if you need pingback support.
 */
function _s_pingback_header(): void {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
// add_action( 'wp_head', '_s_pingback_header' );
