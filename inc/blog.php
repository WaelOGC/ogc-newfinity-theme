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
 * Trimmed excerpt for blog cards; word count from Theme Options → Posts.
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

	$limit = max( 5, (int) get_theme_mod( 'ogc_blog_excerpt_length', 22 ) );

	if ( count( $words ) > $limit ) {
		$words = array_slice( $words, 0, $limit );
		return implode( ' ', $words ) . '…';
	}

	return implode( ' ', $words );
}

/**
 * Resolve the requested blog listing page number (pretty permalinks + ?paged=).
 *
 * @return int
 */
function ogc_get_blog_paged() {
	$paged = 1;

	if ( get_query_var( 'paged' ) ) {
		$paged = (int) get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$paged = (int) get_query_var( 'page' );
	} elseif ( isset( $_GET['paged'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged = absint( wp_unslash( $_GET['paged'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return max( 1, $paged );
}

/**
 * Query published posts for the blog archive grid.
 *
 * @param string $category_slug Category slug, or empty / 'all' for everything.
 * @param int    $page          Page number (1-indexed).
 * @param int    $per_page      Posts per page. Default 9.
 * @return WP_Query
 */
function ogc_blog_posts_query( $category_slug = '', $page = 1, $per_page = 9 ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, (int) $per_page ),
		'paged'               => max( 1, (int) $page ),
		'ignore_sticky_posts' => true,
	);

	$slug = sanitize_title( (string) $category_slug );
	if ( $slug && 'all' !== $slug ) {
		$args['category_name'] = $slug;
	}

	return new WP_Query( $args );
}

/**
 * Render the post-card grid HTML from a query (or run a fresh query).
 *
 * @param string        $category_slug Category slug, or empty / 'all'.
 * @param int           $page          Page number. Default 1.
 * @param int           $per_page      Posts per page. Default 9.
 * @param WP_Query|null $query         Optional existing query.
 * @return string HTML markup.
 */
function ogc_render_blog_grid( $category_slug = '', $page = 1, $per_page = 9, $query = null ) {
	$owns_query = ! ( $query instanceof WP_Query );
	if ( $owns_query ) {
		$query = ogc_blog_posts_query( $category_slug, $page, $per_page );
	}

	ob_start();

	if ( $query->have_posts() ) {
		echo '<div class="ogc-glass-grid ogc-blog-grid">';

		$listing_ads   = array();
		$ads_interval  = 0;
		$ad_cursor     = 0;
		$post_index    = 0;

		if ( function_exists( 'ogc_ads_can_show_listing' ) && ogc_ads_can_show_listing()
			&& function_exists( 'ogc_get_ads_for_placement' ) ) {
			$listing_ads = ogc_get_ads_for_placement( 'listing' );
			$post_count  = (int) $query->post_count;
			if ( ! empty( $listing_ads ) && $post_count > 1 ) {
				$ads_interval = max( 1, (int) floor( $post_count / ( count( $listing_ads ) + 1 ) ) );
			}
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_index++;
			get_template_part( 'template-parts/blog', 'post-card' );

			if ( $ads_interval > 0 && $ad_cursor < count( $listing_ads )
				&& 0 === ( $post_index % $ads_interval )
				&& $post_index < (int) $query->post_count ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from escaped template part.
				echo ogc_get_ad_slot_html( $listing_ads[ $ad_cursor ] );
				$ad_cursor++;
			}
		}
		echo '</div>';
		if ( $owns_query ) {
			wp_reset_postdata();
		}
	} else {
		echo '<p class="ogc-blog-empty">';
		echo esc_html__( 'No articles in this category yet — check back soon.', 'ogc-newfinity' );
		echo '</p>';
	}

	return (string) ob_get_clean();
}

/**
 * Build the truncated page list used by blog pagination (matches comment mid/end size).
 *
 * @param int $current  Current page.
 * @param int $total    Total pages.
 * @param int $end_size Pages at each end. Default 1.
 * @param int $mid_size Pages either side of current. Default 1.
 * @return array<int, array{type: string, page?: int}>
 */
function ogc_blog_pagination_items( $current, $total, $end_size = 1, $mid_size = 1 ) {
	$current = max( 1, min( (int) $current, (int) $total ) );
	$total   = max( 1, (int) $total );
	$end     = max( 1, (int) $end_size );
	$mid     = max( 0, (int) $mid_size );

	$pages = array();
	for ( $i = 1; $i <= $total; $i++ ) {
		$in_end = ( $i <= $end ) || ( $i > $total - $end );
		$in_mid = ( $i >= $current - $mid ) && ( $i <= $current + $mid );
		if ( $in_end || $in_mid ) {
			$pages[] = $i;
		}
	}
	$pages = array_values( array_unique( $pages ) );
	sort( $pages, SORT_NUMERIC );

	$items = array();
	$prev  = 0;
	foreach ( $pages as $p ) {
		if ( $prev && $p > $prev + 1 ) {
			$items[] = array( 'type' => 'dots' );
		}
		$items[] = array(
			'type' => 'page',
			'page' => (int) $p,
		);
		$prev = (int) $p;
	}

	return $items;
}

/**
 * Render numbered blog archive pagination (comment-style truncation).
 *
 * Returns empty string when there is only one page (or none).
 *
 * @param int $current_page Current page (1-indexed).
 * @param int $max_pages    Total pages.
 * @return string HTML markup.
 */
function ogc_render_blog_pagination( $current_page, $max_pages ) {
	$current_page = max( 1, (int) $current_page );
	$max_pages    = max( 0, (int) $max_pages );

	if ( $max_pages <= 1 ) {
		return '';
	}

	$items = ogc_blog_pagination_items( $current_page, $max_pages, 1, 1 );

	ob_start();
	?>
	<nav class="ogc-blog-pagination" aria-label="<?php esc_attr_e( 'Blog pages', 'ogc-newfinity' ); ?>">
		<ul class="page-numbers">
			<?php if ( $current_page > 1 ) : ?>
				<li>
					<button
						type="button"
						class="page-numbers prev"
						data-page="<?php echo esc_attr( (string) ( $current_page - 1 ) ); ?>"
					>
						<?php esc_html_e( 'Prev', 'ogc-newfinity' ); ?>
					</button>
				</li>
			<?php endif; ?>

			<?php foreach ( $items as $item ) : ?>
				<?php if ( 'dots' === $item['type'] ) : ?>
					<li><span class="page-numbers dots">&hellip;</span></li>
				<?php else : ?>
					<?php
					$page      = (int) $item['page'];
					$is_current = ( $page === $current_page );
					?>
					<li>
						<?php if ( $is_current ) : ?>
							<span class="page-numbers current" aria-current="page"><?php echo esc_html( (string) $page ); ?></span>
						<?php else : ?>
							<button
								type="button"
								class="page-numbers"
								data-page="<?php echo esc_attr( (string) $page ); ?>"
							>
								<?php echo esc_html( (string) $page ); ?>
							</button>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ( $current_page < $max_pages ) : ?>
				<li>
					<button
						type="button"
						class="page-numbers next"
						data-page="<?php echo esc_attr( (string) ( $current_page + 1 ) ); ?>"
					>
						<?php esc_html_e( 'Next', 'ogc-newfinity' ); ?>
					</button>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
	<?php

	return (string) ob_get_clean();
}

/**
 * Full blog listing payload: card grid + pagination + meta.
 *
 * @param string $category_slug Category slug, or empty / 'all'.
 * @param int    $page          Page number. Default 1.
 * @param int    $per_page      Posts per page. Default 9.
 * @return array{html: string, pagination_html: string, current_page: int, max_pages: int, found_posts: int, category: string}
 */
function ogc_get_blog_listing( $category_slug = '', $page = 1, $per_page = 9 ) {
	$page  = max( 1, (int) $page );
	$slug  = sanitize_title( (string) $category_slug );
	$query = ogc_blog_posts_query( $slug, $page, $per_page );

	$max_pages = (int) $query->max_num_pages;
	if ( $max_pages > 0 && $page > $max_pages ) {
		$page  = $max_pages;
		$query = ogc_blog_posts_query( $slug, $page, $per_page );
		$max_pages = (int) $query->max_num_pages;
	}

	$html = ogc_render_blog_grid( $slug, $page, $per_page, $query );
	wp_reset_postdata();

	return array(
		'html'            => $html,
		'pagination_html' => ogc_render_blog_pagination( $page, $max_pages ),
		'current_page'    => $page,
		'max_pages'       => $max_pages,
		'found_posts'     => (int) $query->found_posts,
		'category'        => ( $slug && 'all' !== $slug ) ? $slug : 'all',
	);
}

/**
 * AJAX: return filtered blog card grid + pagination HTML.
 */
function ogc_ajax_filter_blog_posts() {
	check_ajax_referer( 'ogc_blog_filter', 'nonce' );

	$category = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';
	$page     = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;

	$allowed = wp_list_pluck( ogc_blog_categories(), 'slug' );
	if ( $category && 'all' !== $category && ! in_array( $category, $allowed, true ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid category.', 'ogc-newfinity' ) ),
			400
		);
	}

	$listing = ogc_get_blog_listing( 'all' === $category ? '' : $category, $page );

	wp_send_json_success(
		array(
			'html'            => $listing['html'],
			'pagination_html' => $listing['pagination_html'],
			'current_page'    => $listing['current_page'],
			'max_pages'       => $listing['max_pages'],
			'found_posts'     => $listing['found_posts'],
			'category'        => $listing['category'],
		)
	);
}
add_action( 'wp_ajax_ogc_filter_blog_posts', 'ogc_ajax_filter_blog_posts' );
add_action( 'wp_ajax_nopriv_ogc_filter_blog_posts', 'ogc_ajax_filter_blog_posts' );

/* --------------------------------------------------------------------------
   Likes
   -------------------------------------------------------------------------- */

/**
 * Cookie name for a liked post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function ogc_like_cookie_name( $post_id ) {
	return 'ogc_liked_' . (int) $post_id;
}

/**
 * Get stored like count for a post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function ogc_get_like_count( $post_id ) {
	return max( 0, (int) get_post_meta( (int) $post_id, '_ogc_like_count', true ) );
}

/**
 * Whether the current visitor already liked this post (cookie check).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ogc_visitor_has_liked( $post_id ) {
	$cookie = ogc_like_cookie_name( $post_id );
	return ! empty( $_COOKIE[ $cookie ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
}

/**
 * AJAX: increment post like count once per visitor cookie.
 */
function ogc_ajax_like_post() {
	check_ajax_referer( 'ogc_blog_like', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid post.', 'ogc-newfinity' ) ), 400 );
	}

	if ( ogc_visitor_has_liked( $post_id ) ) {
		wp_send_json_success(
			array(
				'count'  => ogc_get_like_count( $post_id ),
				'liked'  => true,
				'already'=> true,
			)
		);
	}

	$count = ogc_get_like_count( $post_id ) + 1;
	update_post_meta( $post_id, '_ogc_like_count', $count );

	$cookie = ogc_like_cookie_name( $post_id );
	$expire = time() + YEAR_IN_SECONDS;
	$path   = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
	$domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
	setcookie( $cookie, '1', $expire, $path, $domain, is_ssl(), true );
	$_COOKIE[ $cookie ] = '1';

	wp_send_json_success(
		array(
			'count' => $count,
			'liked' => true,
		)
	);
}
add_action( 'wp_ajax_ogc_like_post', 'ogc_ajax_like_post' );
add_action( 'wp_ajax_nopriv_ogc_like_post', 'ogc_ajax_like_post' );

/* --------------------------------------------------------------------------
   Comments — moderation, pagination, link blocking, list markup
   -------------------------------------------------------------------------- */

/**
 * Ensure comment moderation + 4-per-page pagination on theme load.
 */
function ogc_configure_blog_comments() {
	$flag = '1.0.0';
	if ( get_option( 'ogc_blog_comments_configured' ) === $flag ) {
		return;
	}

	update_option( 'comment_moderation', '1' );
	update_option( 'moderation_notify', '1' );
	update_option( 'page_comments', 1 );
	update_option( 'comments_per_page', 4 );
	update_option( 'default_comments_page', 'newest' );
	update_option( 'comment_order', 'asc' );
	update_option( 'require_name_email', 0 ); // Name optional; we still require email via form HTML5.
	update_option( 'ogc_blog_comments_configured', $flag );
}
add_action( 'after_setup_theme', 'ogc_configure_blog_comments', 30 );
add_action( 'after_switch_theme', 'ogc_configure_blog_comments' );

/**
 * Whether comment text contains a URL-like pattern.
 *
 * @param string $text Comment content.
 * @return bool
 */
function ogc_comment_contains_url( $text ) {
	$text = (string) $text;
	if ( preg_match( '#https?://#i', $text ) ) {
		return true;
	}
	if ( preg_match( '#www\.#i', $text ) ) {
		return true;
	}
	if ( preg_match( '#\b[a-z0-9][a-z0-9.-]*\.(com|net|org|io|co|nl|dev|app|info|biz|me|xyz)\b#i', $text ) ) {
		return true;
	}
	return false;
}

/**
 * Reject comments that include links before they reach moderation.
 *
 * @param array $commentdata Comment data.
 * @return array
 */
function ogc_block_comment_links( $commentdata ) {
	if ( empty( $commentdata['comment_content'] ) ) {
		return $commentdata;
	}

	if ( ogc_comment_contains_url( $commentdata['comment_content'] ) ) {
		wp_die(
			esc_html__( "Comments can't contain links — please remove any URLs and try again.", 'ogc-newfinity' ),
			esc_html__( 'Comment blocked', 'ogc-newfinity' ),
			array(
				'response'  => 400,
				'back_link' => true,
			)
		);
	}

	return $commentdata;
}
add_filter( 'preprocess_comment', 'ogc_block_comment_links' );

/**
 * Never collect a website URL on the comment form.
 *
 * @param array $fields Default fields.
 * @return array
 */
function ogc_remove_comment_url_field( $fields ) {
	unset( $fields['url'] );
	unset( $fields['cookies'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'ogc_remove_comment_url_field' );
add_filter( 'comment_form_field_cookies', '__return_empty_string' );

/**
 * Keep field order: Name → Email → Message (not Message-first).
 *
 * @param array $fields Comment form fields including comment.
 * @return array
 */
function ogc_comment_form_field_order( $fields ) {
	$ordered = array();
	foreach ( array( 'author', 'email', 'comment' ) as $key ) {
		if ( isset( $fields[ $key ] ) ) {
			$ordered[ $key ] = $fields[ $key ];
		}
	}
	foreach ( $fields as $key => $markup ) {
		if ( ! isset( $ordered[ $key ] ) ) {
			$ordered[ $key ] = $markup;
		}
	}
	return $ordered;
}
add_filter( 'comment_form_fields', 'ogc_comment_form_field_order' );

/**
 * Require email even when WP name/email requirement is relaxed for optional name.
 *
 * @param array $commentdata Comment data.
 * @return array
 */
function ogc_require_comment_email( $commentdata ) {
	$email = isset( $commentdata['comment_author_email'] ) ? trim( (string) $commentdata['comment_author_email'] ) : '';
	if ( '' === $email || ! is_email( $email ) ) {
		wp_die(
			esc_html__( 'Please enter a valid email address.', 'ogc-newfinity' ),
			esc_html__( 'Comment blocked', 'ogc-newfinity' ),
			array(
				'response'  => 400,
				'back_link' => true,
			)
		);
	}
	return $commentdata;
}
add_filter( 'preprocess_comment', 'ogc_require_comment_email', 5 );

/**
 * Custom comment list item markup.
 *
 * @param WP_Comment $comment Comment object.
 * @param array      $args    wp_list_comments args.
 * @param int        $depth   Depth.
 */
function ogc_comment_list_item( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class( 'ogc-comment', $comment ); ?> id="comment-<?php comment_ID(); ?>">
		<article class="ogc-comment__body">
			<header class="ogc-comment__meta">
				<span class="ogc-comment__author"><?php echo esc_html( get_comment_author( $comment ) ); ?></span>
				<span class="ogc-comment__sep" aria-hidden="true">·</span>
				<time class="ogc-comment__date" datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>">
					<?php echo esc_html( get_comment_date( '', $comment ) ); ?>
				</time>
			</header>
			<div class="ogc-comment__content">
				<?php comment_text( $comment ); ?>
			</div>
			<?php if ( '0' === (string) $comment->comment_approved ) : ?>
				<p class="ogc-comment__awaiting"><?php esc_html_e( 'Your comment is awaiting moderation.', 'ogc-newfinity' ); ?></p>
			<?php endif; ?>
		</article>
	<?php
	// Closing </li> is handled by WordPress walker.
}

