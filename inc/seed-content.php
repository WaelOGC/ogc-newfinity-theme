<?php
/**
 * Seed core pages and primary navigation so header/footer links resolve.
 *
 * Runs once per seed version (option flag). Safe to re-run after bumping version.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump this to re-seed menus/pages after structural sitemap changes. */
define( 'OGC_CONTENT_SEED_VERSION', '1.5.0' );

/**
 * Ensure a published page exists for the given slug.
 *
 * @param string $title   Page title.
 * @param string $slug    Page slug.
 * @param string $content Page content HTML.
 * @return int Page ID.
 */
function ogc_ensure_page( $title, $slug, $content ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	// Also catch drafts/private (e.g. WP sample Privacy Policy).
	if ( ! $existing instanceof WP_Post ) {
		$matches = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		if ( ! empty( $matches[0] ) ) {
			$existing = $matches[0];
		}
	}

	// Pages whose markup lives in PHP templates — clear leftover editor placeholders.
	$template_driven = array( 'home', 'about-ogc-newfinity', 'services', 'our-mission', 'support', 'blog' );

	if ( $existing instanceof WP_Post ) {
		$update = array( 'ID' => (int) $existing->ID );

		if ( 'publish' !== $existing->post_status ) {
			$update['post_status'] = 'publish';
		}

		$stripped = trim( wp_strip_all_tags( $existing->post_content ) );
		$is_wp_privacy_sample = ( false !== stripos( $existing->post_content, 'Suggested text:' ) );
		$is_coming_soon       = ( false !== stripos( $stripped, 'coming soon' ) );

		if ( in_array( $slug, $template_driven, true ) && ( '' === $stripped || $is_coming_soon ) ) {
			$update['post_title']   = $title;
			$update['post_content'] = '';
			$update['post_name']    = $slug;
		} elseif ( '' === $stripped || $is_wp_privacy_sample ) {
			// Only fill empty placeholder pages — never overwrite real content.
			$update['post_title']   = $title;
			$update['post_content'] = $content;
			$update['post_name']    = $slug;
		}

		if ( count( $update ) > 1 ) {
			wp_update_post( $update );
		}

		return (int) $existing->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
		),
		true
	);

	return is_wp_error( $page_id ) ? 0 : (int) $page_id;
}

/**
 * Create sitemap pages + primary menu if missing.
 */
function ogc_seed_content() {
	$stored = get_option( 'ogc_content_seed_version', '' );
	if ( OGC_CONTENT_SEED_VERSION === $stored ) {
		return;
	}

	$placeholder = '<!-- wp:paragraph --><p>Full policy content coming soon.</p><!-- /wp:paragraph -->';
	$coming_soon = '<!-- wp:paragraph --><p>Page content coming soon.</p><!-- /wp:paragraph -->';

	$pages = array(
		// Content is rendered by dedicated templates — keep editor body empty (no placeholder copy).
		'home'                 => array( 'Home', '' ),
		'about-ogc-newfinity'  => array( 'About Us', '' ),
		'services'             => array( 'Our Services', '' ),
		'our-mission'          => array( 'Our Mission', '' ),
		'submit-idea'          => array( 'Submit Your Idea', $coming_soon ),
		'blog'                 => array( 'Blog & Insights', '' ),
		'support'              => array( 'Help & Support', '' ),
		'contact'              => array( 'Contact', $coming_soon ),
		'privacy-policy'       => array( 'Privacy Policy', $placeholder ),
		'terms-and-conditions' => array( 'Terms and Conditions', $placeholder ),
		'cookie-policy'        => array( 'Cookie Policy', $placeholder ),
		'refund-policy'        => array( 'Refund and Cancellation Policy', $placeholder ),
	);

	$page_ids = array();
	foreach ( $pages as $slug => $data ) {
		$page_ids[ $slug ] = ogc_ensure_page( $data[0], $slug, $data[1] );
	}

	// Remove leftover demo Sample Page from a fresh WP install.
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample instanceof WP_Post ) {
		wp_trash_post( (int) $sample->ID );
	}

	// Primary nav: Home, About Us, Our Services, Our Mission, Blog & Insights, Help & Support, Contact.
	$menu_name = 'Primary Navigation';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
	} else {
		$menu_id = (int) $menu->term_id;
		$items   = wp_get_nav_menu_items( $menu_id );
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}
	}

	if ( ! is_wp_error( $menu_id ) && $menu_id ) {
		$nav_map = array(
			array( 'title' => 'Home', 'url' => home_url( '/' ), 'object' => 'custom' ),
			array( 'title' => 'About Us', 'slug' => 'about-ogc-newfinity' ),
			array( 'title' => 'Our Services', 'slug' => 'services' ),
			array( 'title' => 'Our Mission', 'slug' => 'our-mission' ),
			array( 'title' => 'Blog & Insights', 'slug' => 'blog' ),
			array( 'title' => 'Help & Support', 'slug' => 'support' ),
			array( 'title' => 'Contact', 'slug' => 'contact' ),
		);

		$position = 1;
		foreach ( $nav_map as $item ) {
			if ( isset( $item['url'] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $item['title'],
						'menu-item-url'    => $item['url'],
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
						'menu-item-position' => $position,
					)
				);
			} elseif ( ! empty( $page_ids[ $item['slug'] ] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $item['title'],
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page_ids[ $item['slug'] ],
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $position,
					)
				);
			}
			++$position;
		}

		$locations            = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = (int) $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Point Reading settings at the Home page (front-page.php) + Blog page.
	if ( ! empty( $page_ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $page_ids['home'] );
	}
	if ( ! empty( $page_ids['blog'] ) ) {
		update_option( 'page_for_posts', (int) $page_ids['blog'] );
	}

	update_option( 'ogc_content_seed_version', OGC_CONTENT_SEED_VERSION );
}
add_action( 'init', 'ogc_seed_content', 20 );
add_action( 'after_switch_theme', 'ogc_seed_content' );
