<?php
/**
 * Front-end ad rendering — AdSense + advertiser slots, placement gates, device classes.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether article-body ads may render on this request.
 *
 * Restricted to single posts — never Home, About, Services, Mission, Contact, etc.
 *
 * @return bool
 */
function ogc_ads_can_show_article() {
	return is_single() && 'post' === get_post_type();
}

/**
 * Whether between-posts listing ads may render on this request.
 *
 * @return bool
 */
function ogc_ads_can_show_listing() {
	if ( is_home() || is_category() || is_archive() ) {
		return true;
	}

	// Blog filter AJAX refreshes the same home.php grid.
	if ( wp_doing_ajax() ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only action gate.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
		return 'ogc_filter_blog_posts' === $action;
	}

	return false;
}

/**
 * Capture ad-slot template markup for a normalized slot array.
 *
 * @param array<string, mixed> $slot Slot data.
 * @return string
 */
function ogc_get_ad_slot_html( $slot ) {
	if ( ! is_array( $slot ) || empty( $slot['source'] ) ) {
		return '';
	}

	ob_start();
	get_template_part( 'template-parts/ad', 'slot', array( 'slot' => $slot ) );
	return (string) ob_get_clean();
}

/**
 * Evenly spaced paragraph indices at which to inject N ads.
 *
 * @param int $paragraph_count Number of </p> closes.
 * @param int $ad_count        Ads to place.
 * @return array<int, int> Zero-based paragraph indices (sorted unique).
 */
function ogc_ads_even_paragraph_targets( $paragraph_count, $ad_count ) {
	$paragraph_count = (int) $paragraph_count;
	$ad_count        = (int) $ad_count;

	if ( $paragraph_count < 1 || $ad_count < 1 ) {
		return array();
	}

	$ad_count = min( $ad_count, $paragraph_count );
	$targets  = array();

	for ( $i = 1; $i <= $ad_count; $i++ ) {
		$ratio     = $i / ( $ad_count + 1 );
		$targets[] = max( 0, min( $paragraph_count - 1, (int) floor( $paragraph_count * $ratio ) ) );
	}

	$targets = array_values( array_unique( $targets ) );
	sort( $targets, SORT_NUMERIC );

	return $targets;
}

/**
 * Inject article-body ads evenly through single-post content.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function ogc_insert_article_ads( $content ) {
	if ( is_admin() || ! ogc_ads_can_show_article() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( ! function_exists( 'ogc_get_ads_for_placement' ) ) {
		return $content;
	}

	$slots = ogc_get_ads_for_placement( 'article' );
	if ( empty( $slots ) ) {
		return $content;
	}

	if ( false === stripos( $content, '</p>' ) ) {
		$html = '';
		foreach ( $slots as $slot ) {
			$html .= ogc_get_ad_slot_html( $slot );
		}
		return $content . $html;
	}

	$parts = preg_split( '/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) || count( $parts ) < 2 ) {
		return $content;
	}

	$p_indices = array();
	foreach ( $parts as $i => $chunk ) {
		if ( preg_match( '/^<\/p>$/i', $chunk ) ) {
			$p_indices[] = $i;
		}
	}

	$p_count = count( $p_indices );
	if ( $p_count < 1 ) {
		return $content;
	}

	$targets = ogc_ads_even_paragraph_targets( $p_count, count( $slots ) );
	if ( empty( $targets ) ) {
		return $content;
	}

	$insert_after = array();
	foreach ( $targets as $ti => $p_index ) {
		if ( ! isset( $slots[ $ti ] ) || ! isset( $p_indices[ $p_index ] ) ) {
			continue;
		}
		$part_i = $p_indices[ $p_index ];
		if ( ! isset( $insert_after[ $part_i ] ) ) {
			$insert_after[ $part_i ] = '';
		}
		$insert_after[ $part_i ] .= ogc_get_ad_slot_html( $slots[ $ti ] );
	}

	$out = '';
	foreach ( $parts as $i => $chunk ) {
		$out .= $chunk;
		if ( isset( $insert_after[ $i ] ) ) {
			$out .= $insert_after[ $i ];
		}
	}

	return $out;
}
add_filter( 'the_content', 'ogc_insert_article_ads', 12 );

/**
 * Enqueue ads.css when ads may appear on this surface.
 */
