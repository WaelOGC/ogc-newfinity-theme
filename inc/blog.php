<?php
/**
 * Blog & Insights — categories, helpers, and AJAX filter.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option key for one-time category seed. */
define( 'OGC_BLOG_CATEGORIES_VERSION', '1.0.0' );

/**
 * Canonical blog category definitions (native WP category taxonomy).
 *
 * @return array<int, array{name: string, slug: string, description: string}>
 */
function ogc_blog_categories() {
	return array(
		array(
			'name'        => 'OGC News',
			'slug'        => 'ogc-news',
			'description' => 'Company news and announcements from OGC NewFinity.',
		),
		array(
			'name'        => 'Build & Development',
			'slug'        => 'build-development',
			'description' => 'Software & App Development, Custom WordPress Development, and Cybersecurity.',
		),
		array(
			'name'        => 'Design & Experience',
			'slug'        => 'design-experience',
			'description' => 'UI/UX Design insights and experience design.',
		),
		array(
			'name'        => 'Strategy & Growth',
			'slug'        => 'strategy-growth',
			'description' => 'Marketing & Strategy Consulting.',
		),
		array(
			'name'        => 'AI & Innovation',
			'slug'        => 'ai-innovation',
			'description' => 'AI Solutions and innovation topics.',
		),
	);
}

/**
 * Ensure the five blog categories exist (idempotent).
 */
function ogc_ensure_blog_categories() {
	$stored = get_option( 'ogc_blog_categories_version', '' );
	if ( OGC_BLOG_CATEGORIES_VERSION === $stored ) {
		// Still ensure each term exists in case of manual deletion.
		foreach ( ogc_blog_categories() as $cat ) {
			if ( ! term_exists( $cat['slug'], 'category' ) ) {
				wp_insert_term(
					$cat['name'],
					'category',
					array(
						'slug'        => $cat['slug'],
						'description' => $cat['description'],
					)
				);
			}
		}
		return;
	}

	foreach ( ogc_blog_categories() as $cat ) {
		$existing = term_exists( $cat['slug'], 'category' );
		if ( ! $existing ) {
			wp_insert_term(
				$cat['name'],
				'category',
				array(
					'slug'        => $cat['slug'],
					'description' => $cat['description'],
				)
			);
		}
	}

	update_option( 'ogc_blog_categories_version', OGC_BLOG_CATEGORIES_VERSION );
}
add_action( 'init', 'ogc_ensure_blog_categories', 15 );
add_action( 'after_switch_theme', 'ogc_ensure_blog_categories' );

/**
 * Default cover image URI when a post has no featured image.
 *
 * @return string
 */
function ogc_blog_placeholder_cover_uri() {
	return OGC_THEME_URI . '/assets/images/articles/cover.png';
}

/**
 * Whether the current request is a Blog & Insights surface.
 *
 * @return bool
 */
function ogc_is_blog_surface() {
	return is_home() || is_singular( 'post' ) || is_category();
}

/**
 * Estimated reading time in minutes (≈200 wpm), minimum 1.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return int
 */
function ogc_get_reading_time_minutes( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( (string) $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Primary category for a post (first assigned, preferring seeded blog cats).
 *
 * @param int|null $post_id Post ID.
 * @return WP_Term|null
 */
function ogc_get_post_primary_category( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = get_the_category( $post_id );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}

	$slugs = wp_list_pluck( ogc_blog_categories(), 'slug' );
	foreach ( $terms as $term ) {
		if ( in_array( $term->slug, $slugs, true ) ) {
			return $term;
		}
	}

	return $terms[0];
}

/**
 * Echo featured image markup with cover.png fallback.
 *
 * @param array $args {
 *     Optional.
 *     @type string $class   Extra class on the <img>.
 *     @type string $size    Image size. Default 'large'.
 *     @type string $loading lazy|eager. Default 'lazy'.
 * }
 */
function ogc_the_post_cover( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'   => '',
			'size'    => 'large',
			'loading' => 'lazy',
		)
	);

	$classes = trim( 'ogc-blog-cover ' . $args['class'] );
	$alt     = the_title_attribute( array( 'echo' => false ) );

	if ( has_post_thumbnail() ) {
		the_post_thumbnail(
			$args['size'],
			array(
				'class'    => $classes,
				'loading'  => $args['loading'],
				'decoding' => 'async',
				'alt'      => $alt,
			)
		);
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async" width="1200" height="675">',
		esc_url( ogc_blog_placeholder_cover_uri() ),
		esc_attr( $alt ? $alt : __( 'Article cover', 'ogc-newfinity' ) ),
		esc_attr( $classes ),
		esc_attr( $args['loading'] )
	);
}

/**
 * Trimmed excerpt for blog cards (~22 words).
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function ogc_get_blog_card_excerpt( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$raw     = get_the_excerpt( $post_id );
	$words   = preg_split( '/\s+/', wp_strip_all_tags( (string) $raw ), -1, PREG_SPLIT_NO_EMPTY );

	if ( empty( $words ) ) {
		return '';
	}

	if ( count( $words ) > 22 ) {
		$words = array_slice( $words, 0, 22 );
		return implode( ' ', $words ) . '…';
	}

	return implode( ' ', $words );
}

/**
 * Render the post-card grid HTML for a category slug (or all posts).
 *
 * @param string $category_slug Category slug, or empty / 'all' for everything.
 * @param int    $per_page      Posts per page. Default 9.
 * @return string HTML markup.
 */
function ogc_render_blog_grid( $category_slug = '', $per_page = 9 ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $per_page,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$slug = sanitize_title( (string) $category_slug );
	if ( $slug && 'all' !== $slug ) {
		$args['category_name'] = $slug;
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		echo '<div class="ogc-glass-grid ogc-blog-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/blog', 'post-card' );
		}
		echo '</div>';
		wp_reset_postdata();
	} else {
		echo '<p class="ogc-blog-empty">';
		echo esc_html__( 'No articles in this category yet — check back soon.', 'ogc-newfinity' );
		echo '</p>';
	}

	return (string) ob_get_clean();
}

/**
 * AJAX: return filtered blog card grid HTML.
 */
function ogc_ajax_filter_blog_posts() {
	check_ajax_referer( 'ogc_blog_filter', 'nonce' );

	$category = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';

	$allowed = wp_list_pluck( ogc_blog_categories(), 'slug' );
	if ( $category && 'all' !== $category && ! in_array( $category, $allowed, true ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid category.', 'ogc-newfinity' ) ),
			400
		);
	}

	$html = ogc_render_blog_grid( 'all' === $category ? '' : $category );

	wp_send_json_success(
		array(
			'html'     => $html,
			'category' => $category ? $category : 'all',
		)
	);
}
add_action( 'wp_ajax_ogc_filter_blog_posts', 'ogc_ajax_filter_blog_posts' );
add_action( 'wp_ajax_nopriv_ogc_filter_blog_posts', 'ogc_ajax_filter_blog_posts' );
