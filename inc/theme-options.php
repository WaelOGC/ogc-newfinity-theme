<?php
/**
 * OGC NewFinity Theme Options — schema, admin panel, getters, and front-end output.
 *
 * Storage stays on theme_mods (get_theme_mod / set_theme_mod) so nothing that was
 * configured through the old Customizer panel is lost. The Customizer registration
 * is gone; this standalone admin page is the single place to edit theme settings.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * 1. Defaults / constants
 * ====================================================================== */

/** Default accent gradient start (matches style.css --accent-gradient-start). */
define( 'OGC_DEFAULT_COLOR_ACCENT_START', '#FFD27A' );

/** Default accent gradient end (matches style.css --accent-gradient-end). */
define( 'OGC_DEFAULT_COLOR_ACCENT_END', '#FFEAC2' );

/** Default background (matches style.css --bg-primary). */
define( 'OGC_DEFAULT_COLOR_BG', '#0A0A0A' );

/** Default primary text (matches style.css --text-primary). */
define( 'OGC_DEFAULT_COLOR_TEXT_PRIMARY', '#F5F5F5' );

/** Default secondary text (matches style.css --text-secondary). */
define( 'OGC_DEFAULT_COLOR_TEXT_SECONDARY', '#C9C9C9' );

/** Default heading font. */
define( 'OGC_DEFAULT_FONT_HEADING', 'Space Grotesk' );

/** Default body font. */
define( 'OGC_DEFAULT_FONT_BODY', 'Inter' );

/** Default footer tagline (matches footer.php). */
define(
	'OGC_DEFAULT_FOOTER_TAGLINE',
	'OGC NewFinity is a digital agency in The Hague, Netherlands, delivering ethical innovation across software and app development, custom WordPress, UI/UX, marketing strategy, cybersecurity, and AI solutions.'
);

/** Default contact email. */
define( 'OGC_DEFAULT_CONTACT_EMAIL', 'contact@ogcnewfinity.com' );

/** Default location text. */
define( 'OGC_DEFAULT_LOCATION_TEXT', 'The Hague, Netherlands' );

/** Default response-time guarantee label. */
define( 'OGC_DEFAULT_RESPONSE_TIME', '48h Response' );

/** Admin page slug. */
define( 'OGC_OPTIONS_PAGE_SLUG', 'ogc-theme-options' );

/** Capability required to view / save theme options. */
define( 'OGC_OPTIONS_CAPABILITY', 'edit_theme_options' );

/* =========================================================================
 * 2. Choice tables
 * ====================================================================== */

/**
 * Google Fonts library: display name => family slug + weights the family ships.
 *
 * Weights are used to build a valid CSS2 request — asking Google for a weight a
 * family does not provide returns HTTP 400 and silently drops the whole font.
 *
 * @return array<string, array{family:string, weights:int[]}>
 */