function ogc_enqueue_ads_assets() {
	if ( ! function_exists( 'ogc_mod' ) || ! ogc_mod( 'ogc_ads_enabled' ) ) {
		return;
	}

	if ( ! ogc_ads_can_show_article() && ! ogc_ads_can_show_listing() ) {
		return;
	}

	wp_enqueue_style(
		'ogc-ads',
		OGC_THEME_URI . '/assets/css/ads.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'ogc_enqueue_ads_assets', 20 );

/**
 * Load AdSense once when a publisher ID is configured.
 */
function ogc_output_adsense_loader() {
	if ( is_admin() || ! function_exists( 'ogc_mod' ) || ! ogc_mod( 'ogc_ads_enabled' ) ) {
		return;
	}

	$client = function_exists( 'ogc_get_adsense_client_id' ) ? ogc_get_adsense_client_id() : '';
	if ( '' === $client ) {
		return;
	}

	printf(
		'<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>' . "\n",
		esc_attr( $client )
	);

	if ( ogc_mod( 'ogc_adsense_auto_ads' ) ) {
		printf(
			'<script>(adsbygoogle=window.adsbygoogle||[]).push({google_ad_client:%s,enable_page_level_ads:true});</script>' . "\n",
			wp_json_encode( $client )
		);
	}
}
add_action( 'wp_head', 'ogc_output_adsense_loader', 6 );

/**
 * One-time migration from legacy ogc_ads_config_json → ogc_custom_ads_json.
 */
function ogc_migrate_legacy_ads_config() {
	if ( get_option( 'ogc_ads_schema_version' ) >= 2 ) {
		return;
	}

	$legacy = get_theme_mod( 'ogc_ads_config_json', null );
	if ( null !== $legacy && '' !== $legacy && function_exists( 'ogc_decode_json_list' ) ) {
		$rows   = ogc_decode_json_list( $legacy );
		$custom = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$type  = isset( $row['type'] ) && 'code' === $row['type'] ? 'code' : 'image';
			$image = isset( $row['image'] ) ? (string) $row['image'] : '';
			$code  = isset( $row['code'] ) ? (string) $row['code'] : '';
			$url   = isset( $row['url'] ) ? (string) $row['url'] : '';
			$key   = isset( $row['key'] ) ? sanitize_key( $row['key'] ) : '';

			$has_content = ( 'code' === $type && '' !== trim( $code ) ) || ( 'image' === $type && '' !== $image );
			if ( ! $has_content && empty( $row['enabled'] ) ) {
				continue;
			}

			$placement = ( 'inline' === $key ) ? 'article' : 'listing';

			$custom[] = array(
				'label'         => $key ? ucfirst( $key ) : __( 'Advertisement', 'ogc-newfinity' ),
				'type'          => $type,
				'image'         => $image,
				'url'           => $url,
				'code'          => $code,
				'size'          => 'medium_rectangle',
				'width'         => 300,
				'height'        => 250,
				'lazy'          => true,
				'placement'     => $placement,
				'hide_mobile'   => false,
				'hide_tablet'   => false,
			);
		}

		if ( ! empty( $custom ) && '' === (string) get_theme_mod( 'ogc_custom_ads_json', '' ) ) {
			set_theme_mod( 'ogc_custom_ads_json', wp_json_encode( $custom ) );
		}

		remove_theme_mod( 'ogc_ads_config_json' );
	} elseif ( null !== $legacy ) {
		remove_theme_mod( 'ogc_ads_config_json' );
	}

	update_option( 'ogc_ads_schema_version', 2, false );
}
add_action( 'after_setup_theme', 'ogc_migrate_legacy_ads_config', 5 );
