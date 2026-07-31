<?php
/**
 * Seed / resync legal page content from markdown sources.
 *
 * Version-gated like inc/seed-articles.php: bump OGC_LEGAL_SEED_VERSION to push
 * markdown edits into the four existing WP pages. Page existence (slug + title)
 * remains the job of inc/seed-content.php; this file owns body HTML + template.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump this version string EVERY time any legal-*.md file changes, or edits will not sync.
define( 'OGC_LEGAL_SEED_VERSION', '1.0.0' );

/**
 * Definitions for legal pages to seed.
 *
 * @return array<int, array<string, string>>
 */
function ogc_seed_legal_definitions() {
	return array(
		array(
			'title'  => 'Privacy Policy',
			'slug'   => 'privacy-policy',
			'source' => 'inc/seed-content/legal-privacy-policy.md',
		),
		array(
			'title'  => 'Terms and Conditions',
			'slug'   => 'terms-and-conditions',
			'source' => 'inc/seed-content/legal-terms-and-conditions.md',
		),
		array(
			'title'  => 'Cookie Policy',
			'slug'   => 'cookie-policy',
			'source' => 'inc/seed-content/legal-cookie-policy.md',
		),
		array(
			'title'  => 'Refund and Cancellation Policy',
			'slug'   => 'refund-policy',
			'source' => 'inc/seed-content/legal-refund-policy.md',
		),
	);
}

/**
 * Inject id attributes onto H2 headings and build a TOC list (used by page-legal.php).
 *
 * @param string $html Post content HTML.
 * @return array{html: string, toc: array<int, array{id: string, label: string}>}
 */
function ogc_legal_prepare_content( $html ) {
	$toc      = array();
	$used_ids = array();

	$prepared = preg_replace_callback(
		'/<h2(\s[^>]*)?>(.*?)<\/h2>/is',
		function ( $m ) use ( &$toc, &$used_ids ) {
			$attrs = isset( $m[1] ) ? $m[1] : '';
			$inner = $m[2];
			$label = wp_strip_all_tags( $inner );
			$id    = '';

			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $attrs, $id_m ) ) {
				$id = $id_m[1];
			} else {
				$base = sanitize_title( $label );
				if ( '' === $base ) {
					$base = 'section';
				}
				$id = $base;
				$n  = 2;
				while ( isset( $used_ids[ $id ] ) ) {
					$id = $base . '-' . $n;
					++$n;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}

			$used_ids[ $id ] = true;
			$toc[]           = array(
				'id'    => $id,
				'label' => $label,
			);

			return '<h2' . $attrs . '>' . $inner . '</h2>';
		},
		(string) $html
	);

	return array(
		'html' => is_string( $prepared ) ? $prepared : (string) $html,
		'toc'  => $toc,
	);
}

/**
 * Find an existing legal page by slug (publish or draft).
 *
 * @param string $slug Page slug.
 * @return int Page ID or 0.
 */
function ogc_find_legal_page_id( $slug ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing instanceof WP_Post ) {
		return (int) $existing->ID;
	}

	$matches = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		)
	);

	return ! empty( $matches[0] ) ? (int) $matches[0]->ID : 0;
}

/**
 * Insert or update seeded legal pages (idempotent per version).
 */
function ogc_seed_legal_pages() {
	$stored = get_option( 'ogc_legal_seed_version', '' );
	if ( OGC_LEGAL_SEED_VERSION === $stored ) {
		return;
	}

	if ( ! function_exists( 'ogc_article_markdown_to_html' ) ) {
		return;
	}

	foreach ( ogc_seed_legal_definitions() as $page ) {
		$path = OGC_THEME_DIR . '/' . ltrim( $page['source'], '/' );
		if ( ! is_readable( $path ) ) {
			continue;
		}

		$markdown = file_get_contents( $path );
		if ( false === $markdown || '' === trim( $markdown ) ) {
			continue;
		}

		$content = ogc_article_markdown_to_html( $markdown );
		$page_id = ogc_find_legal_page_id( $page['slug'] );

		$postarr = array(
			'post_title'   => $page['title'],
			'post_name'    => $page['slug'],
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
		);

		if ( $page_id > 0 ) {
			$postarr['ID'] = $page_id;
			$result_id     = wp_update_post( $postarr, true );
		} else {
			$result_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $result_id ) || ! $result_id ) {
			continue;
		}

		update_post_meta( (int) $result_id, '_wp_page_template', 'page-legal.php' );
	}

	update_option( 'ogc_legal_seed_version', OGC_LEGAL_SEED_VERSION );
}
add_action( 'init', 'ogc_seed_legal_pages', 25 );