function ogc_font_library() {
	return array(
		'Space Grotesk'        => array( 'family' => 'Space+Grotesk', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Playfair Display'     => array( 'family' => 'Playfair+Display', 'weights' => array( 400, 500, 600, 700, 800, 900 ) ),
		'Poppins'              => array( 'family' => 'Poppins', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Sora'                 => array( 'family' => 'Sora', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Manrope'              => array( 'family' => 'Manrope', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Fraunces'             => array( 'family' => 'Fraunces', 'weights' => array( 300, 400, 500, 600, 700, 800, 900 ) ),
		'DM Serif Display'     => array( 'family' => 'DM+Serif+Display', 'weights' => array( 400 ) ),
		'Cormorant Garamond'   => array( 'family' => 'Cormorant+Garamond', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Libre Baskerville'    => array( 'family' => 'Libre+Baskerville', 'weights' => array( 400, 700 ) ),
		'Outfit'               => array( 'family' => 'Outfit', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Plus Jakarta Sans'    => array( 'family' => 'Plus+Jakarta+Sans', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Urbanist'             => array( 'family' => 'Urbanist', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Figtree'              => array( 'family' => 'Figtree', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Syne'                 => array( 'family' => 'Syne', 'weights' => array( 400, 500, 600, 700, 800 ) ),
		'Instrument Sans'      => array( 'family' => 'Instrument+Sans', 'weights' => array( 400, 500, 600, 700 ) ),
		'Bricolage Grotesque'  => array( 'family' => 'Bricolage+Grotesque', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Archivo'              => array( 'family' => 'Archivo', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Inter'                => array( 'family' => 'Inter', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Work Sans'            => array( 'family' => 'Work+Sans', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Source Sans 3'        => array( 'family' => 'Source+Sans+3', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Nunito Sans'          => array( 'family' => 'Nunito+Sans', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'DM Sans'              => array( 'family' => 'DM+Sans', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'IBM Plex Sans'        => array( 'family' => 'IBM+Plex+Sans', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Lato'                 => array( 'family' => 'Lato', 'weights' => array( 300, 400, 700 ) ),
		'Rubik'                => array( 'family' => 'Rubik', 'weights' => array( 300, 400, 500, 600, 700 ) ),
		'Karla'                => array( 'family' => 'Karla', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Mulish'               => array( 'family' => 'Mulish', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Public Sans'          => array( 'family' => 'Public+Sans', 'weights' => array( 300, 400, 500, 600, 700, 800 ) ),
		'Atkinson Hyperlegible' => array( 'family' => 'Atkinson+Hyperlegible', 'weights' => array( 400, 700 ) ),
	);
}

/**
 * Build a name => name choice map from a list of font names.
 *
 * @param string[] $names Font display names.
 * @return array<string, string>
 */
function ogc_font_choice_map( $names ) {
	$library = ogc_font_library();
	$out     = array();
	foreach ( $names as $name ) {
		if ( isset( $library[ $name ] ) ) {
			$out[ $name ] = $name;
		}
	}
	return $out;
}

/**
 * Allow-listed heading fonts.
 *
 * @return array<string, string>
 */
function ogc_heading_font_choices() {
	return ogc_font_choice_map(
		array(
			'Space Grotesk',
			'Playfair Display',
			'Poppins',
			'Sora',
			'Manrope',
			'Fraunces',
			'DM Serif Display',
			'Cormorant Garamond',
			'Libre Baskerville',
			'Outfit',
			'Plus Jakarta Sans',
			'Urbanist',
			'Figtree',
			'Syne',
			'Instrument Sans',
			'Bricolage Grotesque',
			'Archivo',
			'Inter',
		)
	);
}

/**
 * Allow-listed body fonts.
 *
 * @return array<string, string>
 */
function ogc_body_font_choices() {
	return ogc_font_choice_map(
		array(
			'Inter',
			'Work Sans',
			'Source Sans 3',
			'Nunito Sans',
			'Plus Jakarta Sans',
			'Outfit',
			'DM Sans',
			'IBM Plex Sans',
			'Lato',
			'Rubik',
			'Karla',
			'Mulish',
			'Public Sans',
			'Atkinson Hyperlegible',
			'Manrope',
			'Figtree',
		)
	);
}

/**
 * Swoosh underline style choices.
 *
 * @return array<string, string>
 */
function ogc_swoosh_style_choices() {
	return array(
		'gradient' => __( 'Gradient (default)', 'ogc-newfinity' ),
		'solid'    => __( 'Solid accent', 'ogc-newfinity' ),
		'none'     => __( 'None', 'ogc-newfinity' ),
	);
}

/**
 * Button hover effect choices.
 *
 * @return array<string, string>
 */
function ogc_button_hover_effect_choices() {
	return array(
		'lift'            => __( 'Lift', 'ogc-newfinity' ),
		'glow'            => __( 'Glow', 'ogc-newfinity' ),
		'scale'           => __( 'Scale', 'ogc-newfinity' ),
		'fill-sweep'      => __( 'Fill sweep', 'ogc-newfinity' ),
		'border-pulse'    => __( 'Border pulse', 'ogc-newfinity' ),
		'gradient-shift'  => __( 'Gradient shift', 'ogc-newfinity' ),
		'icon-slide'      => __( 'Icon slide', 'ogc-newfinity' ),
		'ripple'          => __( 'Ripple', 'ogc-newfinity' ),
		'none'            => __( 'None', 'ogc-newfinity' ),
	);
}

/**
 * Hover duration choices.
 *
 * @return array<string, string>
 */
function ogc_speed_choices() {
	return array(
		'fast'   => __( 'Fast', 'ogc-newfinity' ),
		'normal' => __( 'Normal', 'ogc-newfinity' ),
		'slow'   => __( 'Slow', 'ogc-newfinity' ),
	);
}

/**
 * Hover intensity choices.
 *
 * @return array<string, string>
 */
function ogc_intensity_choices() {
	return array(
		'subtle' => __( 'Subtle', 'ogc-newfinity' ),
		'medium' => __( 'Medium', 'ogc-newfinity' ),
		'strong' => __( 'Strong', 'ogc-newfinity' ),
	);
}

/**
 * Nav slug => label + relative path. Drives the header nav ordering control.
 *
 * @return array<string, array{label:string, path:string}>
 */
function ogc_nav_slug_map() {
	return array(
		'home'     => array( 'label' => __( 'Home', 'ogc-newfinity' ), 'path' => '/' ),
		'about'    => array( 'label' => __( 'About Us', 'ogc-newfinity' ), 'path' => '/about-ogc-newfinity/' ),
		'services' => array( 'label' => __( 'Our Services', 'ogc-newfinity' ), 'path' => '/services/' ),
		'mission'  => array( 'label' => __( 'Our Mission', 'ogc-newfinity' ), 'path' => '/our-mission/' ),
		'blog'     => array( 'label' => __( 'Blog & Insights', 'ogc-newfinity' ), 'path' => '/blog/' ),
		'support'  => array( 'label' => __( 'Help & Support', 'ogc-newfinity' ), 'path' => '/support/' ),
		'contact'  => array( 'label' => __( 'Contact', 'ogc-newfinity' ), 'path' => '/contact/' ),
	);
}

/**
 * Default header nav order JSON (all seven items, visible, current order).
 *
 * @return string
 */
function ogc_default_nav_order_json() {
	$rows = array();
	foreach ( array_keys( ogc_nav_slug_map() ) as $slug ) {
		$rows[] = array(
			'slug'    => $slug,
			'visible' => true,
		);
	}
	return wp_json_encode( $rows );
}

/**
 * Default footer link columns JSON (mirrors the current hard-coded footer).
 *
 * Brand and Contact columns stay in PHP; only these link lists are editable.
 *
 * @return string
 */
function ogc_default_footer_columns_json() {
	return wp_json_encode(
		array(
			array(
				'title' => 'Quick Links',
				'links' => array(
					array( 'label' => 'Our Services', 'url' => '/services/' ),
					array( 'label' => 'Our Mission', 'url' => '/our-mission/' ),
					array( 'label' => 'Submit Your Idea', 'url' => '/submit-idea/' ),
					array( 'label' => 'Blog & Insights', 'url' => '/blog/' ),
					array( 'label' => 'Help & Support', 'url' => '/support/' ),
				),
			),
			array(
				'title' => 'Legal',
				'links' => array(
					array( 'label' => 'Privacy Policy', 'url' => '/privacy-policy/' ),
					array( 'label' => 'Terms and Conditions', 'url' => '/terms-and-conditions/' ),
					array( 'label' => 'Cookie Policy', 'url' => '/cookie-policy/' ),
					array( 'label' => 'Refund Policy', 'url' => '/refund-policy/' ),
				),
			),
		)
	);
}

/**
 * Default ad slot configuration JSON.
 *
 * @return string
 */
function ogc_default_ads_config_json() {
	return wp_json_encode(
		array(
			array( 'key' => 'sidebar', 'enabled' => false, 'type' => 'image', 'image' => '', 'url' => '', 'code' => '' ),
			array( 'key' => 'inline', 'enabled' => false, 'type' => 'image', 'image' => '', 'url' => '', 'code' => '' ),
		)
	);
}

/* =========================================================================
 * 3. Schema
 * ====================================================================== */

/**
 * The single source of truth for every theme option.
 *
 * Each tab: label, icon (dashicon class), description, fields[].
 * Each field: key, type, label, tooltip, default, sanitize, options, attrs.
 *
 * @return array<string, array<string, mixed>>
 */
function ogc_theme_options_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$socials = array(
		'x'        => array( 'X (Twitter)', 'https://x.com/OGCNewfinity' ),
		'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/company/ogc-newfinity' ),
		'github'   => array( 'GitHub', 'https://github.com/OGC-NewFinity' ),
		'discord'  => array( 'Discord', 'https://discord.com/invite/AyzKNHdn' ),
	);

	$social_fields = array(
		array(
			'key'     => 'ogc_social_heading',
			'type'    => 'heading',
			'label'   => __( 'Social links', 'ogc-newfinity' ),
			'tooltip' => __( 'Clear a field to hide that icon in the footer.', 'ogc-newfinity' ),
		),
	);
	foreach ( $socials as $network => $data ) {
		$social_fields[] = array(
			'key'     => 'ogc_social_' . $network,
			'type'    => 'url',
			/* translators: %s: social network name. */
			'label'   => sprintf( __( '%s URL', 'ogc-newfinity' ), $data[0] ),
			'tooltip' => __( 'Leave empty to hide this social icon.', 'ogc-newfinity' ),
			'default' => $data[1],
		);
	}

	$hero_pages = array(
		'home'     => __( 'Home', 'ogc-newfinity' ),
		'about'    => __( 'About', 'ogc-newfinity' ),
		'services' => __( 'Services', 'ogc-newfinity' ),
		'mission'  => __( 'Mission', 'ogc-newfinity' ),
	);

	$hero_image_fields = array();
	$hero_opacity_fields = array();
	foreach ( $hero_pages as $page_key => $page_label ) {
		$hero_image_fields[] = array(
			'key'     => 'ogc_hero_bg_' . $page_key,
			'type'    => 'image',
			/* translators: %s: page name. */
			'label'   => sprintf( __( '%s hero background', 'ogc-newfinity' ), $page_label ),
			'tooltip' => __( 'Additive layer behind the existing hero canvas. Leave empty to keep the WebGL hero untouched.', 'ogc-newfinity' ),
		);
		$hero_opacity_fields[] = array(
			'key'     => 'ogc_hero_bg_opacity_' . $page_key,
			'type'    => 'range',
			/* translators: %s: page name. */
			'label'   => sprintf( __( '%s hero background opacity', 'ogc-newfinity' ), $page_label ),
			'tooltip' => __( 'Only applies when a hero background image is set for this page.', 'ogc-newfinity' ),
			'default' => 35,
			'attrs'   => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
		);
	}

	$schema = array(

		/* ------------------------------------------------------------ General */
		'general'  => array(
			'label'       => __( 'General', 'ogc-newfinity' ),
			'icon'        => 'dashicons-admin-generic',
			'description' => __( 'Contact details, analytics, and site availability.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_contact_email',
					'type'    => 'email',
					'label'   => __( 'Contact email', 'ogc-newfinity' ),
					'tooltip' => __( 'Shown in the footer and used as the reply-to for site forms.', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_CONTACT_EMAIL,
				),
				array(
					'key'     => 'ogc_location_text',
					'type'    => 'text',
					'label'   => __( 'Location text', 'ogc-newfinity' ),
					'tooltip' => __( 'Appears in the footer contact block and the home brand cube.', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_LOCATION_TEXT,
				),
				array(
					'key'     => 'ogc_response_time_text',
					'type'    => 'text',
					'label'   => __( 'Response-time guarantee', 'ogc-newfinity' ),
					'tooltip' => __( 'Short badge text, e.g. "48h Response".', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_RESPONSE_TIME,
				),
				array(
					'key'     => 'ogc_tracking_heading',
					'type'    => 'heading',
					'label'   => __( 'Tracking', 'ogc-newfinity' ),
					'tooltip' => __( 'Scripts only load on the front end, never in the admin.', 'ogc-newfinity' ),
				),
				array(
					'key'      => 'ogc_google_analytics_id',
					'type'     => 'text',
					'label'    => __( 'Google Analytics 4 ID', 'ogc-newfinity' ),
					'tooltip'  => __( 'Measurement ID such as G-XXXXXXXXXX. Leave empty to disable.', 'ogc-newfinity' ),
					'default'  => '',
					'sanitize' => 'ogc_sanitize_tracking_id',
					'attrs'    => array( 'placeholder' => 'G-XXXXXXXXXX' ),
				),
				array(
					'key'      => 'ogc_meta_pixel_id',
					'type'     => 'text',
					'label'    => __( 'Meta Pixel ID', 'ogc-newfinity' ),
					'tooltip'  => __( 'Numeric pixel ID. Leave empty to disable.', 'ogc-newfinity' ),
					'default'  => '',
					'sanitize' => 'ogc_sanitize_tracking_id',
					'attrs'    => array( 'placeholder' => '000000000000000' ),
				),
				array(
					'key'   => 'ogc_maintenance_heading',
					'type'  => 'heading',
					'label' => __( 'Maintenance mode', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_maintenance_mode_enabled',
					'type'    => 'checkbox',
					'label'   => __( 'Enable maintenance mode', 'ogc-newfinity' ),
					'tooltip' => __( 'Visitors see a holding message; editors and administrators keep full access.', 'ogc-newfinity' ),
					'default' => false,
				),
				array(
					'key'      => 'ogc_maintenance_mode_message',
					'type'     => 'textarea',
					'label'    => __( 'Maintenance message', 'ogc-newfinity' ),
					'default'  => __( "We'll be back shortly.", 'ogc-newfinity' ),
					'sanitize' => 'wp_kses_post',
				),
			),
		),

		/* ------------------------------------------------------------- Header */
		'header'   => array(
			'label'       => __( 'Header', 'ogc-newfinity' ),
			'icon'        => 'dashicons-align-center',
			'description' => __( 'Logo, bar styling, and primary navigation order.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_header_logo',
					'type'    => 'image',
					'label'   => __( 'Header logo', 'ogc-newfinity' ),
					'tooltip' => __( 'Falls back to the Site Identity logo, then the bundled theme logo.', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_header_logo_width_desktop',
					'type'    => 'number',
					'label'   => __( 'Logo width (desktop)', 'ogc-newfinity' ),
					'default' => 40,
					'attrs'   => array( 'min' => 16, 'max' => 320, 'step' => 1, 'unit' => 'px' ),
				),
				array(
					'key'     => 'ogc_header_logo_width_mobile',
					'type'    => 'number',
					'label'   => __( 'Logo width (mobile)', 'ogc-newfinity' ),
					'default' => 40,
					'attrs'   => array( 'min' => 16, 'max' => 320, 'step' => 1, 'unit' => 'px' ),
				),
				array(
					'key'     => 'ogc_header_bg_color',
					'type'    => 'color',
					'label'   => __( 'Header background', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_BG,
				),
				array(
					'key'     => 'ogc_header_bg_opacity',
					'type'    => 'range',
					'label'   => __( 'Header background opacity', 'ogc-newfinity' ),
					'tooltip' => __( '0% keeps the current fully transparent header.', 'ogc-newfinity' ),
					'default' => 0,
					'attrs'   => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
				),
				array(
					'key'     => 'ogc_header_text_color',
					'type'    => 'color',
					'label'   => __( 'Header link colour', 'ogc-newfinity' ),
					'default' => '#FFD27A',
				),
				array(
					'key'     => 'ogc_header_sticky',
					'type'    => 'checkbox',
					'label'   => __( 'Sticky header', 'ogc-newfinity' ),
					'tooltip' => __( 'Keeps the header pinned while scrolling.', 'ogc-newfinity' ),
					'default' => true,
				),
				array(
					'key'     => 'ogc_header_nav_order',
					'type'    => 'nav_order',
					'label'   => __( 'Primary navigation', 'ogc-newfinity' ),
					'tooltip' => __( 'Drag to reorder, untick to hide. Applies to the built-in fallback menu.', 'ogc-newfinity' ),
					'default' => ogc_default_nav_order_json(),
				),
			),
		),

		/* ------------------------------------------------------------- Footer */
		'footer'   => array(
			'label'       => __( 'Footer', 'ogc-newfinity' ),
			'icon'        => 'dashicons-editor-insertmore',
			'description' => __( 'Branding, editable link columns, social profiles, and copyright.', 'ogc-newfinity' ),
			'fields'      => array_merge(
				array(
					array(
						'key'     => 'ogc_footer_logo',
						'type'    => 'image',
						'label'   => __( 'Footer logo', 'ogc-newfinity' ),
						'tooltip' => __( 'Falls back to the header logo, then the bundled theme logo.', 'ogc-newfinity' ),
					),
					array(
						'key'     => 'ogc_footer_logo_width',
						'type'    => 'number',
						'label'   => __( 'Footer logo width', 'ogc-newfinity' ),
						'default' => 54,
						'attrs'   => array( 'min' => 16, 'max' => 320, 'step' => 1, 'unit' => 'px' ),
					),
					array(
						'key'     => 'ogc_footer_bg_color',
						'type'    => 'color',
						'label'   => __( 'Footer background', 'ogc-newfinity' ),
						'default' => OGC_DEFAULT_COLOR_BG,
					),
					array(
						'key'     => 'ogc_footer_tagline',
						'type'    => 'textarea',
						'label'   => __( 'Footer tagline', 'ogc-newfinity' ),
						'default' => OGC_DEFAULT_FOOTER_TAGLINE,
					),
					array(
						'key'     => 'ogc_footer_columns_json',
						'type'    => 'footer_columns',
						'label'   => __( 'Footer link columns', 'ogc-newfinity' ),
						'tooltip' => __( 'The brand and contact columns are fixed. These columns render between them.', 'ogc-newfinity' ),
						'default' => ogc_default_footer_columns_json(),
					),
					array(
						'key'      => 'ogc_footer_copyright_text',
						'type'     => 'text',
						'label'    => __( 'Copyright line', 'ogc-newfinity' ),
						'tooltip'  => __( 'Use {year} for the current year and {start_year} for 2022.', 'ogc-newfinity' ),
						'default'  => '© {year} OGC NewFinity. All rights reserved.',
						'sanitize' => 'wp_kses_post',
					),
				),
				$social_fields
			),
		),

		/* ------------------------------------------------------------- Colors */
		'colors'   => array(
			'label'       => __( 'Colors', 'ogc-newfinity' ),
			'icon'        => 'dashicons-art',
			'description' => __( 'Brand palette. Every value maps to a CSS custom property.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_color_accent_start',
					'type'    => 'color',
					'label'   => __( 'Accent gradient start', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_ACCENT_START,
				),
				array(
					'key'     => 'ogc_color_accent_end',
					'type'    => 'color',
					'label'   => __( 'Accent gradient end', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_ACCENT_END,
				),
				array(
					'key'     => 'ogc_color_bg',
					'type'    => 'color',
					'label'   => __( 'Page background', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_BG,
				),
				array(
					'key'     => 'ogc_color_text_primary',
					'type'    => 'color',
					'label'   => __( 'Text (primary)', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_TEXT_PRIMARY,
				),
				array(
					'key'     => 'ogc_color_text_secondary',
					'type'    => 'color',
					'label'   => __( 'Text (secondary)', 'ogc-newfinity' ),
					'default' => OGC_DEFAULT_COLOR_TEXT_SECONDARY,
				),
				array(
					'key'     => 'ogc_color_border',
					'type'    => 'color',
					'label'   => __( 'Border / accent line', 'ogc-newfinity' ),
					'default' => '#FFD27A',
				),
				array(
					'key'   => 'ogc_button_color_heading',
					'type'  => 'heading',
					'label' => __( 'Buttons', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_color_button_bg_start',
					'type'    => 'color',
					'label'   => __( 'Button gradient start', 'ogc-newfinity' ),
					'default' => '#FFD27A',
				),
				array(
					'key'     => 'ogc_color_button_bg_end',
					'type'    => 'color',
					'label'   => __( 'Button gradient end', 'ogc-newfinity' ),
					'default' => '#FFEAC2',
				),
				array(
					'key'     => 'ogc_color_button_text',
					'type'    => 'color',
					'label'   => __( 'Button label colour', 'ogc-newfinity' ),
					'default' => '#FFFFFF',
				),
				array(
					'key'     => 'ogc_gradient_angle',
					'type'    => 'number',
					'label'   => __( 'Gradient angle', 'ogc-newfinity' ),
					'tooltip' => __( 'Angle used by accent and button gradients.', 'ogc-newfinity' ),
					'default' => 145,
					'attrs'   => array( 'min' => 0, 'max' => 360, 'step' => 1, 'unit' => 'deg' ),
				),
				array(
					'key'   => 'ogc_shadow_heading',
					'type'  => 'heading',
					'label' => __( 'Shadows', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_color_shadow',
					'type'    => 'color',
					'label'   => __( 'Shadow colour', 'ogc-newfinity' ),
					'default' => '#000000',
				),
				array(
					'key'     => 'ogc_shadow_opacity',
					'type'    => 'range',
					'label'   => __( 'Shadow opacity', 'ogc-newfinity' ),
					'default' => 40,
					'attrs'   => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
				),
			),
		),

		/* -------------------------------------------------------------- Fonts */
		'fonts'    => array(
			'label'       => __( 'Fonts', 'ogc-newfinity' ),
			'icon'        => 'dashicons-editor-textcolor',
			'description' => __( 'Only the selected pair is requested from Google Fonts.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'      => 'ogc_font_heading',
					'type'     => 'select',
					'label'    => __( 'Heading font', 'ogc-newfinity' ),
					'default'  => OGC_DEFAULT_FONT_HEADING,
					'options'  => ogc_heading_font_choices(),
					'sanitize' => 'ogc_sanitize_font_heading',
				),
				array(
					'key'      => 'ogc_font_body',
					'type'     => 'select',
					'label'    => __( 'Body font', 'ogc-newfinity' ),
					'default'  => OGC_DEFAULT_FONT_BODY,
					'options'  => ogc_body_font_choices(),
					'sanitize' => 'ogc_sanitize_font_body',
				),
				array(
					'key'     => 'ogc_heading_weight',
					'type'    => 'select',
					'label'   => __( 'Heading weight', 'ogc-newfinity' ),
					'default' => '700',
					'options' => array(
						'500' => __( '500 — Medium', 'ogc-newfinity' ),
						'600' => __( '600 — Semibold', 'ogc-newfinity' ),
						'700' => __( '700 — Bold', 'ogc-newfinity' ),
						'800' => __( '800 — Extrabold', 'ogc-newfinity' ),
					),
				),
				array(
					'key'     => 'ogc_body_weight',
					'type'    => 'select',
					'label'   => __( 'Body weight', 'ogc-newfinity' ),
					'default' => '400',
					'options' => array(
						'400' => __( '400 — Regular', 'ogc-newfinity' ),
						'500' => __( '500 — Medium', 'ogc-newfinity' ),
						'600' => __( '600 — Semibold', 'ogc-newfinity' ),
					),
				),
				array(
					'key'     => 'ogc_font_base_size',
					'type'    => 'number',
					'label'   => __( 'Base font size', 'ogc-newfinity' ),
					'default' => 16,
					'attrs'   => array( 'min' => 12, 'max' => 24, 'step' => 1, 'unit' => 'px' ),
				),
				array(
					'key'     => 'ogc_line_height_body',
					'type'    => 'number',
					'label'   => __( 'Body line height', 'ogc-newfinity' ),
					'default' => 1.7,
					'attrs'   => array( 'min' => 1.1, 'max' => 2.2, 'step' => 0.05 ),
				),
				array(
					'key'     => 'ogc_letter_spacing_heading',
					'type'    => 'number',
					'label'   => __( 'Heading letter spacing', 'ogc-newfinity' ),
					'tooltip' => __( 'Measured in em. Negative values tighten headings.', 'ogc-newfinity' ),
					'default' => -0.02,
					'attrs'   => array( 'min' => -0.1, 'max' => 0.2, 'step' => 0.01, 'unit' => 'em' ),
				),
			),
		),

		/* ------------------------------------------------------------ Buttons */
		'buttons'  => array(
			'label'       => __( 'Buttons & Motion', 'ogc-newfinity' ),
			'icon'        => 'dashicons-button',
			'description' => __( 'Shape, hover behaviour, and global motion switches.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_button_radius',
					'type'    => 'number',
					'label'   => __( 'Corner radius', 'ogc-newfinity' ),
					'default' => 12,
					'attrs'   => array( 'min' => 0, 'max' => 999, 'step' => 1, 'unit' => 'px' ),
				),
				array(
					'key'     => 'ogc_button_shadow_style',
					'type'    => 'select',
					'label'   => __( 'Shadow style', 'ogc-newfinity' ),
					'default' => 'soft',
					'options' => array(
						'none' => __( 'None', 'ogc-newfinity' ),
						'soft' => __( 'Soft', 'ogc-newfinity' ),
						'glow' => __( 'Glow', 'ogc-newfinity' ),
						'hard' => __( 'Hard', 'ogc-newfinity' ),
					),
				),
				array(
					'key'   => 'ogc_button_primary_heading',
					'type'  => 'heading',
					'label' => __( 'Primary button hover', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_button_hover_effect_primary',
					'type'    => 'select',
					'label'   => __( 'Hover effect', 'ogc-newfinity' ),
					'default' => 'lift',
					'options' => ogc_button_hover_effect_choices(),
				),
				array(
					'key'     => 'ogc_button_hover_duration_primary',
					'type'    => 'select',
					'label'   => __( 'Hover duration', 'ogc-newfinity' ),
					'default' => 'normal',
					'options' => ogc_speed_choices(),
				),
				array(
					'key'     => 'ogc_button_hover_intensity_primary',
					'type'    => 'select',
					'label'   => __( 'Hover intensity', 'ogc-newfinity' ),
					'default' => 'medium',
					'options' => ogc_intensity_choices(),
				),
				array(
					'key'   => 'ogc_button_outline_heading',
					'type'  => 'heading',
					'label' => __( 'Outline button hover', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_button_hover_effect_outline',
					'type'    => 'select',
					'label'   => __( 'Hover effect', 'ogc-newfinity' ),
					'default' => 'lift',
					'options' => ogc_button_hover_effect_choices(),
				),
				array(
					'key'     => 'ogc_button_hover_duration_outline',
					'type'    => 'select',
					'label'   => __( 'Hover duration', 'ogc-newfinity' ),
					'default' => 'normal',
					'options' => ogc_speed_choices(),
				),
				array(
					'key'     => 'ogc_button_hover_intensity_outline',
					'type'    => 'select',
					'label'   => __( 'Hover intensity', 'ogc-newfinity' ),
					'default' => 'medium',
					'options' => ogc_intensity_choices(),
				),
				array(
					'key'   => 'ogc_button_states_heading',
					'type'  => 'heading',
					'label' => __( 'States', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_button_active_state',
					'type'    => 'select',
					'label'   => __( 'Active (pressed) state', 'ogc-newfinity' ),
					'default' => 'scale-down',
					'options' => array(
						'scale-down' => __( 'Scale down', 'ogc-newfinity' ),
						'darken'     => __( 'Darken', 'ogc-newfinity' ),
						'none'       => __( 'None', 'ogc-newfinity' ),
					),
				),
				array(
					'key'     => 'ogc_button_disabled_style',
					'type'    => 'select',
					'label'   => __( 'Disabled style', 'ogc-newfinity' ),
					'default' => 'dimmed',
					'options' => array(
						'dimmed'    => __( 'Dimmed', 'ogc-newfinity' ),
						'grayscale' => __( 'Grayscale', 'ogc-newfinity' ),
					),
				),
				array(
					'key'     => 'ogc_button_focus_style',
					'type'    => 'select',
					'label'   => __( 'Focus style', 'ogc-newfinity' ),
					'tooltip' => __( 'Keyboard focus indicator. Never fully removed, for accessibility.', 'ogc-newfinity' ),
					'default' => 'outline',
					'options' => array(
						'outline'   => __( 'Outline', 'ogc-newfinity' ),
						'glow-ring' => __( 'Glow ring', 'ogc-newfinity' ),
					),
				),
				array(
					'key'   => 'ogc_motion_heading',
					'type'  => 'heading',
					'label' => __( 'Motion', 'ogc-newfinity' ),
				),
				array(
					'key'     => 'ogc_transition_speed',
					'type'    => 'select',
					'label'   => __( 'Global transition speed', 'ogc-newfinity' ),
					'tooltip' => __( 'Fast 150ms, Normal 250ms, Slow 400ms.', 'ogc-newfinity' ),
					'default' => 'normal',
					'options' => ogc_speed_choices(),
				),
				array(
					'key'     => 'ogc_toggle_orbit_animation',
					'type'    => 'checkbox',
					'label'   => __( 'Enable orbit / decorative motion', 'ogc-newfinity' ),
					'tooltip' => __( 'When off, hero animations freeze using the same path as prefers-reduced-motion.', 'ogc-newfinity' ),
					'default' => true,
				),
				array(
					'key'      => 'ogc_underline_swoosh_style',
					'type'     => 'select',
					'label'    => __( 'H1 underline style', 'ogc-newfinity' ),
					'default'  => 'gradient',
					'options'  => ogc_swoosh_style_choices(),
					'sanitize' => 'ogc_sanitize_swoosh_style',
				),
			),
		),

		/* ------------------------------------------------------------- Images */
		'images'   => array(
			'label'       => __( 'Images', 'ogc-newfinity' ),
			'icon'        => 'dashicons-format-image',
			'description' => __( 'Site Icon (favicon) lives under Appearance → Customize → Site Identity.', 'ogc-newfinity' ),
			'fields'      => array_merge(
				array(
					array(
						'key'     => 'ogc_logo_light',
						'type'    => 'image',
						'label'   => __( 'Logo (light contexts)', 'ogc-newfinity' ),
						'tooltip' => __( 'Optional light-background logo variant.', 'ogc-newfinity' ),
					),
					array(
						'key'   => 'ogc_hero_image_heading',
						'type'  => 'heading',
						'label' => __( 'Hero backgrounds', 'ogc-newfinity' ),
					),
				),
				$hero_image_fields
			),
		),

		/* -------------------------------------------------------------- Pages */
		'pages'    => array(
			'label'       => __( 'Pages', 'ogc-newfinity' ),
			'icon'        => 'dashicons-admin-page',
			'description' => __( 'Per-page hero treatment.', 'ogc-newfinity' ),
			'fields'      => $hero_opacity_fields,
		),

		/* -------------------------------------------------------------- Posts */
		'posts'    => array(
			'label'       => __( 'Posts', 'ogc-newfinity' ),
			'icon'        => 'dashicons-admin-post',
			'description' => __( 'Blog archive card composition.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_blog_card_show_image',
					'type'    => 'checkbox',
					'label'   => __( 'Show featured image on cards', 'ogc-newfinity' ),
					'default' => true,
				),
				array(
					'key'     => 'ogc_blog_card_show_date',
					'type'    => 'checkbox',
					'label'   => __( 'Show publish date on cards', 'ogc-newfinity' ),
					'default' => true,
				),
				array(
					'key'     => 'ogc_blog_card_show_category',
					'type'    => 'checkbox',
					'label'   => __( 'Show category on cards', 'ogc-newfinity' ),
					'default' => true,
				),
				array(
					'key'     => 'ogc_blog_excerpt_length',
					'type'    => 'number',
					'label'   => __( 'Excerpt length', 'ogc-newfinity' ),
					'tooltip' => __( 'Words shown in the card excerpt.', 'ogc-newfinity' ),
					'default' => 22,
					'attrs'   => array( 'min' => 5, 'max' => 80, 'step' => 1, 'unit' => __( 'words', 'ogc-newfinity' ) ),
				),
			),
		),

		/* ---------------------------------------------------------------- Ads */
		'ads'      => array(
			'label'       => __( 'Ads', 'ogc-newfinity' ),
			'icon'        => 'dashicons-megaphone',
			'description' => __( 'Optional promotional slots rendered by the ad-slot template part.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_ads_enabled',
					'type'    => 'checkbox',
					'label'   => __( 'Enable ad slots', 'ogc-newfinity' ),
					'tooltip' => __( 'Master switch. Individual slots also need their own toggle.', 'ogc-newfinity' ),
					'default' => false,
				),
				array(
					'key'     => 'ogc_ads_config_json',
					'type'    => 'ads_config',
					'label'   => __( 'Ad slots', 'ogc-newfinity' ),
					'default' => ogc_default_ads_config_json(),
				),
			),
		),

		/* ----------------------------------------------------------- Advanced */
		'advanced' => array(
			'label'       => __( 'Advanced', 'ogc-newfinity' ),
			'icon'        => 'dashicons-editor-code',
			'description' => __( 'Custom code, plus import, export, and reset tools.', 'ogc-newfinity' ),
			'fields'      => array(
				array(
					'key'     => 'ogc_custom_css',
					'type'    => 'monospace',
					'label'   => __( 'Custom CSS', 'ogc-newfinity' ),
					'tooltip' => __( 'Printed in wp_head after every theme stylesheet.', 'ogc-newfinity' ),
					'attrs'   => array( 'rows' => 12, 'placeholder' => ".ogc-btn { letter-spacing: 0.02em; }\n" ),
				),
				array(
					'key'     => 'ogc_custom_js',
					'type'    => 'monospace',
					'label'   => __( 'Custom JavaScript', 'ogc-newfinity' ),
					'tooltip' => __( 'Printed in wp_footer without <script> tags — add statements only.', 'ogc-newfinity' ),
					'attrs'   => array( 'rows' => 12, 'placeholder' => "console.log('hello');\n" ),
				),
			),
		),
	);

	return $schema;
}

/**
 * Default sanitize callback name for a field type.
 *
 * @param string $type Field type.
 * @return string Callable name, or empty for non-saving types.
 */
function ogc_default_sanitizer_for_type( $type ) {
	$map = array(
		'color'          => 'ogc_sanitize_hex_color',
		'text'           => 'sanitize_text_field',
		'email'          => 'sanitize_email',
		'url'            => 'esc_url_raw',
		'textarea'       => 'sanitize_textarea_field',
		'monospace'      => 'ogc_sanitize_code',
		'number'         => 'ogc_sanitize_number',
		'range'          => 'ogc_sanitize_int',
		'checkbox'       => 'ogc_sanitize_checkbox',
		'select'         => 'sanitize_text_field',
		'image'          => 'ogc_sanitize_image_url',
		'nav_order'      => 'ogc_sanitize_nav_order_json',
		'footer_columns' => 'ogc_sanitize_footer_columns_json',
		'ads_config'     => 'ogc_sanitize_ads_config_json',
	);

	return isset( $map[ $type ] ) ? $map[ $type ] : '';
}

/**
 * Flat, normalized map of every savable field: key => field definition.
 *
 * @return array<string, array<string, mixed>>
 */
function ogc_theme_options_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}

	$fields = array();
	foreach ( ogc_theme_options_schema() as $tab_id => $tab ) {
		foreach ( $tab['fields'] as $field ) {
			if ( 'heading' === $field['type'] ) {
				continue;
			}
			$field = wp_parse_args(
				$field,
				array(
					'tooltip' => '',
					'default' => '',
					'options' => array(),
					'attrs'   => array(),
				)
			);
			if ( empty( $field['sanitize'] ) ) {
				$field['sanitize'] = ogc_default_sanitizer_for_type( $field['type'] );
			}
			$field['tab']              = $tab_id;
			$fields[ $field['key'] ] = $field;
		}
	}

	return $fields;
}

/**
 * A single normalized field definition.
 *
 * @param string $key Option key.
 * @return array<string, mixed>|null
 */
function ogc_theme_options_field( $key ) {
	$fields = ogc_theme_options_fields();
	return isset( $fields[ $key ] ) ? $fields[ $key ] : null;
}

/**
 * Schema default for an option key.
 *
 * @param string $key Option key.
 * @return mixed
 */
function ogc_option_default( $key ) {
	$field = ogc_theme_options_field( $key );
	return $field ? $field['default'] : '';
}

/**
 * Sanitize a raw value against its field definition.
 *
 * @param array<string, mixed> $field Field definition.
 * @param mixed                $value Raw value.
 * @return mixed
 */
function ogc_sanitize_option_value( $field, $value ) {
	if ( ! empty( $field['sanitize'] ) && is_callable( $field['sanitize'] ) ) {
		$value = call_user_func( $field['sanitize'], $value );
	}

	if ( null === $value ) {
		return $field['default'];
	}

	switch ( $field['type'] ) {
		case 'color':
			if ( '' === (string) $value ) {
				$value = $field['default'];
			}
			break;

		case 'select':
			if ( ! empty( $field['options'] ) && ! array_key_exists( (string) $value, $field['options'] ) ) {
				$value = $field['default'];
			}
			break;

		case 'number':
		case 'range':
			$attrs = $field['attrs'];
			if ( isset( $attrs['min'] ) ) {
				$value = max( $attrs['min'], $value );
			}
			if ( isset( $attrs['max'] ) ) {
				$value = min( $attrs['max'], $value );
			}
			break;
	}

	return $value;
}

/**
 * Read a theme option: stored theme_mod, sanitized, with the schema default as fallback.
 *
 * @param string $key Option key.
 * @return mixed
 */
function ogc_mod( $key ) {
	$field = ogc_theme_options_field( $key );
	if ( ! $field ) {
		return get_theme_mod( $key );
	}

	return ogc_sanitize_option_value( $field, get_theme_mod( $key, $field['default'] ) );
}

/* =========================================================================
 * 4. Sanitizers
 * ====================================================================== */

/**
 * Sanitize a value against an allow-list; fall back to $fallback.
 *
 * @param string               $value    Submitted value.
 * @param array<string, mixed> $allowed  Allowed keys.
 * @param string               $fallback Fallback.
 * @return string
 */
function ogc_sanitize_choice( $value, $allowed, $fallback ) {
	$value = is_string( $value ) ? $value : '';
	return array_key_exists( $value, $allowed ) ? $value : $fallback;
}

/**
 * Sanitize heading font select.
 *
 * @param string $value Submitted value.
 * @return string
 */
function ogc_sanitize_font_heading( $value ) {
	return ogc_sanitize_choice( $value, ogc_heading_font_choices(), OGC_DEFAULT_FONT_HEADING );
}

/**
 * Sanitize body font select.
 *
 * @param string $value Submitted value.
 * @return string
 */
function ogc_sanitize_font_body( $value ) {
	return ogc_sanitize_choice( $value, ogc_body_font_choices(), OGC_DEFAULT_FONT_BODY );
}

/**
 * Sanitize swoosh style select.
 *
 * @param string $value Submitted value.
 * @return string
 */
function ogc_sanitize_swoosh_style( $value ) {
	return ogc_sanitize_choice( $value, ogc_swoosh_style_choices(), 'gradient' );
}

/**
 * Sanitize checkbox / switch input.
 *
 * @param mixed $value Submitted value.
 * @return bool
 */
function ogc_sanitize_checkbox( $value ) {
	if ( is_string( $value ) ) {
		return in_array( strtolower( $value ), array( '1', 'on', 'true', 'yes' ), true );
	}
	return (bool) $value;
}

/**
 * Sanitize an image URL.
 *
 * @param string $value Submitted URL.
 * @return string
 */
function ogc_sanitize_image_url( $value ) {
	return esc_url_raw( trim( (string) $value ) );
}

/**
 * Sanitize a hex colour; empty string when invalid so callers can fall back.
 *
 * @param string $value Submitted colour.
 * @return string
 */
function ogc_sanitize_hex_color( $value ) {
	$color = sanitize_hex_color( trim( (string) $value ) );
	return $color ? $color : '';
}

/**
 * Sanitize a float-ish number.
 *
 * @param mixed $value Submitted value.
 * @return float
 */
function ogc_sanitize_number( $value ) {
	return (float) str_replace( ',', '.', (string) $value );
}

/**
 * Sanitize an integer.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function ogc_sanitize_int( $value ) {
	return (int) $value;
}

/**
 * Sanitize an analytics / pixel identifier.
 *
 * @param string $value Submitted value.
 * @return string
 */
function ogc_sanitize_tracking_id( $value ) {
	$value = trim( (string) $value );
	return preg_replace( '/[^A-Za-z0-9\-_]/', '', $value );
}

/**
 * Sanitize custom CSS / JS.
 *
 * Tags are stripped so a stray closing tag cannot break out of the printed
 * <style> or <script> block; the code itself is preserved verbatim.
 *
 * @param string $value Submitted code.
 * @return string
 */
function ogc_sanitize_code( $value ) {
	return trim( wp_strip_all_tags( (string) $value ) );
}

/**
 * Sanitize third-party ad markup.
 *
 * Administrators with `unfiltered_html` keep raw markup so ad-network snippets
 * (which rely on inline JS) survive intact. Everyone else gets a kses pass that
 * allows embed containers but drops event-handler attributes.
 *
 * @param string $value Submitted markup.
 * @return string
 */
function ogc_sanitize_ad_code( $value ) {
	$value = (string) $value;

	if ( current_user_can( 'unfiltered_html' ) ) {
		return trim( $value );
	}

	$allowed            = wp_kses_allowed_html( 'post' );
	$allowed['script']  = array(
		'src'         => true,
		'async'       => true,
		'defer'       => true,
		'type'        => true,
		'crossorigin' => true,
		'data-*'      => true,
	);
	$allowed['ins']     = array(
		'class'  => true,
		'style'  => true,
		'data-*' => true,
	);
	$allowed['iframe']  = array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'frameborder'     => true,
		'scrolling'       => true,
		'allow'           => true,
		'allowfullscreen' => true,
		'loading'         => true,
		'title'           => true,
		'style'           => true,
	);

	return trim( wp_kses( $value, $allowed ) );
}

/**
 * Decode a JSON string into a list array.
 *
 * @param mixed $value Raw JSON string or array.
 * @return array<int, mixed>
 */
function ogc_decode_json_list( $value ) {
	if ( is_array( $value ) ) {
		return array_values( $value );
	}

	$decoded = json_decode( (string) $value, true );
	return is_array( $decoded ) ? array_values( $decoded ) : array();
}

/**
 * Sanitize the header nav order JSON.
 *
 * @param mixed $value Raw JSON.
 * @return string Normalized JSON.
 */
function ogc_sanitize_nav_order_json( $value ) {
	$map  = ogc_nav_slug_map();
	$rows = ogc_decode_json_list( $value );
	$out  = array();
	$seen = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['slug'] ) ) {
			continue;
		}
		$slug = sanitize_key( $row['slug'] );
		if ( ! isset( $map[ $slug ] ) || isset( $seen[ $slug ] ) ) {
			continue;
		}
		$seen[ $slug ] = true;
		$out[]         = array(
			'slug'    => $slug,
			'visible' => ! isset( $row['visible'] ) || ogc_sanitize_checkbox( $row['visible'] ),
		);
	}

	// Any slug the stored value does not know about is appended, visible.
	foreach ( array_keys( $map ) as $slug ) {
		if ( ! isset( $seen[ $slug ] ) ) {
			$out[] = array(
				'slug'    => $slug,
				'visible' => true,
			);
		}
	}

	return wp_json_encode( $out );
}

/**
 * Sanitize the footer columns JSON.
 *
 * @param mixed $value Raw JSON.
 * @return string Normalized JSON.
 */
function ogc_sanitize_footer_columns_json( $value ) {
	$columns = ogc_decode_json_list( $value );
	$out     = array();

	foreach ( array_slice( $columns, 0, 6 ) as $column ) {
		if ( ! is_array( $column ) ) {
			continue;
		}

		$links = array();
		$raw   = isset( $column['links'] ) && is_array( $column['links'] ) ? $column['links'] : array();
		foreach ( array_slice( $raw, 0, 20 ) as $link ) {
			if ( ! is_array( $link ) ) {
				continue;
			}
			$label = sanitize_text_field( isset( $link['label'] ) ? $link['label'] : '' );
			$url   = ogc_sanitize_relative_or_absolute_url( isset( $link['url'] ) ? $link['url'] : '' );
			if ( '' === $label && '' === $url ) {
				continue;
			}
			$links[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}

		$out[] = array(
			'title' => sanitize_text_field( isset( $column['title'] ) ? $column['title'] : '' ),
			'links' => $links,
		);
	}

	return wp_json_encode( $out );
}

/**
 * Sanitize a URL that may be site-relative (e.g. /services/).
 *
 * @param string $value Submitted URL.
 * @return string
 */
function ogc_sanitize_relative_or_absolute_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( 0 === strpos( $value, '/' ) || 0 === strpos( $value, '#' ) ) {
		return esc_url_raw( $value, array( 'http', 'https' ) );
	}

	return esc_url_raw( $value );
}

/**
 * Sanitize the ad slots JSON.
 *
 * @param mixed $value Raw JSON.
 * @return string Normalized JSON.
 */
function ogc_sanitize_ads_config_json( $value ) {
	$rows = ogc_decode_json_list( $value );
	$out  = array();
	$seen = array();

	foreach ( array_slice( $rows, 0, 10 ) as $row ) {
		if ( ! is_array( $row ) || empty( $row['key'] ) ) {
			continue;
		}
		$key = sanitize_key( $row['key'] );
		if ( '' === $key || isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;

		$type = isset( $row['type'] ) && 'code' === $row['type'] ? 'code' : 'image';

		$out[] = array(
			'key'     => $key,
			'enabled' => ! empty( $row['enabled'] ) && ogc_sanitize_checkbox( $row['enabled'] ),
			'type'    => $type,
			'image'   => ogc_sanitize_image_url( isset( $row['image'] ) ? $row['image'] : '' ),
			'url'     => ogc_sanitize_relative_or_absolute_url( isset( $row['url'] ) ? $row['url'] : '' ),
			'code'    => ogc_sanitize_ad_code( isset( $row['code'] ) ? $row['code'] : '' ),
		);
	}

	return wp_json_encode( $out );
}

/* =========================================================================
 * 5. Public getters
 * ====================================================================== */

/**
 * Contact email from Theme Options.
 *
 * @return string
 */
function ogc_get_contact_email() {
	$email = sanitize_email( (string) ogc_mod( 'ogc_contact_email' ) );
	return $email ? $email : OGC_DEFAULT_CONTACT_EMAIL;
}

/**
 * Location text from Theme Options.
 *
 * @return string
 */
function ogc_get_location_text() {
	$text = (string) ogc_mod( 'ogc_location_text' );
	return '' !== $text ? $text : OGC_DEFAULT_LOCATION_TEXT;
}

/**
 * Footer tagline from Theme Options.
 *
 * @return string
 */
function ogc_get_footer_tagline() {
	$text = (string) ogc_mod( 'ogc_footer_tagline' );
	return '' !== $text ? $text : OGC_DEFAULT_FOOTER_TAGLINE;
}

/**
 * Response-time guarantee label from Theme Options.
 *
 * @return string
 */
function ogc_get_response_time_text() {
	$text = (string) ogc_mod( 'ogc_response_time_text' );
	return '' !== $text ? $text : OGC_DEFAULT_RESPONSE_TIME;
}

/**
 * Social profile URL for a network key (x, linkedin, github, discord).
 *
 * @param string $network Network slug.
 * @return string Empty string when cleared (caller should hide the icon).
 */
function ogc_get_social_url( $network ) {
	$network = sanitize_key( $network );
	$key     = "ogc_social_{$network}";

	if ( ! ogc_theme_options_field( $key ) ) {
		return '';
	}

	$url = (string) ogc_mod( $key );
	return $url ? esc_url( $url ) : '';
}

/**
 * Light-logo image URL (optional variant beyond the primary custom logo).
 *
 * @return string
 */
function ogc_get_logo_light_url() {
	return esc_url( (string) ogc_mod( 'ogc_logo_light' ) );
}

/**
 * URL of the WordPress Site Identity logo, if one is set.
 *
 * @return string
 */
function ogc_get_custom_logo_url() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		return '';
	}

	$src = wp_get_attachment_image_url( $logo_id, 'full' );
	return $src ? esc_url( $src ) : '';
}

/**
 * Header logo URL: theme option, then Site Identity, then the bundled logo.
 *
 * @return string
 */
function ogc_get_header_logo_url() {
	$candidates = array(
		(string) ogc_mod( 'ogc_header_logo' ),
		ogc_get_custom_logo_url(),
		ogc_get_logo_light_url(),
	);

	foreach ( $candidates as $url ) {
		if ( $url ) {
			return esc_url( $url );
		}
	}

	return esc_url( ogc_get_logo_uri() );
}

/**
 * Footer logo URL: theme option, then the header logo chain.
 *
 * @return string
 */
function ogc_get_footer_logo_url() {
	$url = (string) ogc_mod( 'ogc_footer_logo' );
	return $url ? esc_url( $url ) : ogc_get_header_logo_url();
}

/**
 * Header navigation order, enriched with labels and absolute URLs.
 *
 * @param bool $visible_only Whether to drop hidden items. Default true.
 * @return array<int, array{slug:string, label:string, url:string, visible:bool}>
 */
function ogc_get_nav_order( $visible_only = true ) {
	$map  = ogc_nav_slug_map();
	$rows = ogc_decode_json_list( ogc_mod( 'ogc_header_nav_order' ) );
	$out  = array();

	foreach ( $rows as $row ) {
		$slug = isset( $row['slug'] ) ? sanitize_key( $row['slug'] ) : '';
		if ( ! isset( $map[ $slug ] ) ) {
			continue;
		}
		$visible = ! empty( $row['visible'] );
		if ( $visible_only && ! $visible ) {
			continue;
		}
		$out[] = array(
			'slug'    => $slug,
			'label'   => $map[ $slug ]['label'],
			'url'     => home_url( $map[ $slug ]['path'] ),
			'visible' => $visible,
		);
	}

	return $out;
}

/**
 * Footer link columns, decoded and resolved to absolute URLs.
 *
 * @return array<int, array{title:string, links:array<int, array{label:string, url:string}>}>
 */
function ogc_get_footer_columns() {
	$columns = ogc_decode_json_list( ogc_mod( 'ogc_footer_columns_json' ) );
	$out     = array();

	foreach ( $columns as $column ) {
		if ( ! is_array( $column ) ) {
			continue;
		}

		$links = array();
		$raw   = isset( $column['links'] ) && is_array( $column['links'] ) ? $column['links'] : array();
		foreach ( $raw as $link ) {
			$label = isset( $link['label'] ) ? (string) $link['label'] : '';
			$url   = isset( $link['url'] ) ? (string) $link['url'] : '';
			if ( '' === $label ) {
				continue;
			}
			$links[] = array(
				'label' => $label,
				'url'   => ogc_resolve_url( $url ),
			);
		}

		$out[] = array(
			'title' => isset( $column['title'] ) ? (string) $column['title'] : '',
			'links' => $links,
		);
	}

	return $out;
}

/**
 * Turn a stored (possibly relative) URL into an absolute one.
 *
 * @param string $url Stored URL.
 * @return string
 */
function ogc_resolve_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	if ( 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}

	return $url;
}

/**
 * Footer copyright line with {year} / {start_year} placeholders resolved.
 *
 * @return string
 */
function ogc_get_footer_copyright() {
	$text = (string) ogc_mod( 'ogc_footer_copyright_text' );
	if ( '' === $text ) {
		$text = ogc_option_default( 'ogc_footer_copyright_text' );
	}

	return strtr(
		$text,
		array(
			'{year}'       => wp_date( 'Y' ),
			'{start_year}' => '2022',
		)
	);
}

/**
 * Ad slot configuration keyed by slot key.
 *
 * @return array<string, array{key:string, enabled:bool, type:string, image:string, url:string, code:string}>
 */
function ogc_get_ads_config() {
	$rows = ogc_decode_json_list( ogc_mod( 'ogc_ads_config_json' ) );
	$out  = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['key'] ) ) {
			continue;
		}
		$key         = sanitize_key( $row['key'] );
		$out[ $key ] = array(
			'key'     => $key,
			'enabled' => ! empty( $row['enabled'] ),
			'type'    => isset( $row['type'] ) && 'code' === $row['type'] ? 'code' : 'image',
			'image'   => isset( $row['image'] ) ? (string) $row['image'] : '',
			'url'     => isset( $row['url'] ) ? ogc_resolve_url( $row['url'] ) : '',
			'code'    => isset( $row['code'] ) ? (string) $row['code'] : '',
		);
	}

	return $out;
}

/**
 * A single ad slot, or null when the slot is missing or switched off.
 *
 * @param string $slot_key Slot key (e.g. sidebar, inline).
 * @return array<string, mixed>|null
 */
function ogc_get_ad_slot( $slot_key ) {
	if ( ! ogc_mod( 'ogc_ads_enabled' ) ) {
		return null;
	}

	$slots    = ogc_get_ads_config();
	$slot_key = sanitize_key( $slot_key );

	if ( empty( $slots[ $slot_key ] ) || ! $slots[ $slot_key ]['enabled'] ) {
		return null;
	}

	return $slots[ $slot_key ];
}

/**
 * Optional hero background image URL for a key page.
 *
 * @param string $page_key One of: home, about, services, mission.
 * @return string
 */
function ogc_get_hero_bg_url( $page_key ) {
	$page_key = sanitize_key( $page_key );
	if ( ! in_array( $page_key, array( 'home', 'about', 'services', 'mission' ), true ) ) {
		return '';
	}

	return esc_url( (string) ogc_mod( "ogc_hero_bg_{$page_key}" ) );
}

/**
 * Hero background opacity (0–1) for a key page.
 *
 * @param string $page_key One of: home, about, services, mission.
 * @return float
 */
function ogc_get_hero_bg_opacity( $page_key ) {
	$page_key = sanitize_key( $page_key );
	$field    = "ogc_hero_bg_opacity_{$page_key}";

	if ( ! ogc_theme_options_field( $field ) ) {
		return 0.35;
	}

	return ( (int) ogc_mod( $field ) ) / 100;
}

/**
 * Echo an optional additive hero background layer (behind existing canvas/content).
 *
 * @param string $page_key One of: home, about, services, mission.
 */
function ogc_render_hero_bg( $page_key ) {
	$url = ogc_get_hero_bg_url( $page_key );
	if ( ! $url ) {
		return;
	}

	printf(
		'<div class="ogc-hero-bg" style="background-image:url(%1$s);opacity:%2$s;" aria-hidden="true"></div>',
		esc_url( $url ),
		esc_attr( ogc_css_num( ogc_get_hero_bg_opacity( $page_key ) ) )
	);
}

/**
 * Build a Google Fonts CSS2 URL for the selected heading + body pair and weights.
 *
 * @return string Absolute Google Fonts URL, or empty if nothing to load.
 */
function ogc_get_google_fonts_url() {
	$library = ogc_font_library();

	$heading = ogc_sanitize_font_heading( ogc_mod( 'ogc_font_heading' ) );
	$body    = ogc_sanitize_font_body( ogc_mod( 'ogc_font_body' ) );

	$wanted = array(
		array( $heading, array( 500, (int) ogc_mod( 'ogc_heading_weight' ), 700 ) ),
		array( $body, array( 400, (int) ogc_mod( 'ogc_body_weight' ), 600, 700 ) ),
	);

	// One request per family — a shared heading/body font gets the union of both weight sets.
	$requests = array();
	foreach ( $wanted as $request ) {
		list( $name, $weights ) = $request;
		if ( ! isset( $library[ $name ] ) ) {
			continue;
		}
		$requests[ $name ] = isset( $requests[ $name ] )
			? array_merge( $requests[ $name ], $weights )
			: $weights;
	}

	$parts = array();
	foreach ( $requests as $name => $weights ) {
		$available = $library[ $name ]['weights'];
		$weights   = array_values( array_unique( array_intersect( array_map( 'intval', $weights ), $available ) ) );
		sort( $weights );

		$parts[] = $weights
			? 'family=' . $library[ $name ]['family'] . ':wght@' . implode( ';', $weights )
			: 'family=' . $library[ $name ]['family'];
	}

	if ( empty( $parts ) ) {
		return '';
	}

	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/* =========================================================================
 * 6. Front-end CSS output
 * ====================================================================== */

/**
 * Format a number for CSS output without trailing zeros.
 *
 * @param float|int|string $value Numeric value.
 * @return string
 */
function ogc_css_num( $value ) {
	$out = rtrim( rtrim( number_format( (float) $value, 4, '.', '' ), '0' ), '.' );
	return '' === $out || '-' === $out ? '0' : $out;
}

/**
 * Make a value safe to print inside a <style> block.
 *
 * Escaping functions cannot be used here: entities are not decoded inside
 * <style>, so esc_html() would corrupt quoted font stacks and `>` combinators.
 * Instead, strip the characters that could terminate the block or the rule.
 *
 * @param string $value Raw CSS value.
 * @return string
 */
function ogc_css_value_safe( $value ) {
	return trim( str_replace( array( '<', '>', '{', '}', ';', '\\' ), '', (string) $value ) );
}

/**
 * Convert a hex colour to a comma-separated "r, g, b" triplet.
 *
 * @param string $hex     Hex colour.
 * @param string $fallback Fallback hex when $hex is invalid.
 * @return string
 */
function ogc_hex_to_rgb( $hex, $fallback = '#000000' ) {
	$hex = ogc_sanitize_hex_color( $hex );
	if ( ! $hex ) {
		$hex = $fallback;
	}

	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return sprintf(
		'%d, %d, %d',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) )
	);
}

/**
 * Milliseconds for a fast/normal/slow speed token.
 *
 * @param string $speed Speed token.
 * @param bool   $global Whether this is the global transition scale.
 * @return int
 */
function ogc_speed_ms( $speed, $global = false ) {
	$scale = $global
		? array( 'fast' => 150, 'normal' => 250, 'slow' => 400 )
		: array( 'fast' => 120, 'normal' => 220, 'slow' => 380 );

	return isset( $scale[ $speed ] ) ? $scale[ $speed ] : $scale['normal'];
}

/**
 * Numeric hover values for a subtle/medium/strong intensity token.
 *
 * @param string $intensity Intensity token.
 * @return array{lift:string, scale:string, glow:string}
 */
function ogc_intensity_values( $intensity ) {
	$scale = array(
		'subtle' => array( 'lift' => '-2px', 'scale' => '1.01', 'glow' => '0.25' ),
		'medium' => array( 'lift' => '-4px', 'scale' => '1.03', 'glow' => '0.4' ),
		'strong' => array( 'lift' => '-7px', 'scale' => '1.06', 'glow' => '0.6' ),
	);

	return isset( $scale[ $intensity ] ) ? $scale[ $intensity ] : $scale['medium'];
}

/**
 * Build the button box-shadow value for the chosen shadow style.
 *
 * @param string $style     none|soft|glow|hard.
 * @param string $shadow_rgb Shadow colour as "r, g, b".
 * @param string $brand_rgb  Accent colour as "r, g, b".
 * @param float  $opacity    Shadow opacity 0–1.
 * @return string
 */
function ogc_button_shadow_value( $style, $shadow_rgb, $brand_rgb, $opacity ) {
	switch ( $style ) {
		case 'none':
			return 'none';
		case 'glow':
			return sprintf( '0 0 24px rgba(%s, %s)', $brand_rgb, ogc_css_num( min( 1, $opacity + 0.1 ) ) );
		case 'hard':
			return sprintf( '0 4px 0 rgba(%s, %s)', $shadow_rgb, ogc_css_num( $opacity ) );
		case 'soft':
		default:
			return sprintf( '0 8px 24px rgba(%s, %s)', $shadow_rgb, ogc_css_num( $opacity ) );
	}
}

/**
 * Print every theme-option CSS custom property in wp_head.
 *
 * Always outputs the full token set rather than a diff from the defaults —
 * predictable regardless of how theme_mod storage and defaults interact.
 */
function ogc_theme_options_css() {
	$accent_start   = ogc_mod( 'ogc_color_accent_start' );
	$accent_end     = ogc_mod( 'ogc_color_accent_end' );
	$bg             = ogc_mod( 'ogc_color_bg' );
	$text_primary   = ogc_mod( 'ogc_color_text_primary' );
	$text_secondary = ogc_mod( 'ogc_color_text_secondary' );
	$border         = ogc_mod( 'ogc_color_border' );

	$btn_start = ogc_mod( 'ogc_color_button_bg_start' );
	$btn_end   = ogc_mod( 'ogc_color_button_bg_end' );
	$btn_text  = ogc_mod( 'ogc_color_button_text' );

	$angle          = ogc_css_num( ogc_mod( 'ogc_gradient_angle' ) );
	$shadow_rgb     = ogc_hex_to_rgb( ogc_mod( 'ogc_color_shadow' ) );
	$shadow_opacity = ( (int) ogc_mod( 'ogc_shadow_opacity' ) ) / 100;

	$brand_rgb       = ogc_hex_to_rgb( $accent_start, OGC_DEFAULT_COLOR_ACCENT_START );
	$brand_hover_rgb = ogc_hex_to_rgb( $accent_end, OGC_DEFAULT_COLOR_ACCENT_END );

	$font_heading = ogc_sanitize_font_heading( ogc_mod( 'ogc_font_heading' ) );
	$font_body    = ogc_sanitize_font_body( ogc_mod( 'ogc_font_body' ) );
	$swoosh       = ogc_sanitize_swoosh_style( ogc_mod( 'ogc_underline_swoosh_style' ) );

	$header_rgb     = ogc_hex_to_rgb( ogc_mod( 'ogc_header_bg_color' ), OGC_DEFAULT_COLOR_BG );
	$header_opacity = ( (int) ogc_mod( 'ogc_header_bg_opacity' ) ) / 100;

	// Baselines only: button-effects.css scales these via the ogc-btn-*-intensity-* body classes.
	$hover_base = ogc_intensity_values( 'medium' );

	$transition_ms = ogc_speed_ms( ogc_mod( 'ogc_transition_speed' ), true );

	$tokens = array(
		/* Palette */
		'--accent-gradient-start'   => $accent_start,
		'--accent-gradient-end'     => $accent_end,
		'--bg-primary'              => $bg,
		'--text-primary'            => $text_primary,
		'--text-secondary'          => $text_secondary,
		'--border-accent'           => $border,
		'--ogc-text-primary'        => $text_primary,
		'--ogc-brand-rgb'           => $brand_rgb,
		'--ogc-brand-hover-rgb'     => $brand_hover_rgb,
		'--ogc-link-color'          => $accent_start,
		'--ogc-link-hover'          => $accent_end,
		'--ogc-btn-hover-bg'        => sprintf( 'rgba(%s, 0.16)', $brand_rgb ),
		'--ogc-gradient-angle'      => $angle . 'deg',
		'--ogc-accent-gradient'     => sprintf( 'linear-gradient(%sdeg, %s 0%%, %s 100%%)', $angle, $accent_start, $accent_end ),

		/* Typography */
		'--font-heading'            => sprintf( '"%s", system-ui, -apple-system, "Segoe UI", sans-serif', $font_heading ),
		'--font-body'               => sprintf( '"%s", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', $font_body ),
		'--ogc-font-size-base'      => ogc_css_num( ogc_mod( 'ogc_font_base_size' ) ) . 'px',
		'--ogc-heading-weight'      => (string) (int) ogc_mod( 'ogc_heading_weight' ),
		'--ogc-body-weight'         => (string) (int) ogc_mod( 'ogc_body_weight' ),
		'--ogc-line-height-body'    => ogc_css_num( ogc_mod( 'ogc_line_height_body' ) ),
		'--ogc-letter-spacing-heading' => ogc_css_num( ogc_mod( 'ogc_letter_spacing_heading' ) ) . 'em',
		'--ogc-swoosh-mode'         => $swoosh,

		/* Buttons */
		'--ogc-btn-bg-start'        => $btn_start,
		'--ogc-btn-bg-end'          => $btn_end,
		'--ogc-btn-gradient'        => sprintf( 'linear-gradient(%sdeg, %s 0%%, %s 100%%)', $angle, $btn_start, $btn_end ),
		'--ogc-btn-text'            => $btn_text,
		'--ogc-btn-radius'          => ogc_css_num( ogc_mod( 'ogc_button_radius' ) ) . 'px',
		'--ogc-button-radius'       => ogc_css_num( ogc_mod( 'ogc_button_radius' ) ) . 'px',
		'--ogc-focus-ring'          => $accent_end,
		'--ogc-button-radius'       => ogc_css_num( ogc_mod( 'ogc_button_radius' ) ) . 'px',
		'--ogc-btn-shadow'          => ogc_button_shadow_value( ogc_mod( 'ogc_button_shadow_style' ), $shadow_rgb, $brand_rgb, $shadow_opacity ),
		'--ogc-shadow-rgb'          => $shadow_rgb,
		'--ogc-shadow-opacity'      => ogc_css_num( $shadow_opacity ),
		'--ogc-shadow-color'        => sprintf( 'rgba(%s, %s)', $shadow_rgb, ogc_css_num( $shadow_opacity ) ),

		/* Hover tuning */
		'--ogc-hover-duration-primary' => ogc_speed_ms( ogc_mod( 'ogc_button_hover_duration_primary' ) ) . 'ms',
		'--ogc-hover-duration-outline' => ogc_speed_ms( ogc_mod( 'ogc_button_hover_duration_outline' ) ) . 'ms',
		'--ogc-btn-duration-primary'   => ogc_speed_ms( ogc_mod( 'ogc_button_hover_duration_primary' ) ) . 'ms',
		'--ogc-btn-duration-outline'   => ogc_speed_ms( ogc_mod( 'ogc_button_hover_duration_outline' ) ) . 'ms',
		'--ogc-hover-lift-primary'     => $primary['lift'],
		'--ogc-hover-scale-primary'    => $primary['scale'],
		'--ogc-hover-glow-primary'     => $primary['glow'],
		'--ogc-hover-lift-outline'     => $outline['lift'],
		'--ogc-hover-scale-outline'    => $outline['scale'],
		'--ogc-hover-glow-outline'     => $outline['glow'],

		/* Motion */
		'--ogc-transition-duration' => $transition_ms . 'ms',
		'--ogc-transition-speed'    => $transition_ms . 'ms',
		'--ogc-transition'          => $transition_ms . 'ms ease',

		/* Header */
		'--ogc-header-bg'           => sprintf( 'rgba(%s, %s)', $header_rgb, ogc_css_num( $header_opacity ) ),
		'--ogc-header-text-color'   => ogc_mod( 'ogc_header_text_color' ),
		'--ogc-header-logo-width'   => ogc_css_num( ogc_mod( 'ogc_header_logo_width_desktop' ) ) . 'px',
		'--ogc-header-logo-width-desktop' => ogc_css_num( ogc_mod( 'ogc_header_logo_width_desktop' ) ) . 'px',
		'--ogc-header-logo-width-mobile' => ogc_css_num( ogc_mod( 'ogc_header_logo_width_mobile' ) ) . 'px',

		/* Footer */
		'--ogc-footer-bg'           => ogc_mod( 'ogc_footer_bg_color' ),
		'--ogc-footer-logo-width'   => ogc_css_num( ogc_mod( 'ogc_footer_logo_width' ) ) . 'px',
	);

	foreach ( array( 'home', 'about', 'services', 'mission' ) as $page_key ) {
		$tokens[ '--ogc-hero-bg-opacity-' . $page_key ] = ogc_css_num( ogc_get_hero_bg_opacity( $page_key ) );
	}

	$lines = '';
	foreach ( $tokens as $name => $value ) {
		$lines .= "\t" . ogc_css_value_safe( $name ) . ': ' . ogc_css_value_safe( $value ) . ";\n";
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- values passed through ogc_css_value_safe().
	echo "<style id=\"ogc-theme-options-css\">\n:root {\n" . $lines . "}\n</style>\n";
}
add_action( 'wp_head', 'ogc_theme_options_css', 20 );

/**
 * Body classes driven by theme options.
 *
 * Front-end CSS hooks off these, e.g.
 * `body.ogc-btn-primary-lift .ogc-btn:not(.ogc-btn-outline):hover { ... }`.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function ogc_theme_options_body_classes( $classes ) {
	if ( ! ogc_mod( 'ogc_toggle_orbit_animation' ) ) {
		$classes[] = 'ogc-motion-reduced-by-admin';
	}

	$tokens = array(
		'ogc-swoosh-'                 => ogc_mod( 'ogc_underline_swoosh_style' ),
		'ogc-btn-primary-'            => ogc_mod( 'ogc_button_hover_effect_primary' ),
		'ogc-btn-outline-'            => ogc_mod( 'ogc_button_hover_effect_outline' ),
		'ogc-btn-primary-intensity-'  => ogc_mod( 'ogc_button_hover_intensity_primary' ),
		'ogc-btn-outline-intensity-'  => ogc_mod( 'ogc_button_hover_intensity_outline' ),
		'ogc-btn-shadow-'             => ogc_mod( 'ogc_button_shadow_style' ),
		'ogc-btn-active-'             => ogc_mod( 'ogc_button_active_state' ),
		'ogc-btn-disabled-'           => ogc_mod( 'ogc_button_disabled_style' ),
		'ogc-btn-focus-'              => ogc_mod( 'ogc_button_focus_style' ),
		'ogc-transition-'             => ogc_mod( 'ogc_transition_speed' ),
	);

	foreach ( $tokens as $prefix => $value ) {
		$classes[] = $prefix . sanitize_html_class( (string) $value );
	}

	$classes[] = ogc_mod( 'ogc_header_sticky' ) ? 'ogc-header-sticky' : 'ogc-header-static';
	$classes[] = ogc_mod( 'ogc_ads_enabled' ) ? 'ogc-ads-on' : 'ogc-ads-off';

	return $classes;
}
add_filter( 'body_class', 'ogc_theme_options_body_classes' );

/* =========================================================================
 * 7. Tracking, maintenance mode, custom code
 * ====================================================================== */

/**
 * Print GA4 and Meta Pixel snippets when IDs are configured.
 */
function ogc_output_tracking_scripts() {
	if ( is_admin() ) {
		return;
	}

	$ga4   = (string) ogc_mod( 'ogc_google_analytics_id' );
	$pixel = (string) ogc_mod( 'ogc_meta_pixel_id' );

	if ( $ga4 ) {
		printf(
			'<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>' . "\n"
			. '<script id="ogc-ga4">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag(\'js\',new Date());gtag(\'config\',%2$s);</script>' . "\n",
			esc_attr( rawurlencode( $ga4 ) ),
			wp_json_encode( $ga4 )
		);
	}

	if ( $pixel ) {
		printf(
			'<script id="ogc-meta-pixel">!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?'
			. 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;'
			. "n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];"
			. "s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');"
			. "fbq('init',%1\$s);fbq('track','PageView');</script>\n",
			wp_json_encode( $pixel )
		);
	}
}
add_action( 'wp_head', 'ogc_output_tracking_scripts', 5 );

/**
 * Short-circuit front-end requests while maintenance mode is on.
 *
 * Users who can edit theme options keep browsing normally so the site can be
 * finished behind the curtain.
 */
function ogc_maintenance_mode_redirect() {
	if ( ! ogc_mod( 'ogc_maintenance_mode_enabled' ) ) {
		return;
	}

	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( current_user_can( OGC_OPTIONS_CAPABILITY ) ) {
		return;
	}

	$message = (string) ogc_mod( 'ogc_maintenance_mode_message' );
	if ( '' === $message ) {
		$message = ogc_option_default( 'ogc_maintenance_mode_message' );
	}

	wp_die(
		wp_kses_post( wpautop( $message ) ),
		esc_html( get_bloginfo( 'name' ) ),
		array(
			'response'  => 503,
			'back_link' => false,
		)
	);
}
add_action( 'template_redirect', 'ogc_maintenance_mode_redirect' );

/**
 * Print the admin-authored custom CSS after all theme stylesheets.
 */
function ogc_output_custom_css() {
	$css = (string) ogc_mod( 'ogc_custom_css' );
	if ( '' === $css ) {
		return;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin CSS, tag-stripped on save; escaping would corrupt selectors.
	echo "<style id=\"ogc-custom-css\">\n" . $css . "\n</style>\n";
}
add_action( 'wp_head', 'ogc_output_custom_css', 99 );

/**
 * Print the admin-authored custom JavaScript at the end of the page.
 */
function ogc_output_custom_js() {
	$js = (string) ogc_mod( 'ogc_custom_js' );
	if ( '' === $js ) {
		return;
	}

	// Tags were already stripped on save; guard the closing sequence regardless.
	$js = str_replace( '</script', '<\/script', $js );

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin code, tag-stripped on save.
	echo "<script id=\"ogc-custom-js\">\n" . $js . "\n</script>\n";
}
add_action( 'wp_footer', 'ogc_output_custom_js', 99 );

/* =========================================================================
 * 8. Colour presets
 * ====================================================================== */

/**
 * Named colour presets applied client-side in the options panel.
 *
 * @return array<string, array<string, string>>
 */
function ogc_color_presets() {
	return array(
		'OGC Default' => array(
			'ogc_color_accent_start'    => '#FFD27A',
			'ogc_color_accent_end'      => '#FFEAC2',
			'ogc_color_bg'              => '#0A0A0A',
			'ogc_color_text_primary'    => '#F5F5F5',
			'ogc_color_text_secondary'  => '#C9C9C9',
			'ogc_color_border'          => '#FFD27A',
			'ogc_color_button_bg_start' => '#FFD27A',
			'ogc_color_button_bg_end'   => '#FFEAC2',
			'ogc_color_button_text'     => '#FFFFFF',
			'ogc_color_shadow'          => '#000000',
			'ogc_header_bg_color'       => '#0A0A0A',
			'ogc_header_text_color'     => '#FFD27A',
			'ogc_footer_bg_color'       => '#0A0A0A',
		),
		'Slate Amber' => array(
			'ogc_color_accent_start'    => '#F0A500',
			'ogc_color_accent_end'      => '#FFD699',
			'ogc_color_bg'              => '#0B0E12',
			'ogc_color_text_primary'    => '#F2F5F8',
			'ogc_color_text_secondary'  => '#B9C3CE',
			'ogc_color_border'          => '#F0A500',
			'ogc_color_button_bg_start' => '#F0A500',
			'ogc_color_button_bg_end'   => '#FFD699',
			'ogc_color_button_text'     => '#14181D',
			'ogc_color_shadow'          => '#01060C',
			'ogc_header_bg_color'       => '#0B0E12',
			'ogc_header_text_color'     => '#FFD699',
			'ogc_footer_bg_color'       => '#0B0E12',
		),
		'Deep Ember'  => array(
			'ogc_color_accent_start'    => '#FF8A4C',
			'ogc_color_accent_end'      => '#FFB38A',
			'ogc_color_bg'              => '#070707',
			'ogc_color_text_primary'    => '#FBF4EF',
			'ogc_color_text_secondary'  => '#CBB6AA',
			'ogc_color_border'          => '#FF8A4C',
			'ogc_color_button_bg_start' => '#FF8A4C',
			'ogc_color_button_bg_end'   => '#FFB38A',
			'ogc_color_button_text'     => '#1A0B03',
			'ogc_color_shadow'          => '#000000',
			'ogc_header_bg_color'       => '#070707',
			'ogc_header_text_color'     => '#FFB38A',
			'ogc_footer_bg_color'       => '#0B0705',
		),
	);
}

/* =========================================================================
 * 9. Admin page
 * ====================================================================== */

/**
 * Register the top-level OGC NewFinity admin menu.
 */
function ogc_theme_options_menu() {
	$hook = add_menu_page(
		__( 'OGC NewFinity Theme Options', 'ogc-newfinity' ),
		__( 'OGC NewFinity', 'ogc-newfinity' ),
		OGC_OPTIONS_CAPABILITY,
		OGC_OPTIONS_PAGE_SLUG,
		'ogc_render_theme_options_page',
		'dashicons-admin-customizer',
		61
	);

	add_action( 'load-' . $hook, 'ogc_theme_options_load' );
}
add_action( 'admin_menu', 'ogc_theme_options_menu' );

/**
 * Fires when the options screen loads — hooks the asset enqueue for this page only.
 */
function ogc_theme_options_load() {
	add_action( 'admin_enqueue_scripts', 'ogc_theme_options_admin_assets' );
}

/**
 * Enqueue panel styles/scripts and the media library.
 */
function ogc_theme_options_admin_assets() {
	wp_enqueue_media();

	wp_enqueue_style(
		'ogc-admin-theme-options',
		OGC_THEME_URI . '/assets/css/admin-theme-options.css',
		array(),
		OGC_THEME_VERSION
	);

	wp_enqueue_script(
		'ogc-admin-theme-options',
		OGC_THEME_URI . '/assets/js/admin-theme-options.js',
		array(),
		OGC_THEME_VERSION,
		true
	);

	$nav_labels = array();
	foreach ( ogc_nav_slug_map() as $slug => $data ) {
		$nav_labels[ $slug ] = $data['label'];
	}

	wp_localize_script(
		'ogc-admin-theme-options',
		'ogcThemeOptions',
		array(
			'presets'   => ogc_color_presets(),
			'navLabels' => $nav_labels,
			'nonce'     => wp_create_nonce( 'ogc_theme_options_ui' ),
			'strings'   => array(
				'chooseImage'    => __( 'Choose image', 'ogc-newfinity' ),
				'useImage'       => __( 'Use this image', 'ogc-newfinity' ),
				'replaceImage'   => __( 'Replace', 'ogc-newfinity' ),
				'removeImage'    => __( 'Remove', 'ogc-newfinity' ),
				'addColumn'      => __( 'Add column', 'ogc-newfinity' ),
				'removeColumn'   => __( 'Remove column', 'ogc-newfinity' ),
				'addLink'        => __( 'Add link', 'ogc-newfinity' ),
				'remove'         => __( 'Remove', 'ogc-newfinity' ),
				'columnTitle'    => __( 'Column title', 'ogc-newfinity' ),
				'linkLabel'      => __( 'Label', 'ogc-newfinity' ),
				'linkUrl'        => __( 'URL', 'ogc-newfinity' ),
				'addSlot'        => __( 'Add ad slot', 'ogc-newfinity' ),
				'slotKey'        => __( 'Slot key', 'ogc-newfinity' ),
				'slotEnabled'    => __( 'Enabled', 'ogc-newfinity' ),
				'slotType'       => __( 'Type', 'ogc-newfinity' ),
				'slotImage'      => __( 'Image', 'ogc-newfinity' ),
				'slotCode'       => __( 'Embed code', 'ogc-newfinity' ),
				'slotLinkUrl'    => __( 'Click-through URL', 'ogc-newfinity' ),
				'visible'        => __( 'Visible', 'ogc-newfinity' ),
				'confirmResetAll' => __( 'Reset every OGC NewFinity theme option back to its default? This cannot be undone.', 'ogc-newfinity' ),
				'confirmResetTab' => __( 'Reset the options on this tab back to their defaults?', 'ogc-newfinity' ),
				'unsaved'        => __( 'You have unsaved theme option changes.', 'ogc-newfinity' ),
				'dragHandle'     => __( 'Drag to reorder', 'ogc-newfinity' ),
			),
		)
	);
}

/**
 * Currently requested tab id, validated against the schema.
 *
 * @return string
 */
function ogc_theme_options_current_tab() {
	$schema = ogc_theme_options_schema();
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tab selection.
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

	return isset( $schema[ $tab ] ) ? $tab : 'general';
}

/**
 * Render the admin options screen.
 */
function ogc_render_theme_options_page() {
	if ( ! current_user_can( OGC_OPTIONS_CAPABILITY ) ) {
		wp_die( esc_html__( 'You do not have permission to edit theme options.', 'ogc-newfinity' ) );
	}

	$schema      = ogc_theme_options_schema();
	$current_tab = ogc_theme_options_current_tab();
	?>
	<div class="wrap ogc-opt">

		<?php ogc_render_theme_options_notices(); ?>

		<header class="ogc-opt__masthead">
			<div class="ogc-opt__brand">
				<img class="ogc-opt__brand-logo" src="<?php echo esc_url( ogc_get_logo_uri() ); ?>" alt="" width="40" height="38">
				<div>
					<h1 class="ogc-opt__title"><?php esc_html_e( 'OGC NewFinity', 'ogc-newfinity' ); ?></h1>
					<p class="ogc-opt__subtitle">
						<?php
						printf(
							/* translators: %s: theme version. */
							esc_html__( 'Theme options — version %s', 'ogc-newfinity' ),
							esc_html( OGC_THEME_VERSION )
						);
						?>
					</p>
				</div>
			</div>
		</header>

		<div class="ogc-opt__layout">

			<nav class="ogc-opt__sidebar" aria-label="<?php esc_attr_e( 'Theme option sections', 'ogc-newfinity' ); ?>">
				<ul class="ogc-opt__tabs" role="tablist">
					<?php foreach ( $schema as $tab_id => $tab ) : ?>
						<li>
							<button
								type="button"
								class="ogc-opt__tab<?php echo $tab_id === $current_tab ? ' is-active' : ''; ?>"
								data-tab="<?php echo esc_attr( $tab_id ); ?>"
								role="tab"
								id="ogc-tab-<?php echo esc_attr( $tab_id ); ?>"
								aria-controls="ogc-panel-<?php echo esc_attr( $tab_id ); ?>"
								aria-selected="<?php echo $tab_id === $current_tab ? 'true' : 'false'; ?>"
							>
								<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
								<span><?php echo esc_html( $tab['label'] ); ?></span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<form
				class="ogc-opt__form"
				id="ogc-opt-form"
				method="post"
				action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			>
				<input type="hidden" name="action" value="ogc_save_theme_options">
				<input type="hidden" name="ogc_active_tab" id="ogc-active-tab" value="<?php echo esc_attr( $current_tab ); ?>">
				<?php
				wp_nonce_field( 'ogc_save_theme_options', 'ogc_save_nonce' );
				wp_nonce_field( 'ogc_reset_theme_options_tab', 'ogc_reset_tab_nonce' );
				?>

				<?php foreach ( $schema as $tab_id => $tab ) : ?>
					<section
						class="ogc-opt__panel<?php echo $tab_id === $current_tab ? ' is-active' : ''; ?>"
						id="ogc-panel-<?php echo esc_attr( $tab_id ); ?>"
						data-panel="<?php echo esc_attr( $tab_id ); ?>"
						role="tabpanel"
						aria-labelledby="ogc-tab-<?php echo esc_attr( $tab_id ); ?>"
						<?php echo $tab_id === $current_tab ? '' : 'hidden'; ?>
					>
						<div class="ogc-opt__panel-head">
							<h2><?php echo esc_html( $tab['label'] ); ?></h2>
							<?php if ( ! empty( $tab['description'] ) ) : ?>
								<p><?php echo esc_html( $tab['description'] ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( 'colors' === $tab_id ) : ?>
							<?php ogc_render_preset_bar(); ?>
						<?php endif; ?>

						<div class="ogc-opt__fields">
							<?php
							foreach ( $tab['fields'] as $field ) {
								ogc_render_option_field( $field );
							}
							?>
						</div>

						<?php if ( 'advanced' === $tab_id ) : ?>
							<?php ogc_render_advanced_tools(); ?>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>

				<div class="ogc-opt__savebar">
					<button type="submit" class="ogc-opt__save"><?php esc_html_e( 'Save changes', 'ogc-newfinity' ); ?></button>
					<button
						type="submit"
						class="ogc-opt__reset-tab"
						formnovalidate
						data-confirm="tab"
						formaction="<?php echo esc_url( admin_url( 'admin-post.php?action=ogc_reset_theme_options_tab' ) ); ?>"
					>
						<?php esc_html_e( 'Reset this tab', 'ogc-newfinity' ); ?>
					</button>
					<span class="ogc-opt__savebar-hint" id="ogc-opt-dirty" hidden><?php esc_html_e( 'Unsaved changes', 'ogc-newfinity' ); ?></span>
				</div>
			</form>
		</div>

		<?php ogc_render_theme_options_side_forms(); ?>
	</div>
	<?php
}

/**
 * Success / error notices for the options screen.
 */
function ogc_render_theme_options_notices() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only status flags on a redirect.
	$updated  = isset( $_GET['updated'] ) ? sanitize_key( wp_unslash( $_GET['updated'] ) ) : '';
	$notice   = isset( $_GET['ogc_notice'] ) ? sanitize_key( wp_unslash( $_GET['ogc_notice'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$messages = array(
		'imported'     => array( 'success', __( 'Theme options imported.', 'ogc-newfinity' ) ),
		'reset_tab'    => array( 'success', __( 'This tab was reset to its defaults.', 'ogc-newfinity' ) ),
		'reset_all'    => array( 'success', __( 'All theme options were reset to their defaults.', 'ogc-newfinity' ) ),
		'import_error' => array( 'error', __( 'That file could not be imported. Upload a JSON export from this theme.', 'ogc-newfinity' ) ),
	);

	if ( '1' === $updated ) {
		echo '<div class="ogc-opt__notice ogc-opt__notice--success"><p>' . esc_html__( 'Theme options saved.', 'ogc-newfinity' ) . '</p></div>';
	}

	if ( isset( $messages[ $notice ] ) ) {
		printf(
			'<div class="ogc-opt__notice ogc-opt__notice--%1$s"><p>%2$s</p></div>',
			esc_attr( $messages[ $notice ][0] ),
			esc_html( $messages[ $notice ][1] )
		);
	}
}

/**
 * Colour preset buttons for the Colors tab.
 */
function ogc_render_preset_bar() {
	?>
	<div class="ogc-opt__presets">
		<span class="ogc-opt__presets-label"><?php esc_html_e( 'Presets', 'ogc-newfinity' ); ?></span>
		<?php foreach ( ogc_color_presets() as $name => $values ) : ?>
			<button type="button" class="ogc-opt__preset" data-preset="<?php echo esc_attr( $name ); ?>">
				<span class="ogc-opt__preset-swatches" aria-hidden="true">
					<span style="background:<?php echo esc_attr( $values['ogc_color_bg'] ); ?>"></span>
					<span style="background:<?php echo esc_attr( $values['ogc_color_accent_start'] ); ?>"></span>
					<span style="background:<?php echo esc_attr( $values['ogc_color_accent_end'] ); ?>"></span>
				</span>
				<?php echo esc_html( $name ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Import / export / reset-all controls inside the Advanced tab.
 *
 * The controls use the HTML5 `form` attribute so they can submit the standalone
 * forms printed after the main options form (forms cannot be nested).
 */
function ogc_render_advanced_tools() {
	?>
	<div class="ogc-opt__tools">
		<h3><?php esc_html_e( 'Import, export & reset', 'ogc-newfinity' ); ?></h3>

		<div class="ogc-opt__tool">
			<div>
				<strong><?php esc_html_e( 'Export', 'ogc-newfinity' ); ?></strong>
				<p><?php esc_html_e( 'Download every theme option as a JSON file.', 'ogc-newfinity' ); ?></p>
			</div>
			<button type="submit" form="ogc-export-form" class="ogc-opt__btn"><?php esc_html_e( 'Download JSON', 'ogc-newfinity' ); ?></button>
		</div>

		<div class="ogc-opt__tool">
			<div>
				<strong><?php esc_html_e( 'Import', 'ogc-newfinity' ); ?></strong>
				<p><?php esc_html_e( 'Restore options from a previously exported JSON file.', 'ogc-newfinity' ); ?></p>
			</div>
			<div class="ogc-opt__tool-actions">
				<input type="file" id="ogc-import-file" name="ogc_import_file" form="ogc-import-form" accept="application/json,.json">
				<button type="submit" form="ogc-import-form" class="ogc-opt__btn"><?php esc_html_e( 'Import', 'ogc-newfinity' ); ?></button>
			</div>
		</div>

		<div class="ogc-opt__tool ogc-opt__tool--danger">
			<div>
				<strong><?php esc_html_e( 'Reset everything', 'ogc-newfinity' ); ?></strong>
				<p><?php esc_html_e( 'Remove all stored values so every option falls back to its theme default.', 'ogc-newfinity' ); ?></p>
			</div>
			<button type="submit" form="ogc-reset-all-form" class="ogc-opt__btn ogc-opt__btn--danger" data-confirm="all">
				<?php esc_html_e( 'Reset all options', 'ogc-newfinity' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Standalone forms targeted by the Advanced tab buttons.
 */
function ogc_render_theme_options_side_forms() {
	$action = esc_url( admin_url( 'admin-post.php' ) );
	?>
	<form id="ogc-export-form" class="ogc-opt__hidden-form" method="post" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>">
		<input type="hidden" name="action" value="ogc_export_theme_options">
		<?php wp_nonce_field( 'ogc_export_theme_options', 'ogc_export_nonce' ); ?>
	</form>

	<form id="ogc-import-form" class="ogc-opt__hidden-form" method="post" enctype="multipart/form-data" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>">
		<input type="hidden" name="action" value="ogc_import_theme_options">
		<?php wp_nonce_field( 'ogc_import_theme_options', 'ogc_import_nonce' ); ?>
	</form>

	<form id="ogc-reset-all-form" class="ogc-opt__hidden-form" method="post" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>">
		<input type="hidden" name="action" value="ogc_reset_theme_options_all">
		<?php wp_nonce_field( 'ogc_reset_theme_options_all', 'ogc_reset_all_nonce' ); ?>
	</form>
	<?php
}

/* =========================================================================
 * 10. Field renderer
 * ====================================================================== */

/**
 * Build an HTML attribute string from a field's `attrs`.
 *
 * @param array<string, mixed> $attrs Attribute map.
 * @param string[]             $allow Attribute names to emit.
 * @return string
 */
function ogc_field_attrs( $attrs, $allow ) {
	$out = '';
	foreach ( $allow as $name ) {
		if ( ! isset( $attrs[ $name ] ) || '' === $attrs[ $name ] ) {
			continue;
		}
		$out .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $attrs[ $name ] ) );
	}
	return $out;
}

/**
 * Render the label + tooltip cell shared by most controls.
 *
 * @param array<string, mixed> $field Field definition.
 */
function ogc_render_field_label( $field ) {
	?>
	<div class="ogc-opt__label">
		<label for="<?php echo esc_attr( $field['key'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
		<?php if ( ! empty( $field['tooltip'] ) ) : ?>
			<span class="ogc-opt__tip" tabindex="0" role="note" aria-label="<?php echo esc_attr( $field['tooltip'] ); ?>">
				<span aria-hidden="true">?</span>
				<span class="ogc-opt__tip-bubble"><?php echo esc_html( $field['tooltip'] ); ?></span>
			</span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render one schema field.
 *
 * @param array<string, mixed> $field Raw schema field definition.
 */
function ogc_render_option_field( $field ) {
	if ( 'heading' === $field['type'] ) {
		?>
		<div class="ogc-opt__section-head">
			<h3><?php echo esc_html( $field['label'] ); ?></h3>
			<?php if ( ! empty( $field['tooltip'] ) ) : ?>
				<p><?php echo esc_html( $field['tooltip'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return;
	}

	$field = ogc_theme_options_field( $field['key'] );
	if ( ! $field ) {
		return;
	}

	$key   = $field['key'];
	$value = ogc_mod( $key );
	$attrs = $field['attrs'];
	?>
	<div class="ogc-opt__row ogc-opt__row--<?php echo esc_attr( $field['type'] ); ?>" data-field="<?php echo esc_attr( $key ); ?>">
		<?php ogc_render_field_label( $field ); ?>
		<div class="ogc-opt__control">
			<?php
			switch ( $field['type'] ) {

				case 'color':
					printf(
						'<div class="ogc-opt__color"><input type="color" class="ogc-opt__color-swatch" value="%1$s" data-sync="%2$s" aria-label="%3$s">'
						. '<input type="text" id="%2$s" name="%2$s" class="ogc-opt__color-text" value="%1$s" spellcheck="false" autocomplete="off">'
						. '<button type="button" class="ogc-opt__color-reset" data-default="%4$s">%5$s</button></div>',
						esc_attr( $value ),
						esc_attr( $key ),
						esc_attr( $field['label'] ),
						esc_attr( $field['default'] ),
						esc_html__( 'Default', 'ogc-newfinity' )
					);
					break;

				case 'text':
				case 'email':
				case 'url':
					printf(
						'<input type="%1$s" id="%2$s" name="%2$s" class="ogc-opt__input" value="%3$s"%4$s>',
						esc_attr( $field['type'] ),
						esc_attr( $key ),
						esc_attr( $value ),
						ogc_field_attrs( $attrs, array( 'placeholder', 'maxlength' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
					);
					break;

				case 'number':
					printf(
						'<div class="ogc-opt__number"><input type="number" id="%1$s" name="%1$s" class="ogc-opt__input ogc-opt__input--number" value="%2$s"%3$s>%4$s</div>',
						esc_attr( $key ),
						esc_attr( ogc_css_num( $value ) ),
						ogc_field_attrs( $attrs, array( 'min', 'max', 'step', 'placeholder' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
						isset( $attrs['unit'] ) ? '<span class="ogc-opt__unit">' . esc_html( $attrs['unit'] ) . '</span>' : ''
					);
					break;

				case 'range':
					printf(
						'<div class="ogc-opt__range"><input type="range" id="%1$s" name="%1$s" value="%2$d"%3$s data-range-output="%1$s-output"><output id="%1$s-output" class="ogc-opt__range-output">%2$d%4$s</output></div>',
						esc_attr( $key ),
						(int) $value,
						ogc_field_attrs( $attrs, array( 'min', 'max', 'step' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
						isset( $attrs['unit'] ) ? esc_html( $attrs['unit'] ) : ''
					);
					break;

				case 'checkbox':
					printf(
						'<label class="ogc-opt__switch"><input type="checkbox" id="%1$s" name="%1$s" value="1"%2$s><span class="ogc-opt__switch-track" aria-hidden="true"></span><span class="ogc-opt__switch-text">%3$s</span></label>',
						esc_attr( $key ),
						checked( (bool) $value, true, false ),
						esc_html__( 'Enabled', 'ogc-newfinity' )
					);
					break;

				case 'select':
					echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" class="ogc-opt__select">';
					foreach ( $field['options'] as $option_value => $option_label ) {
						printf(
							'<option value="%1$s"%2$s>%3$s</option>',
							esc_attr( $option_value ),
							selected( (string) $value, (string) $option_value, false ),
							esc_html( $option_label )
						);
					}
					echo '</select>';
					break;

				case 'textarea':
				case 'monospace':
					printf(
						'<textarea id="%1$s" name="%1$s" class="ogc-opt__textarea%2$s" rows="%3$d"%4$s>%5$s</textarea>',
						esc_attr( $key ),
						'monospace' === $field['type'] ? ' ogc-opt__textarea--code' : '',
						isset( $attrs['rows'] ) ? (int) $attrs['rows'] : 4,
						ogc_field_attrs( $attrs, array( 'placeholder' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
						esc_textarea( $value )
					);
					break;

				case 'image':
					?>
					<div class="ogc-opt__image" data-image-field>
						<input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
						<div class="ogc-opt__image-preview"<?php echo $value ? '' : ' hidden'; ?>>
							<img src="<?php echo esc_url( $value ); ?>" alt="">
						</div>
						<div class="ogc-opt__image-actions">
							<button type="button" class="ogc-opt__btn" data-image-select><?php esc_html_e( 'Choose image', 'ogc-newfinity' ); ?></button>
							<button type="button" class="ogc-opt__btn ogc-opt__btn--ghost" data-image-remove<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'ogc-newfinity' ); ?></button>
						</div>
					</div>
					<?php
					break;

				case 'nav_order':
				case 'footer_columns':
				case 'ads_config':
					printf(
						'<div class="ogc-opt__builder" data-builder="%1$s" data-input="%2$s"></div>'
						. '<input type="hidden" id="%2$s" name="%2$s" value="%3$s">'
						. '<noscript><p class="ogc-opt__noscript">%4$s</p></noscript>',
						esc_attr( $field['type'] ),
						esc_attr( $key ),
						esc_attr( is_string( $value ) ? $value : wp_json_encode( $value ) ),
						esc_html__( 'JavaScript is required to edit this setting.', 'ogc-newfinity' )
					);
					break;
			}
			?>
		</div>
	</div>
	<?php
}

/* =========================================================================
 * 11. admin-post handlers
 * ====================================================================== */

/**
 * Guard: current user may manage theme options, or die.
 */
function ogc_theme_options_require_cap() {
	if ( ! current_user_can( OGC_OPTIONS_CAPABILITY ) ) {
		wp_die( esc_html__( 'You do not have permission to edit theme options.', 'ogc-newfinity' ), '', array( 'response' => 403 ) );
	}
}

/**
 * Build the options screen URL.
 *
 * @param array<string, string|int> $args Extra query args.
 * @return string
 */
function ogc_theme_options_url( $args = array() ) {
	return add_query_arg(
		array_merge( array( 'page' => OGC_OPTIONS_PAGE_SLUG ), $args ),
		admin_url( 'admin.php' )
	);
}

/**
 * Requested tab from a submitted form, validated.
 *
 * @return string
 */
function ogc_posted_tab() {
	$schema = ogc_theme_options_schema();
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- callers verify their own nonce first.
	$tab = isset( $_POST['ogc_active_tab'] ) ? sanitize_key( wp_unslash( $_POST['ogc_active_tab'] ) ) : '';

	return isset( $schema[ $tab ] ) ? $tab : 'general';
}

/**
 * Persist submitted theme options.
 */
function ogc_handle_save_theme_options() {
	ogc_theme_options_require_cap();
	check_admin_referer( 'ogc_save_theme_options', 'ogc_save_nonce' );

	foreach ( ogc_theme_options_fields() as $key => $field ) {
		if ( 'checkbox' === $field['type'] ) {
			set_theme_mod( $key, isset( $_POST[ $key ] ) );
			continue;
		}

		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by the field's own callback below.
		$raw = wp_unslash( $_POST[ $key ] );
		set_theme_mod( $key, ogc_sanitize_option_value( $field, $raw ) );
	}

	wp_safe_redirect(
		ogc_theme_options_url(
			array(
				'tab'     => ogc_posted_tab(),
				'updated' => 1,
			)
		)
	);
	exit;
}
add_action( 'admin_post_ogc_save_theme_options', 'ogc_handle_save_theme_options' );

/**
 * Current value of every schema option, for export.
 *
 * @return array<string, mixed>
 */
function ogc_collect_theme_options() {
	$values = array();
	foreach ( array_keys( ogc_theme_options_fields() ) as $key ) {
		$values[ $key ] = ogc_mod( $key );
	}
	return $values;
}

/**
 * Stream a JSON export of all theme options.
 */
function ogc_handle_export_theme_options() {
	ogc_theme_options_require_cap();
	check_admin_referer( 'ogc_export_theme_options', 'ogc_export_nonce' );

	$payload = array(
		'theme'     => 'ogc-newfinity',
		'version'   => OGC_THEME_VERSION,
		'exported'  => gmdate( 'c' ),
		'options'   => ogc_collect_theme_options(),
	);

	$filename = sprintf( 'ogc-newfinity-options-%s.json', gmdate( 'Ymd-His' ) );

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $filename );

	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'admin_post_ogc_export_theme_options', 'ogc_handle_export_theme_options' );

/**
 * Import theme options from an uploaded JSON export.
 */
function ogc_handle_import_theme_options() {
	ogc_theme_options_require_cap();
	check_admin_referer( 'ogc_import_theme_options', 'ogc_import_nonce' );

	$tmp = isset( $_FILES['ogc_import_file']['tmp_name'] )
		? sanitize_text_field( wp_unslash( $_FILES['ogc_import_file']['tmp_name'] ) )
		: '';

	if ( ! $tmp || ! is_uploaded_file( $tmp ) ) {
		wp_safe_redirect( ogc_theme_options_url( array( 'tab' => 'advanced', 'ogc_notice' => 'import_error' ) ) );
		exit;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a PHP upload temp file.
	$contents = file_get_contents( $tmp );
	$decoded  = json_decode( (string) $contents, true );
	$options  = isset( $decoded['options'] ) && is_array( $decoded['options'] ) ? $decoded['options'] : null;

	if ( null === $options ) {
		wp_safe_redirect( ogc_theme_options_url( array( 'tab' => 'advanced', 'ogc_notice' => 'import_error' ) ) );
		exit;
	}

	foreach ( ogc_theme_options_fields() as $key => $field ) {
		if ( ! array_key_exists( $key, $options ) ) {
			continue;
		}
		set_theme_mod( $key, ogc_sanitize_option_value( $field, $options[ $key ] ) );
	}

	wp_safe_redirect( ogc_theme_options_url( array( 'tab' => 'advanced', 'ogc_notice' => 'imported' ) ) );
	exit;
}
add_action( 'admin_post_ogc_import_theme_options', 'ogc_handle_import_theme_options' );

/**
 * Reset every option belonging to one tab.
 */
function ogc_handle_reset_theme_options_tab() {
	ogc_theme_options_require_cap();
	check_admin_referer( 'ogc_reset_theme_options_tab', 'ogc_reset_tab_nonce' );

	$tab = ogc_posted_tab();

	foreach ( ogc_theme_options_fields() as $key => $field ) {
		if ( $field['tab'] === $tab ) {
			remove_theme_mod( $key );
		}
	}

	wp_safe_redirect( ogc_theme_options_url( array( 'tab' => $tab, 'ogc_notice' => 'reset_tab' ) ) );
	exit;
}
add_action( 'admin_post_ogc_reset_theme_options_tab', 'ogc_handle_reset_theme_options_tab' );

/**
 * Reset every theme option.
 */
function ogc_handle_reset_theme_options_all() {
	ogc_theme_options_require_cap();
	check_admin_referer( 'ogc_reset_theme_options_all', 'ogc_reset_all_nonce' );

	foreach ( array_keys( ogc_theme_options_fields() ) as $key ) {
		remove_theme_mod( $key );
	}

	wp_safe_redirect( ogc_theme_options_url( array( 'tab' => 'advanced', 'ogc_notice' => 'reset_all' ) ) );
	exit;
}
add_action( 'admin_post_ogc_reset_theme_options_all', 'ogc_handle_reset_theme_options_all' );
