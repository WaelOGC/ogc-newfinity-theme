<?php
/**
 * Theme supports, menus, and related setup.
 *
 * Skeleton only — image sizes and further supports land in later steps.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and nav menu locations.
 */
function ogc_theme_setup() {
	load_theme_textdomain( 'ogc-newfinity', OGC_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'ogc-newfinity' ),
		)
	);
}
add_action( 'after_setup_theme', 'ogc_theme_setup' );
