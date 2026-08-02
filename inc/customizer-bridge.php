<?php
/**
 * Customizer bridge for OGC Hero Backgrounds — live-preview essentials only.
 *
 * Reads/writes the SAME theme_mod keys as the Theme Options dashboard
 * (inc/theme-options.php). This is a second UI on one source of truth, not a
 * parallel schema. Full border/blur/filter/animation controls stay dashboard-only.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the OGC Hero Backgrounds Customizer panel + per-page sections.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ogc_customize_bridge_register( $wp_customize ) {
	$pages = array(
		'home'     => __( 'Home', 'ogc-newfinity' ),
		'about'    => __( 'About', 'ogc-newfinity' ),
		'services' => __( 'Services', 'ogc-newfinity' ),
		'mission'  => __( 'Mission', 'ogc-newfinity' ),
	);

	$positions = array(
		'center'       => __( 'Center', 'ogc-newfinity' ),
		'top'          => __( 'Top', 'ogc-newfinity' ),
		'bottom'       => __( 'Bottom', 'ogc-newfinity' ),
		'left'         => __( 'Left', 'ogc-newfinity' ),
		'right'        => __( 'Right', 'ogc-newfinity' ),
		'top-left'     => __( 'Top left', 'ogc-newfinity' ),
		'top-right'    => __( 'Top right', 'ogc-newfinity' ),
		'bottom-left'  => __( 'Bottom left', 'ogc-newfinity' ),
		'bottom-right' => __( 'Bottom right', 'ogc-newfinity' ),
	);

	$dashboard_url = admin_url( 'admin.php?page=ogc-theme-options&tab=images' );

	$wp_customize->add_panel(
		'ogc_hero_backgrounds',
		array(
			'title'       => __( 'OGC Hero Backgrounds', 'ogc-newfinity' ),
			'description' => __( 'Live-preview essentials for hero background images. Full controls (border, blur, animation, etc.) live under OGC NewFinity → Images.', 'ogc-newfinity' ),
			'priority'    => 45,
		)
	);

	foreach ( $pages as $page_key => $page_label ) {
		$section_id = 'ogc_hero_bg_' . $page_key;

		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => sprintf(
					/* translators: %s: page name. */
					__( '%s hero', 'ogc-newfinity' ),
					$page_label
				),
				'panel'       => 'ogc_hero_backgrounds',
				'description' => sprintf(
					'<p><a href="%1$s">%2$s</a></p>',
					esc_url( $dashboard_url ),
					esc_html__( 'Edit all hero background options →', 'ogc-newfinity' )
				),
			)
		);

		/* Image — refresh (media load in preview needs a full reload). */
		$wp_customize->add_setting(
			'ogc_hero_bg_' . $page_key,
			array(
				'default'           => '',
				'sanitize_callback' => 'ogc_sanitize_image_url',
				'transport'         => 'refresh',
				'type'              => 'theme_mod',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'ogc_hero_bg_' . $page_key,
				array(
					'label'   => __( 'Background image', 'ogc-newfinity' ),
					'section' => $section_id,
				)
			)
		);

		/* Opacity — postMessage. */
		$wp_customize->add_setting(
			'ogc_hero_bg_opacity_' . $page_key,
			array(
				'default'           => 35,
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
				'type'              => 'theme_mod',
			)
		);
		$wp_customize->add_control(
			'ogc_hero_bg_opacity_' . $page_key,
			array(
				'label'       => __( 'Opacity', 'ogc-newfinity' ),
				'section'     => $section_id,
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			)
		);

		/* Position — postMessage. */
		$wp_customize->add_setting(
			'ogc_hero_bg_position_' . $page_key,
			array(
				'default'           => 'center',
				'sanitize_callback' => function ( $value ) use ( $positions ) {
					$value = sanitize_key( $value );
					return array_key_exists( $value, $positions ) ? $value : 'center';
				},
				'transport'         => 'postMessage',
				'type'              => 'theme_mod',
			)
		);
		$wp_customize->add_control(
			'ogc_hero_bg_position_' . $page_key,
			array(
				'label'   => __( 'Position', 'ogc-newfinity' ),
				'section' => $section_id,
				'type'    => 'select',
				'choices' => $positions,
			)
		);

		/* Overlay color — postMessage. */
		$wp_customize->add_setting(
			'ogc_hero_bg_overlay_color_' . $page_key,
			array(
				'default'           => '',
				'sanitize_callback' => 'ogc_sanitize_hex_color',
				'transport'         => 'postMessage',
				'type'              => 'theme_mod',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'ogc_hero_bg_overlay_color_' . $page_key,
				array(
					'label'       => __( 'Overlay color', 'ogc-newfinity' ),
					'description' => __( 'Clear to remove the overlay. Overlay opacity is set in OGC NewFinity → Images.', 'ogc-newfinity' ),
					'section'     => $section_id,
				)
			)
		);
	}
}
add_action( 'customize_register', 'ogc_customize_bridge_register' );

/**
 * Enqueue live-preview script for hero background postMessage controls.
 */
function ogc_customize_bridge_preview_init() {
	wp_enqueue_script(
		'ogc-customize-preview',
		OGC_THEME_URI . '/assets/js/customize-preview.js',
		array( 'customize-preview', 'jquery' ),
		OGC_THEME_VERSION,
		true
	);

	wp_localize_script(
		'ogc-customize-preview',
		'ogcHeroPreview',
		array(
			'pages'     => array( 'home', 'about', 'services', 'mission' ),
			'positions' => array(
				'center'       => 'center center',
				'top'          => 'center top',
				'bottom'       => 'center bottom',
				'left'         => 'left center',
				'right'        => 'right center',
				'top-left'     => 'left top',
				'top-right'    => 'right top',
				'bottom-left'  => 'left bottom',
				'bottom-right' => 'right bottom',
			),
		)
	);
}
add_action( 'customize_preview_init', 'ogc_customize_bridge_preview_init' );
