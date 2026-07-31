<?php
/**
 * Theme supports, menus, and related setup.
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

/**
 * Ensure the site uses a static front page so front-page.php is the homepage.
 * Creates a "Home" page if missing. Leaves SEO title/meta empty for the client's SEO plugin.
 */
function ogc_configure_static_front_page() {
	$flag = '1.0.0';
	if ( get_option( 'ogc_static_front_configured' ) === $flag ) {
		return;
	}

	$home = get_page_by_path( 'home', OBJECT, 'page' );

	if ( ! $home instanceof WP_Post ) {
		$matches = get_posts(
			array(
				'name'           => 'home',
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		if ( ! empty( $matches[0] ) ) {
			$home = $matches[0];
		}
	}

	if ( ! $home instanceof WP_Post ) {
		$home_id = wp_insert_post(
			array(
				'post_title'   => 'Home',
				'post_name'    => 'home',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1,
			),
			true
		);
		if ( is_wp_error( $home_id ) || ! $home_id ) {
			return;
		}
	} else {
		$home_id = (int) $home->ID;
		if ( 'publish' !== $home->post_status ) {
			wp_update_post(
				array(
					'ID'          => $home_id,
					'post_status' => 'publish',
				)
			);
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );

	// Blog index: use the seeded Blog & Insights page when available.
	$blog = get_page_by_path( 'blog', OBJECT, 'page' );
	if ( $blog instanceof WP_Post ) {
		update_option( 'page_for_posts', (int) $blog->ID );
	}

	update_option( 'ogc_static_front_configured', $flag );
}
add_action( 'after_setup_theme', 'ogc_configure_static_front_page', 20 );
add_action( 'init', 'ogc_configure_static_front_page', 30 );

/**
 * Output theme favicon / apple-touch-icon link tags.
 * Prefers WordPress Site Icon (Customizer → Site Identity) when set;
 * otherwise falls back to bundled assets in assets/images/Identity/favicon/.
 */
function ogc_output_favicons() {
	$site_icon = function_exists( 'get_site_icon_url' ) ? get_site_icon_url( 512 ) : '';
	if ( $site_icon ) {
		// Native Site Icon handles link tags via core; skip bundled duplicates.
		return;
	}

	$base = OGC_THEME_URI . '/assets/images/Identity/favicon';
	?>
	<link rel="icon" href="<?php echo esc_url( $base . '/favicon-32.png' ); ?>" sizes="32x32" type="image/png">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $base . '/apple-touch-icon.png' ); ?>" sizes="180x180">
	<link rel="icon" href="<?php echo esc_url( $base . '/icon-512.png' ); ?>" sizes="512x512" type="image/png">
	<?php
}
add_action( 'wp_head', 'ogc_output_favicons', 2 );

/**
 * Sideload the 512px mark into the Media Library and set WordPress Site Icon once.
 * Re-runs when OGC_FAVICON_SEED_VERSION is bumped (new logo artwork).
 */
function ogc_maybe_register_site_icon() {
	$seed = '1.3.1';
	if ( get_option( 'ogc_favicon_seed_version' ) === $seed ) {
		return;
	}

	$source = OGC_THEME_DIR . '/assets/images/Identity/favicon/icon-512.png';
	if ( ! file_exists( $source ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$bits = wp_upload_bits( 'ogc-newfinity-icon-512.png', null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $bits['error'] ) || empty( $bits['file'] ) ) {
		return;
	}

	$filetype   = wp_check_filetype( $bits['file'], null );
	$attach_id  = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => 'OGC NewFinity Site Icon',
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$bits['file']
	);

	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return;
	}

	$meta = wp_generate_attachment_metadata( $attach_id, $bits['file'] );
	wp_update_attachment_metadata( $attach_id, $meta );

	update_option( 'site_icon', (int) $attach_id );
	update_option( 'ogc_site_icon_attachment', (int) $attach_id );
	update_option( 'ogc_favicon_seed_version', $seed );
}
add_action( 'init', 'ogc_maybe_register_site_icon', 25 );

/**
 * Fallback primary nav when no menu is assigned yet.
 * Mirrors the 7-item sitemap primary nav (Submit Idea stays a CTA).
 */
function ogc_primary_nav_fallback() {
	$items = array(
		array( 'label' => __( 'Home', 'ogc-newfinity' ), 'url' => home_url( '/' ), 'icon' => 'home' ),
		array( 'label' => __( 'About Us', 'ogc-newfinity' ), 'url' => home_url( '/about-ogc-newfinity/' ), 'icon' => 'about' ),
		array( 'label' => __( 'Our Services', 'ogc-newfinity' ), 'url' => home_url( '/services/' ), 'icon' => 'services' ),
		array( 'label' => __( 'Our Mission', 'ogc-newfinity' ), 'url' => home_url( '/our-mission/' ), 'icon' => 'mission' ),
		array( 'label' => __( 'Blog & Insights', 'ogc-newfinity' ), 'url' => home_url( '/blog/' ), 'icon' => 'blog' ),
		array( 'label' => __( 'Help & Support', 'ogc-newfinity' ), 'url' => home_url( '/support/' ), 'icon' => 'support' ),
		array( 'label' => __( 'Contact', 'ogc-newfinity' ), 'url' => home_url( '/contact/' ), 'icon' => 'contact' ),
	);

	echo '<ul id="ogc-primary-menu" class="ogc-nav__list">';
	foreach ( $items as $item ) {
		echo '<li class="menu-item"><a href="' . esc_url( $item['url'] ) . '">';
		ogc_icon(
			$item['icon'],
			array(
				'size'  => 'sm',
				'class' => 'ogc-nav__icon',
			)
		);
		echo '<span class="ogc-nav__label">' . esc_html( $item['label'] ) . '</span>';
		echo '</a></li>';
	}
	echo '</ul>';
}
