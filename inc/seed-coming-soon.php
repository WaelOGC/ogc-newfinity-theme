<?php
/**
 * Seed the draft "Coming Soon" page used by maintenance mode.
 *
 * Version-gated like inc/seed-articles.php: runs once per version and never
 * overwrites a page the admin has already created or edited by hand.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump only when the initial seed payload itself must change for fresh installs. */
define( 'OGC_COMING_SOON_SEED_VERSION', '1.0.0' );

/**
 * Find the Coming Soon page by slug (including drafts).
 *
 * @return WP_Post|null
 */
function ogc_find_coming_soon_page() {
	$existing = get_page_by_path( 'coming-soon', OBJECT, 'page' );
	if ( $existing instanceof WP_Post ) {
		return $existing;
	}

	$matches = get_posts(
		array(
			'name'             => 'coming-soon',
			'post_type'        => 'page',
			'post_status'      => array( 'publish', 'draft', 'private', 'pending' ),
			'posts_per_page'   => 1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);

	return ! empty( $matches[0] ) && $matches[0] instanceof WP_Post ? $matches[0] : null;
}

/**
 * Build Gutenberg block markup for the Coming Soon page body.
 *
 * @return string
 */
function ogc_coming_soon_seed_content() {
	$location = function_exists( 'ogc_get_location_text' )
		? ogc_get_location_text()
		: 'The Hague, Netherlands';

	$default_message = "We'll be back shortly.";
	$saved_message   = (string) get_theme_mod( 'ogc_maintenance_mode_message', '' );
	$message         = ( '' !== trim( $saved_message ) && $saved_message !== $default_message )
		? $saved_message
		: $default_message;

	$contact = function_exists( 'ogc_get_contact_email' )
		? ogc_get_contact_email()
		: 'contact@ogcnewfinity.com';
	$mailto  = 'mailto:' . $contact;

	$service_map = array(
		'x'        => 'x',
		'linkedin' => 'linkedin',
		'github'   => 'github',
		'discord'  => 'discord',
	);

	$social_blocks = '';
	foreach ( $service_map as $network => $service ) {
		$url = function_exists( 'ogc_get_social_url' ) ? ogc_get_social_url( $network ) : '';
		if ( ! $url ) {
			continue;
		}
		$social_blocks .= sprintf(
			'<!-- wp:social-link {"url":%s,"service":%s} /-->',
			wp_json_encode( $url ),
			wp_json_encode( $service )
		);
	}

	$blocks  = '';
	$blocks .= "<!-- wp:image {\"align\":\"center\",\"className\":\"ogc-coming-soon__logo\"} -->\n";
	$blocks .= "<figure class=\"wp-block-image aligncenter ogc-coming-soon__logo\"></figure>\n";
	$blocks .= "<!-- /wp:image -->\n\n";

	$blocks .= "<!-- wp:paragraph {\"align\":\"center\",\"className\":\"ogc-coming-soon__location\",\"style\":{\"typography\":{\"textTransform\":\"uppercase\",\"letterSpacing\":\"0.14em\",\"fontSize\":\"0.75rem\"}}} -->\n";
	$blocks .= '<p class="has-text-align-center ogc-coming-soon__location" style="font-size:0.75rem;letter-spacing:0.14em;text-transform:uppercase">' . esc_html( $location ) . "</p>\n";
	$blocks .= "<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"textAlign\":\"center\",\"level\":1,\"className\":\"ogc-coming-soon__heading\",\"style\":{\"typography\":{\"fontStyle\":\"italic\",\"fontWeight\":\"500\"}}} -->\n";
	$blocks .= '<h1 class="wp-block-heading has-text-align-center ogc-coming-soon__heading" style="font-style:italic;font-weight:500">' . esc_html__( 'Something new is coming together', 'ogc-newfinity' ) . "</h1>\n";
	$blocks .= "<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:paragraph {\"align\":\"center\",\"className\":\"ogc-coming-soon__message\"} -->\n";
	$blocks .= '<p class="has-text-align-center ogc-coming-soon__message">' . esc_html( $message ) . "</p>\n";
	$blocks .= "<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n";
	$blocks .= "<div class=\"wp-block-buttons\">\n";
	$blocks .= "<!-- wp:button {\"className\":\"ogc-coming-soon__cta\"} -->\n";
	$blocks .= '<div class="wp-block-button ogc-coming-soon__cta"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $mailto ) . '">' . esc_html__( 'Get in touch', 'ogc-newfinity' ) . "</a></div>\n";
	$blocks .= "<!-- /wp:button -->\n";
	$blocks .= "</div>\n";
	$blocks .= "<!-- /wp:buttons -->\n";

	if ( '' !== $social_blocks ) {
		$blocks .= "\n<!-- wp:social-links {\"className\":\"ogc-coming-soon__social\",\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n";
		$blocks .= '<ul class="wp-block-social-links ogc-coming-soon__social">' . $social_blocks . "</ul>\n";
		$blocks .= "<!-- /wp:social-links -->\n";
	}

	return $blocks;
}

/**
 * Insert the draft Coming Soon page once (never overwrites existing pages).
 */
function ogc_seed_coming_soon_page() {
	$stored = get_option( 'ogc_coming_soon_seed_version', '' );
	if ( OGC_COMING_SOON_SEED_VERSION === $stored ) {
		return;
	}

	$existing = ogc_find_coming_soon_page();
	if ( $existing instanceof WP_Post ) {
		// Page already exists (admin may have edited it) — do not touch content.
		update_option( 'ogc_coming_soon_seed_version', OGC_COMING_SOON_SEED_VERSION );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Coming Soon',
			'post_name'    => 'coming-soon',
			'post_content' => ogc_coming_soon_seed_content(),
			'post_status'  => 'draft',
			'post_type'    => 'page',
			'post_author'  => 1,
		),
		true
	);

	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_option( 'ogc_coming_soon_seed_version', OGC_COMING_SOON_SEED_VERSION );
	}
}
add_action( 'init', 'ogc_seed_coming_soon_page', 28 );
add_action( 'after_switch_theme', 'ogc_seed_coming_soon_page' );
