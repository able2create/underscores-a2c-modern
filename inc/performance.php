<?php

declare(strict_types=1);

/**
 * Performance optimizations for WordPress
 *
 * Leverages WP 6.9+ features: fetchpriority, Speculation Rules API,
 * on-demand block styles for classic themes, and modern resource hints.
 *
 * @package _s
 * @since 2.1.0
 * @since 2.2.0 Added fetchpriority, Speculation Rules, WP 6.9+ optimizations.
 */

/**
 * Smart lazy loading for post thumbnails
 *
 * Skips lazy loading on the first image (LCP candidate) for better Core Web Vitals.
 * Uses fetchpriority="high" for above-the-fold images (WP 6.3+).
 *
 * @param array<string,string> $attr Image attributes.
 * @return array<string,string>
 */
function _s_add_lazy_loading( array $attr ): array {
	static $first_image = true;

	if ( $first_image && ( is_singular() || is_front_page() ) ) {
		// First image is likely the LCP element - don't lazy load it
		$attr['loading'] = 'eager';
		$attr['decoding'] = 'async';
		$attr['fetchpriority'] = 'high';
		$first_image = false;
	} else {
		$attr['loading'] = 'lazy';
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', '_s_add_lazy_loading' );

/**
 * Disable emoji script
 *
 * Modern browsers (Chrome 88+, Firefox 87+, Safari 14+) have native emoji support.
 * Saves ~20KB of JS and one external request.
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
 * Add Speculation Rules API for instant page navigation
 *
 * Prerenders links on hover for near-instant page transitions.
 * Supported by Chrome 108+ and Edge 108+. Ignored by other browsers.
 * Uses conservative rules: only same-origin, moderate eagerness.
 *
 * @since 2.2.0
 */
function _s_add_speculation_rules(): void {
	if ( is_admin() ) {
		return;
	}

	$speculation_rules = array(
		'prerender' => array(
			array(
				'where' => array(
					'and' => array(
						array( 'href_matches' => '/*' ),
						array(
							'not' => array(
								'href_matches' => array(
									'/wp-admin/*',
									'/wp-login.php',
									'/*\\?*',
									'/*#*',
									'/*.pdf',
									'/*.zip',
								),
							),
						),
						array(
							'not' => array(
								'selector_matches' => array(
									'[rel~=nofollow]',
									'[download]',
									'[target=_blank]',
								),
							),
						),
					),
				),
				'eagerness' => 'moderate',
			),
		),
	);

	// wp_json_encode() returns false on failure (e.g. invalid UTF-8); guard to
	// prevent an empty <script> tag from being emitted with invalid JSON content.
	$json = wp_json_encode( $speculation_rules, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
	if ( $json ) {
		printf(
			'<script type="speculationrules">%s</script>' . "\n",
			$json
		);
	}
}
add_action( 'wp_footer', '_s_add_speculation_rules', 99 );

/**
 * Remove query strings from static resources (uncomment if needed)
 *
 * Note: Some caching plugins (WP Rocket, W3TC) handle this better.
 * Only enable if you don't use a caching plugin.
 */
/*
function _s_remove_query_strings( string $src ): string {
	if ( str_contains( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'script_loader_src', '_s_remove_query_strings', 15 );
add_filter( 'style_loader_src', '_s_remove_query_strings', 15 );
*/

/**
 * Add preconnect for external domains (uncomment and customize as needed)
 *
 * Add your external domains here (fonts, CDNs, analytics, etc.)
 */
/*
function _s_add_resource_hints(): void {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
	<link rel="dns-prefetch" href="https://fonts.googleapis.com">
	<?php
}
add_action( 'wp_head', '_s_add_resource_hints', 1 );
*/

/**
 * Remove unnecessary head links
 *
 * Removes feed links, REST API discovery, and oEmbed links for cleaner HTML.
 * Feed links are re-added via add_theme_support('automatic-feed-links') if needed.
 */
function _s_cleanup_head(): void {
	// Remove feed links (plugins can re-add if needed)
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	// Remove REST API discovery link (API still works, just not advertised)
	remove_action( 'wp_head', 'rest_output_link_wp_head' );

	// Remove oEmbed discovery links
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Remove REST API link in HTTP headers
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );
}
add_action( 'init', '_s_cleanup_head' );

/**
 * Optimize WP 6.9+ block styles for classic themes
 *
 * WP 6.9 introduces on-demand CSS loading for classic themes via
 * the template enhancement output buffer. This can reduce page CSS by 30-65%.
 * Enable this filter to opt in explicitly.
 *
 * @since 2.2.0
 */
function _s_enable_template_enhancement_buffer(): bool {
	return true;
}
add_filter( 'wp_template_enhancement_output_buffer', '_s_enable_template_enhancement_buffer' );
