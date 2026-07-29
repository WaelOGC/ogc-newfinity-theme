<?php
/**
 * Front-end styles and scripts.
 *
 * Skeleton only — component CSS/JS and AI widget assets come later.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme stylesheets and scripts.
 */
function ogc_enqueue_assets() {
	// Display + body fonts (Playfair Display italic, Inter) — docs/02.
	wp_enqueue_style(
		'ogc-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'ogc-style',
		get_stylesheet_uri(),
		array( 'ogc-fonts' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-main',
		OGC_THEME_URI . '/assets/css/main.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_script(
		'ogc-main',
		OGC_THEME_URI . '/assets/js/main.js',
		array(),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ogc_enqueue_assets' );
