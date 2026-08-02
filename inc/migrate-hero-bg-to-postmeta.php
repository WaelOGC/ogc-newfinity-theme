<?php
/**
 * One-time migration: Theme Options hero_bg_* → per-page post meta.
 *
 * Old ogc_mod() / theme_mod key → new post meta key (per page):
 *
 *   ogc_hero_bg_{page_key}                  → _ogc_hero_bg_image
 *   ogc_hero_bg_fit_{page_key}              → _ogc_hero_bg_fit
 *   ogc_hero_bg_width_{page_key}            → _ogc_hero_bg_width
 *   ogc_hero_bg_height_{page_key}           → _ogc_hero_bg_height
 *   ogc_hero_bg_position_{page_key}         → _ogc_hero_bg_position
 *   ogc_hero_bg_repeat_{page_key}           → _ogc_hero_bg_repeat
 *   ogc_hero_bg_border_width_{page_key}     → _ogc_hero_bg_border_width
 *   ogc_hero_bg_border_color_{page_key}     → _ogc_hero_bg_border_color
 *   ogc_hero_bg_border_style_{page_key}     → _ogc_hero_bg_border_style
 *   ogc_hero_bg_border_radius_{page_key}    → _ogc_hero_bg_border_radius
 *   ogc_hero_bg_blur_{page_key}             → _ogc_hero_bg_blur
 *   ogc_hero_bg_grayscale_{page_key}        → _ogc_hero_bg_grayscale
 *   ogc_hero_bg_overlay_color_{page_key}    → _ogc_hero_bg_overlay_color
 *   ogc_hero_bg_overlay_opacity_{page_key}  → _ogc_hero_bg_overlay_opacity
 *   ogc_hero_bg_brightness_{page_key}       → _ogc_hero_bg_brightness
 *   ogc_hero_bg_contrast_{page_key}         → _ogc_hero_bg_contrast
 *   ogc_hero_bg_animation_{page_key}        → _ogc_hero_bg_animation
 *   ogc_hero_bg_opacity_{page_key}          → _ogc_hero_bg_opacity
 *
 * {page_key} was one of: home | about | services | mission
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OGC_HERO_BG_MIGRATION_VERSION', '1.0.0' );

/**
 * Copy non-default hero background theme_mods into page post meta once.
 */
function ogc_migrate_hero_bg_to_postmeta() {
	if ( get_option( 'ogc_hero_bg_migration_version' ) === OGC_HERO_BG_MIGRATION_VERSION ) {
		return;
	}

	$slug_map = array(
		'home'     => 'home',
		'about'    => 'about-ogc-newfinity',
		'services' => 'services',
		'mission'  => 'our-mission',
	);

	/*
	 * Field map: old theme_mod suffix after ogc_hero_bg_ / before _{page_key},
	 * except the bare image key which has no middle suffix.
	 * Values are [ meta_key, default, sanitize_callback ].
	 */
	$fields = array(
		'image'           => array( '_ogc_hero_bg_image', '', 'ogc_sanitize_image_url' ),
		'fit'             => array( '_ogc_hero_bg_fit', 'cover', 'sanitize_text_field' ),
		'width'           => array( '_ogc_hero_bg_width', '100%', 'ogc_sanitize_css_length' ),
		'height'          => array( '_ogc_hero_bg_height', 'auto', 'ogc_sanitize_css_length' ),
		'position'        => array( '_ogc_hero_bg_position', 'center', 'sanitize_text_field' ),
		'repeat'          => array( '_ogc_hero_bg_repeat', 'no-repeat', 'sanitize_text_field' ),
		'border_width'    => array( '_ogc_hero_bg_border_width', 0, 'ogc_sanitize_int' ),
		'border_color'    => array( '_ogc_hero_bg_border_color', OGC_DEFAULT_COLOR_ACCENT_START, 'ogc_sanitize_hex_color' ),
		'border_style'    => array( '_ogc_hero_bg_border_style', 'solid', 'sanitize_text_field' ),
		'border_radius'   => array( '_ogc_hero_bg_border_radius', 0, 'ogc_sanitize_int' ),
		'blur'            => array( '_ogc_hero_bg_blur', 0, 'ogc_sanitize_int' ),
		'grayscale'       => array( '_ogc_hero_bg_grayscale', 0, 'ogc_sanitize_int' ),
		'overlay_color'   => array( '_ogc_hero_bg_overlay_color', '', 'ogc_sanitize_hex_color' ),
		'overlay_opacity' => array( '_ogc_hero_bg_overlay_opacity', 0, 'ogc_sanitize_int' ),
		'brightness'      => array( '_ogc_hero_bg_brightness', 100, 'ogc_sanitize_int' ),
		'contrast'        => array( '_ogc_hero_bg_contrast', 100, 'ogc_sanitize_int' ),
		'animation'       => array( '_ogc_hero_bg_animation', 'none', 'sanitize_text_field' ),
		'opacity'         => array( '_ogc_hero_bg_opacity', 35, 'ogc_sanitize_int' ),
	);

	foreach ( $slug_map as $old_page_key => $real_slug ) {
		$page = get_page_by_path( $real_slug, OBJECT, 'page' );
		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		foreach ( $fields as $suffix => $spec ) {
			list( $meta_key, $default, $sanitize ) = $spec;

			if ( 'image' === $suffix ) {
				$old_key = 'ogc_hero_bg_' . $old_page_key;
			} else {
				$old_key = 'ogc_hero_bg_' . $suffix . '_' . $old_page_key;
			}

			// Prefer ogc_mod() while schema still exists; fall back to raw theme_mod.
			if ( function_exists( 'ogc_mod' ) && function_exists( 'ogc_theme_options_field' ) && ogc_theme_options_field( $old_key ) ) {
				$value = ogc_mod( $old_key );
			} else {
				$value = get_theme_mod( $old_key, null );
				if ( null === $value ) {
					continue;
				}
			}

			if ( is_callable( $sanitize ) ) {
				$value = call_user_func( $sanitize, $value );
			}

			if ( ogc_hero_bg_migration_is_default( $value, $default ) ) {
				continue;
			}

			update_post_meta( $page->ID, $meta_key, $value );
		}
	}

	update_option( 'ogc_hero_bg_migration_version', OGC_HERO_BG_MIGRATION_VERSION );
}
add_action( 'init', 'ogc_migrate_hero_bg_to_postmeta', 5 );

/**
 * Whether a migrated value matches the old schema default (skip writing).
 *
 * @param mixed $value   Sanitized value.
 * @param mixed $default Schema default.
 * @return bool
 */
function ogc_hero_bg_migration_is_default( $value, $default ) {
	if ( null === $value ) {
		return true;
	}
	if ( is_string( $default ) && '' === $default ) {
		return '' === trim( (string) $value );
	}
	if ( is_int( $default ) || is_float( $default ) ) {
		return (int) $value === (int) $default;
	}
	return (string) $value === (string) $default;
}
