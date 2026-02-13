<?php

declare(strict_types=1);

/**
 * SEO and GEO (Generative Engine Optimization) enhancements
 *
 * Provides basic structured data (JSON-LD), meta tags, and GEO signals
 * so the theme works well with search engines AND AI crawlers/LLMs.
 *
 * Designed to be non-conflicting with SEO plugins (Yoast, Rank Math, AIOSEO).
 * If an SEO plugin is active, its output takes priority.
 *
 * @package _s
 * @since 2.2.0
 */

/**
 * Check if a known SEO plugin is active
 *
 * Prevents duplicate structured data and meta tag output.
 *
 * @return bool
 */
function _s_seo_plugin_active(): bool {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$seo_plugins = array(
		'wordpress-seo/wp-seo.php',
		'wordpress-seo-premium/wp-seo-premium.php',
		'seo-by-rank-math/rank-math.php',
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
		'schema-and-structured-data-for-wp/structured-data-for-wp.php',
	);

	foreach ( $seo_plugins as $plugin ) {
		if ( is_plugin_active( $plugin ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Output JSON-LD structured data in the head
 *
 * Outputs WebSite schema on the front page and Article schema on singular posts.
 * This provides baseline structured data for search engines and AI crawlers.
 * Skipped entirely if an SEO plugin is detected.
 */
function _s_output_structured_data(): void {
	if ( _s_seo_plugin_active() ) {
		return;
	}

	$schema = array();

	if ( is_front_page() ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
		);

		$description = get_bloginfo( 'description' );
		if ( $description ) {
			$schema['description'] = $description;
		}

		// Add search action for sitelinks search box
		$schema['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		);
	} elseif ( is_singular( 'post' ) ) {
		$post = get_post();
		if ( ! $post ) {
			return;
		}

		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title( $post ),
			'url'           => get_permalink( $post ),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
		);

		$author = get_the_author_meta( 'display_name', (int) $post->post_author );
		if ( $author ) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name'  => $author,
			);
		}

		$description = get_the_excerpt( $post );
		if ( $description ) {
			$schema['description'] = wp_strip_all_tags( $description );
		}

		if ( has_post_thumbnail( $post ) ) {
			$image_url = get_the_post_thumbnail_url( $post, 'full' );
			if ( $image_url ) {
				$schema['image'] = $image_url;
			}
		}

		$schema['publisher'] = array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);

		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			if ( $logo_url ) {
				$schema['publisher']['logo'] = array(
					'@type' => 'ImageObject',
					'url'   => $logo_url,
				);
			}
		}
	}

	if ( empty( $schema ) ) {
		return;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
	);
}
add_action( 'wp_head', '_s_output_structured_data', 1 );

/**
 * Add meta description from excerpt or tagline
 *
 * Basic meta description for pages without an SEO plugin.
 * Uses the excerpt for singular posts/pages, tagline for front page.
 */
function _s_output_meta_description(): void {
	if ( _s_seo_plugin_active() ) {
		return;
	}

	$description = '';

	if ( is_front_page() ) {
		$description = get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$post = get_post();
		if ( $post ) {
			$description = get_the_excerpt( $post );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	}

	$description = wp_strip_all_tags( (string) $description );
	$description = trim( $description );

	if ( $description ) {
		// Truncate to 160 characters for optimal SERP display
		if ( mb_strlen( $description ) > 160 ) {
			$description = mb_substr( $description, 0, 157 ) . '...';
		}
		printf(
			'<meta name="description" content="%s">' . "\n",
			esc_attr( $description )
		);
	}
}
add_action( 'wp_head', '_s_output_meta_description', 1 );

/**
 * Output Open Graph meta tags for better social sharing and AI discovery
 *
 * Provides og:title, og:description, og:url, og:type, og:image.
 * These also help AI crawlers understand page content.
 */
function _s_output_open_graph(): void {
	if ( _s_seo_plugin_active() ) {
		return;
	}

	$og_tags = array();

	$og_tags['og:site_name'] = get_bloginfo( 'name' );
	$og_tags['og:locale'] = get_locale();

	if ( is_front_page() ) {
		$og_tags['og:type'] = 'website';
		$og_tags['og:title'] = get_bloginfo( 'name' );
		$og_tags['og:url'] = home_url( '/' );
		$description = get_bloginfo( 'description' );
		if ( $description ) {
			$og_tags['og:description'] = $description;
		}
	} elseif ( is_singular() ) {
		$post = get_post();
		if ( ! $post ) {
			return;
		}

		$og_tags['og:type'] = ( 'post' === $post->post_type ) ? 'article' : 'website';
		$og_tags['og:title'] = get_the_title( $post );
		$og_tags['og:url'] = get_permalink( $post );

		$excerpt = get_the_excerpt( $post );
		if ( $excerpt ) {
			$og_tags['og:description'] = wp_strip_all_tags( $excerpt );
		}

		if ( has_post_thumbnail( $post ) ) {
			$image_url = get_the_post_thumbnail_url( $post, 'large' );
			if ( $image_url ) {
				$og_tags['og:image'] = $image_url;
			}
		}

		if ( 'post' === $post->post_type ) {
			$og_tags['article:published_time'] = get_the_date( 'c', $post );
			$og_tags['article:modified_time'] = get_the_modified_date( 'c', $post );
		}
	}

	foreach ( $og_tags as $property => $content ) {
		printf(
			'<meta property="%s" content="%s">' . "\n",
			esc_attr( $property ),
			esc_attr( $content )
		);
	}
}
add_action( 'wp_head', '_s_output_open_graph', 2 );

/**
 * Add canonical URL to prevent duplicate content issues
 *
 * WordPress 6.4+ has wp_get_canonical_url() but doesn't always output the tag.
 */
function _s_output_canonical(): void {
	if ( _s_seo_plugin_active() ) {
		return;
	}

	if ( is_singular() ) {
		$canonical = wp_get_canonical_url();
		if ( $canonical ) {
			printf(
				'<link rel="canonical" href="%s">' . "\n",
				esc_url( $canonical )
			);
		}
	} elseif ( is_front_page() ) {
		printf(
			'<link rel="canonical" href="%s">' . "\n",
			esc_url( home_url( '/' ) )
		);
	}
}
add_action( 'wp_head', '_s_output_canonical', 1 );

/**
 * Optimize robots meta for archive and search pages
 *
 * Prevents thin content pages from being indexed while keeping
 * the main content pages fully indexable.
 */
function _s_robots_meta(): void {
	if ( _s_seo_plugin_active() ) {
		return;
	}

	// Don't index search results or paginated archives (thin content)
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	} elseif ( is_paged() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
}
add_action( 'wp_head', '_s_robots_meta', 1 );
