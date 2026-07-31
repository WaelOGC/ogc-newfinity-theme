<?php
/**
 * Theme Customizer — OGC NewFinity Theme Options.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

/**
 * Allow-listed heading fonts for the Customizer select + Google Fonts enqueue.
 *
 * @return array<string, string> Font family name => Google Fonts CSS2 URL fragment (empty = system/bundled default).
 */
function ogc_heading_font_choices() {
	return array(
		'Space Grotesk'  => 'family=Space+Grotesk:wght@500;700',
		'Playfair Display' => 'family=Playfair+Display:wght@500;700',
		'Poppins'        => 'family=Poppins:wght@500;700',
		'Sora'           => 'family=Sora:wght@500;700',
		'Manrope'        => 'family=Manrope:wght@500;700',
	);
}

/**
 * Allow-listed body fonts for the Customizer select + Google Fonts enqueue.
 *
 * @return array<string, string> Font family name => Google Fonts CSS2 URL fragment.
 */
function ogc_body_font_choices() {
	return array(
		'Inter'         => 'family=Inter:wght@400;500;600;700',
		'Work Sans'     => 'family=Work+Sans:wght@400;500;600;700',
		'Source Sans 3' => 'family=Source+Sans+3:wght@400;500;600;700',
		'Nunito Sans'   => 'family=Nunito+Sans:wght@400;500;600;700',
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
 * Sanitize a value against an allow-list; fall back to $default.
 *
 * @param string               $value   Submitted value.
 * @param array<string, mixed> $allowed Allowed keys.
 * @param string               $default Fallback.
 * @return string
 */
function ogc_sanitize_choice( $value, $allowed, $default ) {
	$value = is_string( $value ) ? $value : '';
	return array_key_exists( $value, $allowed ) ? $value : $default;
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
 * Sanitize checkbox (orbit animation toggle).
 *
 * @param mixed $value Submitted value.
 * @return bool
 */
function ogc_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize image URL from WP_Customize_Image_Control.
 *
 * @param string $value Submitted URL.
 * @return string
 */
function ogc_sanitize_image_url( $value ) {
	return esc_url_raw( (string) $value );
}

/**
 * Contact email from Theme Options.
 *
 * @return string
 */
function ogc_get_contact_email() {
	$email = get_theme_mod( 'ogc_contact_email', OGC_DEFAULT_CONTACT_EMAIL );
	$email = sanitize_email( $email );
	return $email ? $email : OGC_DEFAULT_CONTACT_EMAIL;
}

/**
 * Location text from Theme Options.
 *
 * @return string
 */
function ogc_get_location_text() {
	$text = get_theme_mod( 'ogc_location_text', OGC_DEFAULT_LOCATION_TEXT );
	$text = sanitize_text_field( $text );
	return '' !== $text ? $text : OGC_DEFAULT_LOCATION_TEXT;
}

/**
 * Footer tagline from Theme Options.
 *
 * @return string
 */
function ogc_get_footer_tagline() {
	$text = get_theme_mod( 'ogc_footer_tagline', OGC_DEFAULT_FOOTER_TAGLINE );
	$text = sanitize_textarea_field( $text );
	return '' !== $text ? $text : OGC_DEFAULT_FOOTER_TAGLINE;
}

/**
 * Response-time guarantee label from Theme Options.
 *
 * @return string
 */
function ogc_get_response_time_text() {
	$text = get_theme_mod( 'ogc_response_time_text', OGC_DEFAULT_RESPONSE_TIME );
	$text = sanitize_text_field( $text );
	return '' !== $text ? $text : OGC_DEFAULT_RESPONSE_TIME;
}

/**
 * Social profile URL for a network key (x, linkedin, github, discord).
 * Defaults match the current footer links so the site looks identical
 * before any Customizer change; clear a field to hide that icon.
 *
 * @param string $network Network slug.
 * @return string Empty string when unset (caller should hide the icon).
 */
function ogc_get_social_url( $network ) {
	$defaults = array(
		'x'        => 'https://x.com/OGCNewfinity',
		'linkedin' => 'https://www.linkedin.com/company/ogc-newfinity',
		'github'   => 'https://github.com/OGC-NewFinity',
		'discord'  => 'https://discord.com/invite/AyzKNHdn',
	);
	$network = sanitize_key( $network );
	if ( ! isset( $defaults[ $network ] ) ) {
		return '';
	}
	// get_theme_mod returns default when unset; empty string after admin clears is intentional hide.
	$url = get_theme_mod( "ogc_social_{$network}", $defaults[ $network ] );
	return $url ? esc_url( $url ) : '';
}

/**
 * Light-logo image URL (optional variant beyond the primary custom logo).
 *
 * @return string
 */
function ogc_get_logo_light_url() {
	return esc_url( get_theme_mod( 'ogc_logo_light', '' ) );
}

/**
 * Optional hero background image URL for a key page.
 *
 * @param string $page_key One of: home, about, services, mission.
 * @return string
 */
function ogc_get_hero_bg_url( $page_key ) {
	$allowed = array( 'home', 'about', 'services', 'mission' );
	$page_key = sanitize_key( $page_key );
	if ( ! in_array( $page_key, $allowed, true ) ) {
		return '';
	}
	return esc_url( get_theme_mod( "ogc_hero_bg_{$page_key}", '' ) );
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
		'<div class="ogc-hero-bg" style="background-image:url(%s);" aria-hidden="true"></div>',
		esc_url( $url )
	);
}

/**
 * Build a Google Fonts CSS2 URL for the currently selected heading + body fonts.
 * Only loads the selected pair (never the full allow-list).
 *
 * @return string Absolute Google Fonts URL, or empty if nothing to load.
 */
function ogc_get_google_fonts_url() {
	$heading = ogc_sanitize_font_heading( get_theme_mod( 'ogc_font_heading', OGC_DEFAULT_FONT_HEADING ) );
	$body    = ogc_sanitize_font_body( get_theme_mod( 'ogc_font_body', OGC_DEFAULT_FONT_BODY ) );

	$heading_map = ogc_heading_font_choices();
	$body_map    = ogc_body_font_choices();

	$parts = array();
	if ( ! empty( $heading_map[ $heading ] ) ) {
		$parts[] = $heading_map[ $heading ];
	}
	if ( ! empty( $body_map[ $body ] ) ) {
		$parts[] = $body_map[ $body ];
	}

	if ( empty( $parts ) ) {
		return '';
	}

	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/**
 * Register Customizer panel, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ogc_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'ogc_theme_options',
		array(
			'title'    => __( 'OGC NewFinity Theme Options', 'ogc-newfinity' ),
			'priority' => 30,
		)
	);

	/* ------------------------------------------------------------------ Colors */
	$wp_customize->add_section(
		'ogc_colors',
		array(
			'title' => __( 'Colors', 'ogc-newfinity' ),
			'panel' => 'ogc_theme_options',
		)
	);

	$colors = array(
		'ogc_color_accent_start'  => array(
			'label'   => __( 'Accent gradient start', 'ogc-newfinity' ),
			'default' => OGC_DEFAULT_COLOR_ACCENT_START,
		),
		'ogc_color_accent_end'    => array(
			'label'   => __( 'Accent gradient end', 'ogc-newfinity' ),
			'default' => OGC_DEFAULT_COLOR_ACCENT_END,
		),
		'ogc_color_bg'            => array(
			'label'   => __( 'Background color', 'ogc-newfinity' ),
			'default' => OGC_DEFAULT_COLOR_BG,
		),
		'ogc_color_text_primary'  => array(
			'label'   => __( 'Text color (primary)', 'ogc-newfinity' ),
			'default' => OGC_DEFAULT_COLOR_TEXT_PRIMARY,
		),
		'ogc_color_text_secondary' => array(
			'label'   => __( 'Text color (secondary)', 'ogc-newfinity' ),
			'default' => OGC_DEFAULT_COLOR_TEXT_SECONDARY,
		),
	);

	foreach ( $colors as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $args['label'],
					'section' => 'ogc_colors',
				)
			)
		);
	}

	/* ------------------------------------------------------------------- Fonts */
	$wp_customize->add_section(
		'ogc_fonts',
		array(
			'title' => __( 'Fonts', 'ogc-newfinity' ),
			'panel' => 'ogc_theme_options',
		)
	);

	$heading_choices = array_combine(
		array_keys( ogc_heading_font_choices() ),
		array_keys( ogc_heading_font_choices() )
	);
	$wp_customize->add_setting(
		'ogc_font_heading',
		array(
			'default'           => OGC_DEFAULT_FONT_HEADING,
			'sanitize_callback' => 'ogc_sanitize_font_heading',
			'transport'         => 'refresh', // Full refresh — live Google Fonts loading via JS is out of scope.
		)
	);
	$wp_customize->add_control(
		'ogc_font_heading',
		array(
			'label'   => __( 'Heading font', 'ogc-newfinity' ),
			'section' => 'ogc_fonts',
			'type'    => 'select',
			'choices' => $heading_choices,
		)
	);

	$body_choices = array_combine(
		array_keys( ogc_body_font_choices() ),
		array_keys( ogc_body_font_choices() )
	);
	$wp_customize->add_setting(
		'ogc_font_body',
		array(
			'default'           => OGC_DEFAULT_FONT_BODY,
			'sanitize_callback' => 'ogc_sanitize_font_body',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_font_body',
		array(
			'label'   => __( 'Body font', 'ogc-newfinity' ),
			'section' => 'ogc_fonts',
			'type'    => 'select',
			'choices' => $body_choices,
		)
	);

	/* ------------------------------------------------------------------ Images */
	$wp_customize->add_section(
		'ogc_images',
		array(
			'title'       => __( 'Images', 'ogc-newfinity' ),
			'description' => __( 'Primary logo is under Site Identity → Logo. Site Icon (favicon) is under Site Identity → Site Icon. Theme bundled favicons are the fallback when no Site Icon is set.', 'ogc-newfinity' ),
			'panel'       => 'ogc_theme_options',
		)
	);

	$wp_customize->add_setting(
		'ogc_logo_light',
		array(
			'default'           => '',
			'sanitize_callback' => 'ogc_sanitize_image_url',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'ogc_logo_light',
			array(
				'label'       => __( 'Logo (light contexts)', 'ogc-newfinity' ),
				'description' => __( 'Optional light-background logo variant. Primary (dark-header) logo stays under Site Identity.', 'ogc-newfinity' ),
				'section'     => 'ogc_images',
			)
		)
	);

	$hero_bgs = array(
		'ogc_hero_bg_home'     => __( 'Home hero background (optional)', 'ogc-newfinity' ),
		'ogc_hero_bg_about'    => __( 'About hero background (optional)', 'ogc-newfinity' ),
		'ogc_hero_bg_services' => __( 'Services hero background (optional)', 'ogc-newfinity' ),
		'ogc_hero_bg_mission'  => __( 'Mission hero background (optional)', 'ogc-newfinity' ),
	);

	foreach ( $hero_bgs as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'ogc_sanitize_image_url',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$id,
				array(
					'label'       => $label,
					'description' => __( 'Additive layer behind the existing hero canvas/content. Leave empty to keep the current WebGL/canvas hero unchanged.', 'ogc-newfinity' ),
					'section'     => 'ogc_images',
				)
			)
		);
	}

	/* ----------------------------------------------------------------- Effects */
	$wp_customize->add_section(
		'ogc_effects',
		array(
			'title' => __( 'Effects', 'ogc-newfinity' ),
			'panel' => 'ogc_theme_options',
		)
	);

	$wp_customize->add_setting(
		'ogc_toggle_orbit_animation',
		array(
			'default'           => true,
			'sanitize_callback' => 'ogc_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_toggle_orbit_animation',
		array(
			'label'       => __( 'Enable orbit / decorative motion', 'ogc-newfinity' ),
			'description' => __( 'When unchecked, hero animations freeze using the same reduced-motion path as prefers-reduced-motion.', 'ogc-newfinity' ),
			'section'     => 'ogc_effects',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'ogc_underline_swoosh_style',
		array(
			'default'           => 'gradient',
			'sanitize_callback' => 'ogc_sanitize_swoosh_style',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'ogc_underline_swoosh_style',
		array(
			'label'   => __( 'H1 underline / shimmer swoosh', 'ogc-newfinity' ),
			'section' => 'ogc_effects',
			'type'    => 'select',
			'choices' => ogc_swoosh_style_choices(),
		)
	);

	/* ----------------------------------------------------------------- Content */
	$wp_customize->add_section(
		'ogc_content',
		array(
			'title' => __( 'Content', 'ogc-newfinity' ),
			'panel' => 'ogc_theme_options',
		)
	);

	$wp_customize->add_setting(
		'ogc_contact_email',
		array(
			'default'           => OGC_DEFAULT_CONTACT_EMAIL,
			'sanitize_callback' => 'sanitize_email',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_contact_email',
		array(
			'label'   => __( 'Contact email', 'ogc-newfinity' ),
			'section' => 'ogc_content',
			'type'    => 'email',
		)
	);

	$wp_customize->add_setting(
		'ogc_location_text',
		array(
			'default'           => OGC_DEFAULT_LOCATION_TEXT,
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_location_text',
		array(
			'label'   => __( 'Location text', 'ogc-newfinity' ),
			'section' => 'ogc_content',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'ogc_footer_tagline',
		array(
			'default'           => OGC_DEFAULT_FOOTER_TAGLINE,
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_footer_tagline',
		array(
			'label'   => __( 'Footer tagline', 'ogc-newfinity' ),
			'section' => 'ogc_content',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'ogc_response_time_text',
		array(
			'default'           => OGC_DEFAULT_RESPONSE_TIME,
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'ogc_response_time_text',
		array(
			'label'   => __( 'Response-time guarantee text', 'ogc-newfinity' ),
			'section' => 'ogc_content',
			'type'    => 'text',
		)
	);

	$socials = array(
		'ogc_social_x'        => array(
			'label'   => __( 'X (Twitter) URL', 'ogc-newfinity' ),
			'default' => 'https://x.com/OGCNewfinity',
		),
		'ogc_social_linkedin' => array(
			'label'   => __( 'LinkedIn URL', 'ogc-newfinity' ),
			'default' => 'https://www.linkedin.com/company/ogc-newfinity',
		),
		'ogc_social_github'   => array(
			'label'   => __( 'GitHub URL', 'ogc-newfinity' ),
			'default' => 'https://github.com/OGC-NewFinity',
		),
		'ogc_social_discord'  => array(
			'label'   => __( 'Discord URL', 'ogc-newfinity' ),
			'default' => 'https://discord.com/invite/AyzKNHdn',
		),
	);

	foreach ( $socials as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $args['label'],
				'description' => __( 'Leave empty to hide this social icon.', 'ogc-newfinity' ),
				'section'     => 'ogc_content',
				'type'        => 'url',
			)
		);
	}
}
add_action( 'customize_register', 'ogc_customize_register' );

/**
 * Output Customizer CSS custom properties in wp_head.
 *
 * Always outputs every token (not a diff-from-default). Reliability over cleverness —
 * docs/04 requires settings to actually apply end-to-end; always-on :root overrides
 * are predictable regardless of how theme_mod storage / defaults interact.
 */
function ogc_customizer_css() {
	$accent_start  = sanitize_hex_color( get_theme_mod( 'ogc_color_accent_start', OGC_DEFAULT_COLOR_ACCENT_START ) );
	$accent_end    = sanitize_hex_color( get_theme_mod( 'ogc_color_accent_end', OGC_DEFAULT_COLOR_ACCENT_END ) );
	$bg            = sanitize_hex_color( get_theme_mod( 'ogc_color_bg', OGC_DEFAULT_COLOR_BG ) );
	$text_primary  = sanitize_hex_color( get_theme_mod( 'ogc_color_text_primary', OGC_DEFAULT_COLOR_TEXT_PRIMARY ) );
	$text_secondary = sanitize_hex_color( get_theme_mod( 'ogc_color_text_secondary', OGC_DEFAULT_COLOR_TEXT_SECONDARY ) );

	$accent_start   = $accent_start ? $accent_start : OGC_DEFAULT_COLOR_ACCENT_START;
	$accent_end     = $accent_end ? $accent_end : OGC_DEFAULT_COLOR_ACCENT_END;
	$bg             = $bg ? $bg : OGC_DEFAULT_COLOR_BG;
	$text_primary   = $text_primary ? $text_primary : OGC_DEFAULT_COLOR_TEXT_PRIMARY;
	$text_secondary = $text_secondary ? $text_secondary : OGC_DEFAULT_COLOR_TEXT_SECONDARY;

	$font_heading = ogc_sanitize_font_heading( get_theme_mod( 'ogc_font_heading', OGC_DEFAULT_FONT_HEADING ) );
	$font_body    = ogc_sanitize_font_body( get_theme_mod( 'ogc_font_body', OGC_DEFAULT_FONT_BODY ) );
	$swoosh       = ogc_sanitize_swoosh_style( get_theme_mod( 'ogc_underline_swoosh_style', 'gradient' ) );

	$font_heading_stack = sprintf(
		'"%s", system-ui, -apple-system, "Segoe UI", sans-serif',
		esc_attr( $font_heading )
	);
	$font_body_stack = sprintf(
		'"%s", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
		esc_attr( $font_body )
	);

	printf(
		"<style id=\"ogc-customizer-css\">\n:root {\n"
		. "\t--accent-gradient-start: %1\$s;\n"
		. "\t--accent-gradient-end: %2\$s;\n"
		. "\t--bg-primary: %3\$s;\n"
		. "\t--text-primary: %4\$s;\n"
		. "\t--text-secondary: %5\$s;\n"
		. "\t--ogc-text-primary: %4\$s;\n"
		. "\t--font-heading: %6\$s;\n"
		. "\t--font-body: %7\$s;\n"
		. "\t--ogc-swoosh-mode: %8\$s;\n"
		. "}\n</style>\n",
		esc_attr( $accent_start ),
		esc_attr( $accent_end ),
		esc_attr( $bg ),
		esc_attr( $text_primary ),
		esc_attr( $text_secondary ),
		$font_heading_stack, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from allow-listed font names + fixed stack.
		$font_body_stack, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( $swoosh )
	);
}
add_action( 'wp_head', 'ogc_customizer_css', 20 );

/**
 * Body classes for admin motion toggle + swoosh mode.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function ogc_customizer_body_classes( $classes ) {
	if ( ! get_theme_mod( 'ogc_toggle_orbit_animation', true ) ) {
		$classes[] = 'ogc-motion-reduced-by-admin';
	}

	$swoosh    = ogc_sanitize_swoosh_style( get_theme_mod( 'ogc_underline_swoosh_style', 'gradient' ) );
	$classes[] = 'ogc-swoosh-' . $swoosh;

	return $classes;
}
add_filter( 'body_class', 'ogc_customizer_body_classes' );

/**
 * Enqueue Customizer live-preview script (postMessage transport).
 */
function ogc_customize_preview_init() {
	wp_enqueue_script(
		'ogc-customize-preview',
		OGC_THEME_URI . '/assets/js/customize-preview.js',
		array( 'customize-preview' ),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'ogc_customize_preview_init' );
