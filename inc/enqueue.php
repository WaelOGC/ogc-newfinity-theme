<?php
/**
 * Front-end styles and scripts.
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
	wp_enqueue_style(
		'ogc-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap',
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
		'ogc-background',
		OGC_THEME_URI . '/assets/css/background.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-glass',
		OGC_THEME_URI . '/assets/css/glass.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-main',
		OGC_THEME_URI . '/assets/css/main.css',
		array( 'ogc-background', 'ogc-glass' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-header',
		OGC_THEME_URI . '/assets/css/header.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-footer',
		OGC_THEME_URI . '/assets/css/footer.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);

	if ( is_front_page() ) {
		wp_enqueue_style(
			'ogc-home',
			OGC_THEME_URI . '/assets/css/home.css',
			array( 'ogc-main', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
	}

	wp_enqueue_script(
		'ogc-three',
		'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
		array(),
		'r128',
		true
	);

	wp_enqueue_script(
		'ogc-space-background',
		OGC_THEME_URI . '/assets/js/space-background.js',
		array( 'ogc-three' ),
		OGC_THEME_VERSION,
		true
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'ogc-brand-cube',
			OGC_THEME_URI . '/assets/js/brand-cube.js',
			array( 'ogc-three' ),
			OGC_THEME_VERSION,
			true
		);
	}

	wp_enqueue_script(
		'ogc-main',
		OGC_THEME_URI . '/assets/js/main.js',
		array(),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ogc_enqueue_assets' );
