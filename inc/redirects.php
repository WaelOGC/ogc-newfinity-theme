<?php
/**
 * Legacy URL redirects (301) only when WordPress would otherwise return 404.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map legacy request paths to their canonical destinations.
 *
 * @return array<string, string> Path segment (no slashes) => absolute destination URL.
 */
function ogc_get_legacy_redirect_map() {
	return array(
		'mission' => home_url( '/our-mission/' ),
		'help'    => home_url( '/help-support/' ),
	);
}

/**
 * If the current request is a 404 for a known legacy path, redirect permanently.
 */
function ogc_maybe_redirect_legacy_paths() {
	if ( ! is_404() ) {
		return;
	}

	$path = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path = (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	}
	$path = trim( $path, '/' );

	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return;
	}

	$map = ogc_get_legacy_redirect_map();
	if ( ! isset( $map[ $path ] ) ) {
		return;
	}

	// Never override a real published page with the same slug.
	$existing = get_page_by_path( $path );
	if ( $existing instanceof WP_Post && 'publish' === $existing->post_status ) {
		return;
	}

	wp_safe_redirect( $map[ $path ], 301 );
	exit;
}
add_action( 'template_redirect', 'ogc_maybe_redirect_legacy_paths', 1 );
