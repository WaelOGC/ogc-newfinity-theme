<?php
/**
 * Front-end styles and scripts.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme stylesheets and scripts.
 */
function ogc_enqueue_assets() {
	// Allow-listed Google Fonts for the Customizer-selected heading + body pair only.
	$fonts_url  = function_exists( 'ogc_get_google_fonts_url' ) ? ogc_get_google_fonts_url() : '';
	$style_deps = array();
	if ( $fonts_url ) {
		wp_enqueue_style(
			'ogc-fonts',
			$fonts_url,
			array(),
			null
		);
		$style_deps[] = 'ogc-fonts';
	}

	wp_enqueue_style(
		'ogc-style',
		get_stylesheet_uri(),
		$style_deps,
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-background',
		OGC_THEME_URI . '/assets/css/background.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-glass',
		OGC_THEME_URI . '/assets/css/glass.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-icons',
		OGC_THEME_URI . '/assets/css/icons.css',
		array( 'ogc-style' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-main',
		OGC_THEME_URI . '/assets/css/main.css',
		array( 'ogc-background', 'ogc-glass', 'ogc-icons' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-button-effects',
		OGC_THEME_URI . '/assets/css/button-effects.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-header',
		OGC_THEME_URI . '/assets/css/header.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);

	wp_enqueue_style(
		'ogc-footer',
		OGC_THEME_URI . '/assets/css/footer.css',
		array( 'ogc-main' ),
		OGC_THEME_VERSION
	);

	$is_legal_page = is_page( array( 'privacy-policy', 'terms-and-conditions', 'cookie-policy', 'refund-policy' ) );

	$needs_home_chrome = is_front_page()
		|| is_page()
		|| $is_legal_page
		|| ogc_is_blog_surface();

	if ( $needs_home_chrome ) {
		wp_enqueue_style(
			'ogc-home',
			OGC_THEME_URI . '/assets/css/home.css',
			array( 'ogc-main', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
	}

	if ( ( is_page() && ! is_front_page() ) || $is_legal_page || ogc_is_blog_surface() ) {
		wp_enqueue_style(
			'ogc-pages',
			OGC_THEME_URI . '/assets/css/pages.css',
			array( 'ogc-home', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
	}

	if ( is_page( 'support' ) ) {
		wp_enqueue_style(
			'ogc-support',
			OGC_THEME_URI . '/assets/css/support.css',
			array( 'ogc-main', 'ogc-pages' ),
			OGC_THEME_VERSION
		);
	}

	if ( is_page( array( 'contact', 'submit-idea' ) ) ) {
		wp_enqueue_style(
			'ogc-contact',
			OGC_THEME_URI . '/assets/css/contact.css',
			array( 'ogc-main', 'ogc-pages' ),
			OGC_THEME_VERSION
		);
	}

	if ( $is_legal_page ) {
		wp_enqueue_style(
			'ogc-legal',
			OGC_THEME_URI . '/assets/css/legal.css',
			array( 'ogc-main', 'ogc-pages' ),
			OGC_THEME_VERSION
		);
	}

	if ( is_page( 'submit-idea' ) ) {
		wp_enqueue_style(
			'ogc-submit-idea',
			OGC_THEME_URI . '/assets/css/submit-idea.css',
			array( 'ogc-main', 'ogc-pages', 'ogc-contact' ),
			OGC_THEME_VERSION
		);
	}

	if ( is_page_template( 'page-pricing.php' ) || is_page( 'pricing' ) ) {
		wp_enqueue_style(
			'ogc-pricing',
			OGC_THEME_URI . '/assets/css/pricing.css',
			array( 'ogc-main', 'ogc-pages', 'ogc-glass' ),
			OGC_THEME_VERSION
		);
		wp_enqueue_script(
			'ogc-pricing',
			OGC_THEME_URI . '/assets/js/pricing.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
	}

	wp_enqueue_script(
		'ogc-three',
		'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
		array(),
		'r128',
		true
	);

	wp_enqueue_script(
		'ogc-space-background',
		OGC_THEME_URI . '/assets/js/space-background.js',
		array( 'ogc-three' ),
		OGC_THEME_VERSION,
		true
	);
	wp_localize_script(
		'ogc-space-background',
		'ogcSpaceBg',
		array( 'themeUri' => OGC_THEME_URI )
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'ogc-brand-cube',
			OGC_THEME_URI . '/assets/js/brand-cube.js',
			array( 'ogc-three' ),
			OGC_THEME_VERSION,
			true
		);
		$location = ogc_get_location_text();
		$email    = ogc_get_contact_email();
		wp_localize_script(
			'ogc-brand-cube',
			'ogcBrandCube',
			array(
				'homeUrl' => home_url( '/' ),
				'faces'   => array(
					array(
						'title' => __( 'About us', 'ogc-newfinity' ),
						'btn'   => __( 'Learn more', 'ogc-newfinity' ),
						'body'  => sprintf(
							/* translators: %s: location text */
							__( 'OGC NewFinity is a digital agency based in %s, founded in 2022. We build technology with a simple standard: it has to actually work for the people using it.', 'ogc-newfinity' ),
							$location
						),
						'url'   => home_url( '/about-ogc-newfinity/' ),
					),
					array( 'title' => __( 'Our services',   'ogc-newfinity' ), 'btn' => __( 'View services',    'ogc-newfinity' ), 'body' => __( 'Software, WordPress, UI/UX, marketing strategy, cybersecurity, and AI — six capabilities, one team.', 'ogc-newfinity' ), 'url' => home_url( '/services/' ) ),
					array( 'title' => __( 'Our mission',    'ogc-newfinity' ), 'btn' => __( 'Read our mission', 'ogc-newfinity' ), 'body' => __( 'Ethical innovation, built with intention. We measure success by whether our work actually earns your trust.', 'ogc-newfinity' ), 'url' => home_url( '/our-mission/' ) ),
					array(
						'title' => __( 'Contact us', 'ogc-newfinity' ),
						'btn'   => __( 'Get in touch', 'ogc-newfinity' ),
						'body'  => sprintf(
							/* translators: 1: location text, 2: contact email */
							__( 'Based in %1$s. %2$s — every message gets a real reply within 48 hours.', 'ogc-newfinity' ),
							$location,
							$email
						),
						'url'   => home_url( '/contact/' ),
					),
					array( 'title' => __( 'Leave feedback', 'ogc-newfinity' ), 'btn' => __( 'Share feedback',   'ogc-newfinity' ), 'body' => __( "Had an experience with us? We'd like to hear about it, good or bad.", 'ogc-newfinity' ), 'url' => home_url( '/support/' ) ),
					array( 'title' => __( 'AI support',     'ogc-newfinity' ), 'btn' => __( 'Chat now',         'ogc-newfinity' ), 'body' => __( 'Talk to our assistant for quick answers about our services and process.', 'ogc-newfinity' ), 'url' => home_url( '/support/' ) ),
				),
			)
		);
	}

	if ( is_page( 'about-ogc-newfinity' ) ) {
		wp_enqueue_script(
			'ogc-orbit-emblem',
			OGC_THEME_URI . '/assets/js/orbit-emblem.js',
			array( 'ogc-three' ),
			OGC_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'support' ) ) {
		wp_enqueue_script(
			'ogc-support-network',
			OGC_THEME_URI . '/assets/js/support-network.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
		wp_enqueue_script(
			'ogc-faq-filter',
			OGC_THEME_URI . '/assets/js/faq-filter.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'contact' ) ) {
		wp_enqueue_script(
			'ogc-envelope-send',
			OGC_THEME_URI . '/assets/js/envelope-send.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
		wp_enqueue_script(
			'ogc-contact-form',
			OGC_THEME_URI . '/assets/js/contact-form.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'submit-idea' ) ) {
		wp_enqueue_script(
			'ogc-idea-blueprint',
			OGC_THEME_URI . '/assets/js/idea-blueprint.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'services' ) ) {
		wp_enqueue_script(
			'ogc-services-code-screen',
			OGC_THEME_URI . '/assets/js/services-code-screen.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'our-mission' ) ) {
		wp_enqueue_script(
			'ogc-mission-compass',
			OGC_THEME_URI . '/assets/js/mission-compass.js',
			array( 'ogc-three' ),
			OGC_THEME_VERSION,
			true
		);
	}

	wp_enqueue_script(
		'ogc-orphan-control',
		OGC_THEME_URI . '/assets/js/orphan-control.js',
		array(),
		OGC_THEME_VERSION,
		true
	);

	if ( is_home() ) {
		wp_enqueue_script(
			'ogc-blog-filter',
			OGC_THEME_URI . '/assets/js/blog-filter.js',
			array( 'ogc-orphan-control' ),
			OGC_THEME_VERSION,
			true
		);
		wp_localize_script(
			'ogc-blog-filter',
			'ogcBlogFilter',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ogc_blog_filter' ),
			)
		);
	}

	if ( is_singular( 'post' ) ) {
		wp_enqueue_script(
			'ogc-blog-engage',
			OGC_THEME_URI . '/assets/js/blog-engage.js',
			array(),
			OGC_THEME_VERSION,
			true
		);
		wp_localize_script(
			'ogc-blog-engage',
			'ogcBlogEngage',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ogc_blog_like' ),
				'i18n'    => array(
					'liked'      => __( 'Liked', 'ogc-newfinity' ),
					'likedLabel' => __( 'Liked', 'ogc-newfinity' ),
				),
			)
		);
	}

	wp_enqueue_script(
		'ogc-main',
		OGC_THEME_URI . '/assets/js/main.js',
		array(),
		OGC_THEME_VERSION,
		true
	);

	// Self-gating: bails unless a body class selects the ripple hover effect.
	wp_enqueue_script(
		'ogc-button-ripple',
		OGC_THEME_URI . '/assets/js/button-ripple.js',
		array(),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ogc_enqueue_assets' );
