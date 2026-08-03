<?php
/**
 * Register OGC NewFinity Elementor widgets + assets.
 *
 * Loaded only after `elementor/loaded` (see functions.php).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'OGC_ELEMENTOR_WIDGETS_LOADED' ) ) {
	return;
}
define( 'OGC_ELEMENTOR_WIDGETS_LOADED', true );

require_once OGC_THEME_DIR . '/inc/elementor/class-widget-base.php';

/**
 * Register the OGC NewFinity widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
 */
function ogc_elementor_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'ogc-newfinity',
		array(
			'title' => __( 'OGC NewFinity', 'ogc-newfinity' ),
			'icon'  => 'fa fa-plug',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'ogc_elementor_register_category' );

/**
 * Map of widget class => relative file under inc/elementor/widgets/.
 *
 * @return array<string, string>
 */
function ogc_elementor_widget_map() {
	return array(
		'OGC_Elementor_Widget_Badge'            => 'class-badge.php',
		'OGC_Elementor_Widget_Split_Hero'       => 'class-split-hero.php',
		'OGC_Elementor_Widget_Feature_Card'     => 'class-feature-card.php',
		'OGC_Elementor_Widget_Service_Card'     => 'class-service-card.php',
		'OGC_Elementor_Widget_Gradient_Button'  => 'class-gradient-button.php',
		'OGC_Elementor_Widget_Outline_Button'   => 'class-outline-button.php',
		'OGC_Elementor_Widget_Image_Effects'    => 'class-image-effects.php',
		'OGC_Elementor_Widget_Slider'           => 'class-slider.php',
		'OGC_Elementor_Widget_CTA_Band'         => 'class-cta-band.php',
		'OGC_Elementor_Widget_Orbit_Graphic'    => 'class-orbit-graphic.php',
		'OGC_Elementor_Widget_Footer_Meta'      => 'class-footer-meta.php',
		'OGC_Elementor_Widget_Logo_Menu'        => 'class-logo-menu.php',
	);
}

/**
 * Register all OGC widgets with Elementor.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
 */
function ogc_elementor_register_widgets( $widgets_manager ) {
	$dir = OGC_THEME_DIR . '/inc/elementor/widgets/';

	foreach ( ogc_elementor_widget_map() as $class => $file ) {
		$path = $dir . $file;
		if ( ! file_exists( $path ) ) {
			continue;
		}
		require_once $path;
		if ( class_exists( $class ) ) {
			$widgets_manager->register( new $class() );
		}
	}
}
add_action( 'elementor/widgets/register', 'ogc_elementor_register_widgets' );

/**
 * Enqueue widget CSS/JS on Elementor frontend + editor preview.
 */
function ogc_elementor_enqueue_widget_assets() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$style_deps = array();
	foreach ( array( 'ogc-main', 'ogc-button-effects' ) as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' ) ) {
			$style_deps[] = $handle;
		}
	}

	wp_enqueue_style(
		'ogc-elementor-widgets',
		OGC_THEME_URI . '/assets/css/elementor-widgets.css',
		$style_deps,
		OGC_THEME_VERSION
	);

	// Badge / service cards / CTA / split-hero live in home + pages sheets.
	if ( ! wp_style_is( 'ogc-home', 'enqueued' ) ) {
		wp_enqueue_style(
			'ogc-home',
			OGC_THEME_URI . '/assets/css/home.css',
			array( 'ogc-main', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
	}
	if ( ! wp_style_is( 'ogc-pages', 'enqueued' ) ) {
		wp_enqueue_style(
			'ogc-pages',
			OGC_THEME_URI . '/assets/css/pages.css',
			array( 'ogc-home', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
	}
	if ( ! wp_style_is( 'ogc-footer', 'enqueued' ) ) {
		wp_enqueue_style(
			'ogc-footer',
			OGC_THEME_URI . '/assets/css/footer.css',
			array( 'ogc-main' ),
			OGC_THEME_VERSION
		);
	}
	if ( ! wp_style_is( 'ogc-header', 'enqueued' ) ) {
		wp_enqueue_style(
			'ogc-header',
			OGC_THEME_URI . '/assets/css/header.css',
			array( 'ogc-main' ),
			OGC_THEME_VERSION
		);
	}

	wp_enqueue_script(
		'ogc-elementor-widgets',
		OGC_THEME_URI . '/assets/js/elementor-widgets.js',
		array(),
		OGC_THEME_VERSION,
		true
	);

	// Orbit emblem needs Three.js + orbit-emblem.js when that widget appears.
	wp_enqueue_script(
		'ogc-three',
		'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
		array(),
		'r128',
		true
	);
	wp_enqueue_script(
		'ogc-orbit-emblem',
		OGC_THEME_URI . '/assets/js/orbit-emblem.js',
		array( 'ogc-three' ),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'elementor/frontend/after_enqueue_styles', 'ogc_elementor_enqueue_widget_assets' );
add_action( 'elementor/frontend/after_enqueue_scripts', 'ogc_elementor_enqueue_widget_assets' );
add_action( 'elementor/preview/enqueue_styles', 'ogc_elementor_enqueue_widget_assets' );
add_action( 'elementor/preview/enqueue_scripts', 'ogc_elementor_enqueue_widget_assets' );
