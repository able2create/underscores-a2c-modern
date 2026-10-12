<?php


/**
 * Security hardening
 *
 * @package _s
 */

// ─── Clean up <head> ──────────────────────────────────────────────────────────

remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );

// ─── XML-RPC, oEmbed, Pingback ────────────────────────────────────────────────

add_filter( 'xmlrpc_enabled', '__return_false' );

add_filter( 'wp_headers', function ( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
} );

add_filter( 'xmlrpc_methods', function ( array $methods ): array {
	unset( $methods['pingback.ping'] );
	return $methods;
} );

// ─── REST API ─────────────────────────────────────────────────────────────────

/**
 * Restrict the core wp/v2 namespace (posts, pages, users, media, comments,
 * taxonomies, templates, ...) to logged-in users, since it can expose
 * content and site structure to anonymous scraping/enumeration.
 *
 * Third-party namespaces (e.g. wp-statistics/v2, contact-form-7/v1) are left
 * untouched, since plugins often need anonymous visitors to reach them.
 * Add a namespace to `_s_rest_blocked_namespaces` to lock down a specific
 * plugin endpoint too, e.g.:
 *   add_filter( '_s_rest_blocked_namespaces', fn( $ns ) => [ ...$ns, 'some-plugin/v1' ] );
 */
add_filter( 'rest_authentication_errors', function ( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}

	if ( is_user_logged_in() ) {
		return $result;
	}

	$rest_route = ltrim( $GLOBALS['wp']->query_vars['rest_route'] ?? '', '/' );

	$blocked_namespaces = apply_filters( '_s_rest_blocked_namespaces', [ 'wp/v2' ] );

	foreach ( $blocked_namespaces as $namespace ) {
		$namespace = trim( $namespace, '/' );

		if ( $rest_route === $namespace || str_starts_with( $rest_route, $namespace . '/' ) ) {
			return new WP_Error(
				'rest_not_logged_in',
				__( 'REST API access restricted.', '_s' ),
				[ 'status' => 401 ]
			);
		}
	}

	return $result;
} );

/**
 * Disable user enumeration via REST endpoints for anonymous visitors.
 * Logged-in users keep the routes (block editor author panel needs them).
 */
add_filter( 'rest_endpoints', function ( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	return $endpoints;
} );

// ─── Comments ─────────────────────────────────────────────────────────────────

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

add_filter( 'comments_template', function (): string {
	return get_template_directory() . '/comments-disabled.php';
}, 20 );

add_action( 'admin_menu', function (): void {
	remove_menu_page( 'edit-comments.php' );
} );

add_action( 'admin_init', function (): void {
	global $pagenow;

	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
} );

add_action( 'init', function (): void {
	if ( is_admin_bar_showing() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
	}
} );

// ─── Prevent User Enumeration ─────────────────────────────────────────────────

/**
 * Redirect author archive pages (prevents username exposure via URL).
 * Priority 1 runs before redirect_canonical, which would otherwise answer
 * ?author=1 with a redirect to /author/username/.
 */
add_action( 'template_redirect', function (): void {
	if ( is_author() || isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );

/**
 * Remove the users sitemap (wp-sitemap-users-1.xml lists author slugs).
 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

/**
 * Remove author name and URL from oEmbed responses.
 */
add_filter( 'oembed_response_data', function ( array $data ): array {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
} );

/**
 * Remove author-* class from body (prevents username exposure in markup).
 */
add_filter( 'body_class', function ( array $classes ): array {
	foreach ( $classes as $key => $class ) {
		if ( str_starts_with( $class, 'author-' ) ) {
			unset( $classes[ $key ] );
		}
	}
	return $classes;
}, 20 );

/**
 * Generic login error message (prevents username enumeration).
 */
add_filter( 'login_errors', function (): string {
	return __( 'Login credentials are incorrect.', '_s' );
} );

/**
 * Lost password: answer unknown usernames like known ones
 * (core otherwise reveals whether an account exists).
 */
add_filter( 'lostpassword_errors', function ( WP_Error $errors ): WP_Error {
	$on_login_page = 'wp-login.php' === ( $GLOBALS['pagenow'] ?? '' );
	if ( $on_login_page && 'invalidcombo' === $errors->get_error_code() ) {
		wp_safe_redirect( add_query_arg( 'checkemail', 'confirm', wp_login_url() ) );
		exit;
	}
	return $errors;
} );

// ─── Hide WordPress Version ───────────────────────────────────────────────────

add_filter( 'the_generator', '__return_empty_string' );

add_filter( 'style_loader_src', function ( string $src ): string {
	if ( $src && str_contains( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}, 9999 );

add_filter( 'script_loader_src', function ( string $src ): string {
	if ( $src && str_contains( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}, 9999 );

// ─── Dashboard Hardening ──────────────────────────────────────────────────────

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

add_filter( 'update_footer', '__return_empty_string', 11 );

// ─── HTTP Security Headers ────────────────────────────────────────────────────

add_action( 'send_headers', function (): void {
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
} );
