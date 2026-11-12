<?php
/**
 * Reusable template helper functions.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical logo file — logo-ogc-newfinity-light.png (site-wide).
 * Used in header, footer, and any future large placements.
 *
 * @return string Absolute URI to the logo PNG.
 */
function ogc_get_logo_uri() {
	return OGC_THEME_URI . '/assets/images/Identity/logo-ogc-newfinity-light.png';
}

/**
 * Echo the site logo image link.
 *
 * Single official mark for header, footer, and elsewhere — no text lockup,
 * no primary/light switching.
 *
 * @param array $args {
 *     Optional.
 *     @type string $class   Extra CSS class on the wrapper link. Default ''.
 *     @type string $src     Image URL override (Theme Options header/footer logo). Default '' (bundled mark).
 *     @type int    $width   Image width attribute. Default 40.
 *     @type int    $height  Image height attribute. Default 38.
 *     @type bool   $lazy    Whether to lazy-load. Default true.
 *     @type string $alt     Alt text. Default 'OGC NewFinity logo'.
 * }
 */
function ogc_get_logo( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'  => '',
			'src'    => '',
			'width'  => 40,
			'height' => 38,
			'lazy'   => true,
			'alt'    => 'OGC NewFinity logo',
		)
	);

	$src = is_string( $args['src'] ) && '' !== trim( $args['src'] ) ? trim( $args['src'] ) : ogc_get_logo_uri();

	$classes = trim( 'ogc-logo ' . $args['class'] );
	$loading = $args['lazy'] ? 'lazy' : 'eager';

	echo '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';

	printf(
		'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="%5$s" decoding="async" class="ogc-logo__img"%6$s>',
		esc_url( $src ),
		esc_attr( $args['alt'] ),
		(int) $args['width'],
		(int) $args['height'],
		esc_attr( $loading ),
		$args['lazy'] ? '' : ' fetchpriority="high"'
	);

	echo '</a>';
}

/**
 * Echo an inline social icon SVG by network key.
 *
 * @param string $network One of: x, linkedin, github, discord, facebook, reddit, copy, link.
 */
function ogc_social_icon( $network ) {
	$icons = array(
		'x'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.727-8.889L2.25 2.25h6.086l4.263 5.708L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>',
		'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
		'github'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
		'discord'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
		'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>',
		'reddit'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 01-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 01.042.52c0 2.769-3.209 5.002-7.14 5.002-3.932 0-7.14-2.233-7.14-5.002 0-.175.014-.348.042-.52-.575-.281-1.01-.898-1.01-1.614 0-.968.786-1.754 1.754-1.754.463 0 .878.175 1.19.469 1.19-.87 2.848-1.432 4.659-1.5l.885-4.182a.342.342 0 01.14-.197.35.35 0 01.238-.042l2.914.614a1.25 1.25 0 011.21-.845zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 00-.231.094.33.33 0 000 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 000-.463.33.33 0 00-.463 0c-.547.533-1.684.73-2.498.73-.814 0-1.98-.185-2.498-.73a.327.327 0 00-.232-.094z"/></svg>',
		'copy'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>',
		'link'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>',
	);

	if ( ! isset( $icons[ $network ] ) ) {
		$network = 'link';
	}

	if ( isset( $icons[ $network ] ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted static SVG markup.
		echo $icons[ $network ];
	}
}


/**
 * Echo a circular embossed share chip with an official brand/social SVG.
 *
 * @param string $network Network key for ogc_social_icon().
 */
function ogc_share_icon_chip( $network ) {
	echo '<span class="ogc-icon ogc-icon--sm ogc-icon--circle" aria-hidden="true">';
	ogc_social_icon( $network );
	echo '</span>';
}

/**
 * Echo a small decorative map-pin / mail SVG for footer contact rows.
 *
 * @param string $type 'map' or 'mail'.
 */
function ogc_footer_icon( $type ) {
	if ( 'map' === $type ) {
		echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>';
		return;
	}

	if ( 'mail' === $type ) {
		echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
	}
}

/**
 * Return raw SVG markup for a named outline icon (stroke, currentColor).
 *
 * @param string $name Icon key.
 * @param int    $size Pixel size for width/height attributes. Default 24.
 * @return string SVG markup or empty string.
 */
function ogc_get_icon_svg( $name, $size = 24 ) {
	$size  = max( 12, (int) $size );
	$attrs = sprintf(
		'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="%1$d" height="%1$d" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"',
		$size
	);

	$icons = array(
		/* Nav */
		'home'     => '<svg ' . $attrs . '><path d="M3 11.5L12 4l9 7.5"/><path d="M6 10.5V20h12v-9.5"/></svg>',
		'about'    => '<svg ' . $attrs . '><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5"/><path d="M14.5 14.2c1.7-.4 3.5.2 4.5 1.8"/></svg>',
		'services' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="3"/><path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.1 5.1l1.6 1.6M17.3 17.3l1.6 1.6M18.9 5.1l-1.6 1.6M6.7 17.3l-1.6 1.6"/></svg>',
		'mission'  => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v2.5M12 18.5V21M3 12h2.5M18.5 12H21"/></svg>',
		'blog'     => '<svg ' . $attrs . '><path d="M6 4h9l3 3v13H6z"/><path d="M15 4v3h3"/><path d="M9 11h6M9 15h6M9 19h3"/></svg>',
		'support'  => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M9.5 9.2a2.6 2.6 0 015 1c0 1.5-2.5 2-2.5 3.5"/><circle cx="12" cy="17.2" r="0.8" fill="currentColor" stroke="none"/></svg>',
		'contact'  => '<svg ' . $attrs . '><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
		'pricing'  => '<svg ' . $attrs . '><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1.5" fill="currentColor" stroke="none"/></svg>',
		/* Services / trust (legacy keys) */
		'code'      => '<svg ' . $attrs . '><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
		'wordpress' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M3.5 9.5h17M3.5 14.5h17"/><path d="M12 3c-2.5 3-2.5 15 0 18M12 3c2.5 3 2.5 15 0 18"/></svg>',
		'design'    => '<svg ' . $attrs . '><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
		'strategy'  => '<svg ' . $attrs . '><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15l3-4 3 2 4-6"/></svg>',
		'shield'    => '<svg ' . $attrs . '><path d="M12 3l8 3v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg>',
		'ai'        => '<svg ' . $attrs . '><rect x="8" y="8" width="8" height="8" rx="1.5"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9L17 7M7 17l-2.1 2.1"/></svg>',
		'ethics'    => '<svg ' . $attrs . '><path d="M12 3v18"/><path d="M5 8h14"/><path d="M5 8l-2.5 5.5h5L5 8z"/><path d="M19 8l-2.5 5.5h5L19 8z"/><circle cx="12" cy="8" r="1.25" fill="currentColor" stroke="none"/></svg>',
		'collab'    => '<svg ' . $attrs . '><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5"/><path d="M14.5 14.2c1.7-.4 3.5.2 4.5 1.8"/></svg>',
		'clock'     => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
		/* About / Mission pillars */
		'eye'       => '<svg ' . $attrs . '><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>',
		'heart'     => '<svg ' . $attrs . '><path d="M12 20s-7-4.4-7-9.2A3.8 3.8 0 0112 7.5a3.8 3.8 0 017 3.3C19 15.6 12 20 12 20z"/></svg>',
		'building'  => '<svg ' . $attrs . '><path d="M4 20V8l8-4 8 4v12"/><path d="M9 20v-5h6v5"/><path d="M9 10h.01M12 10h.01M15 10h.01M9 14h.01M12 14h.01M15 14h.01"/></svg>',
		/* Mission practice cards */
		'stop'      => '<svg ' . $attrs . '><path d="M4 5h16l-6.5 7.5V19l-3 1.5v-8L4 5z"/></svg>',
		'lock'      => '<svg ' . $attrs . '><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/><circle cx="12" cy="16" r="1.25" fill="currentColor" stroke="none"/></svg>',
		'layers'    => '<svg ' . $attrs . '><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 16l9 5 9-5"/></svg>',
		/* Blog share */
		'x'         => '<svg ' . $attrs . '><path d="M5 5l14 14M19 5L5 19"/></svg>',
		'linkedin'  => '<svg ' . $attrs . '><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 11v6M8 8.5v.01M12 17v-4a2 2 0 014 0v4"/></svg>',
		'link'      => '<svg ' . $attrs . '><path d="M10 13a5 5 0 007.07 0l1.41-1.41a5 5 0 00-7.07-7.07L10 5.93"/><path d="M14 11a5 5 0 00-7.07 0L5.52 12.4a5 5 0 007.07 7.07L14 18.07"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Echo a wrapped embossed icon chip (.ogc-icon).
 *
 * @param string $name Icon key (see ogc_get_icon_svg).
 * @param array  $args {
 *     Optional.
 *     @type string $size   sm|md|lg. Default md.
 *     @type string $shape  rounded|circle. Default rounded.
 *     @type string $class  Extra classes on the wrapper.
 * }
 */
function ogc_icon( $name, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'  => 'md',
			'shape' => 'rounded',
			'class' => '',
		)
	);

	get_template_part(
		'template-parts/icon',
		null,
		array(
			'name'  => $name,
			'size'  => $args['size'],
			'shape' => $args['shape'],
			'class' => $args['class'],
		)
	);
}

/**
 * Echo a minimal line-art SVG icon (legacy helper — prefer ogc_icon()).
 *
 * @param string $name Icon key.
 */
function ogc_line_icon( $name ) {
	$svg = ogc_get_icon_svg( $name, 28 );
	if ( $svg ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted static SVG markup.
		echo $svg;
	}
}

/**
 * Map a primary-nav menu item to an icon key.
 *
 * @param object $item WP_Post menu item.
 * @return string Icon key or empty.
 */
function ogc_nav_icon_for_item( $item ) {
	$path = wp_parse_url( isset( $item->url ) ? $item->url : '', PHP_URL_PATH );
	$path = is_string( $path ) ? trim( $path, '/' ) : '';

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' === $path || $path === $home_path ) {
		return 'home';
	}

	$slug = strtolower( basename( $path ) );
	$map  = array(
		'about-ogc-newfinity' => 'about',
		'about'               => 'about',
		'services'            => 'services',
		'pricing'             => 'pricing',
		'our-mission'         => 'mission',
		'mission'             => 'mission',
		'blog'                => 'blog',
		'support'             => 'support',
		'help-support'        => 'support',
		'contact'             => 'contact',
	);

	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}

	$title = strtolower( trim( isset( $item->title ) ? $item->title : '' ) );
	$title_map = array(
		'home'            => 'home',
		'about us'        => 'about',
		'our services'    => 'services',
		'our mission'     => 'mission',
		'blog & insights' => 'blog',
		'help & support'  => 'support',
		'contact'         => 'contact',
	);

	return isset( $title_map[ $title ] ) ? $title_map[ $title ] : '';
}

/**
 * Prepend embossed icons to primary nav link titles.
 *
 * @param string   $title Menu item title.
 * @param WP_Post  $item  Menu item.
 * @param stdClass $args  wp_nav_menu() args.
 * @param int      $depth Depth.
 * @return string
 */
function ogc_nav_menu_item_title( $title, $item, $args, $depth ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $title;
	}

	$icon = ogc_nav_icon_for_item( $item );
	if ( ! $icon ) {
		return $title;
	}

	ob_start();
	ogc_icon(
		$icon,
		array(
			'size'  => 'sm',
			'class' => 'ogc-nav__icon',
		)
	);
	$chip = ob_get_clean();

	return $chip . '<span class="ogc-nav__label">' . esc_html( wp_strip_all_tags( $title ) ) . '</span>';
}
add_filter( 'nav_menu_item_title', 'ogc_nav_menu_item_title', 10, 4 );

if ( ! function_exists( 'ogc_primary_nav_items' ) ) {
	/**
	 * Fixed primary-nav item set, keyed by the slugs used in Theme Options → Header → Nav order.
	 *
	 * Labels and paths come from ogc_nav_slug_map() (the same map the admin
	 * ordering control renders) so the two cannot drift; the array below is the
	 * fallback for when Theme Options is unavailable. Each nav slug doubles as
	 * its ogc_get_icon_svg() key.
	 *
	 * @return array<string, array{label: string, url: string, icon: string}>
	 */
	function ogc_primary_nav_items() {
		$map = function_exists( 'ogc_nav_slug_map' )
			? ogc_nav_slug_map()
			: array(
				'home'     => array( 'label' => __( 'Home', 'ogc-newfinity' ), 'path' => '/' ),
				'about'    => array( 'label' => __( 'About Us', 'ogc-newfinity' ), 'path' => '/about-ogc-newfinity/' ),
				'services' => array( 'label' => __( 'Our Services', 'ogc-newfinity' ), 'path' => '/services/' ),
				'pricing'  => array( 'label' => __( 'Pricing', 'ogc-newfinity' ), 'path' => '/pricing/' ),
				'mission'  => array( 'label' => __( 'Our Mission', 'ogc-newfinity' ), 'path' => '/our-mission/' ),
				'blog'     => array( 'label' => __( 'Blog & Insights', 'ogc-newfinity' ), 'path' => '/blog/' ),
				'support'  => array( 'label' => __( 'Help & Support', 'ogc-newfinity' ), 'path' => '/support/' ),
				'contact'  => array( 'label' => __( 'Contact', 'ogc-newfinity' ), 'path' => '/contact/' ),
			);

		$items = array();
		foreach ( $map as $slug => $row ) {
			$items[ $slug ] = array(
				'label' => isset( $row['label'] ) ? (string) $row['label'] : '',
				'url'   => home_url( isset( $row['path'] ) ? $row['path'] : '/' ),
				'icon'  => $slug,
			);
		}

		return $items;
	}
}

if ( ! function_exists( 'ogc_primary_nav_current_slug' ) ) {
	/**
	 * Nav slug matching the current request, for current-menu-item styling.
	 *
	 * @return string Slug from ogc_primary_nav_items(), or '' when nothing matches.
	 */
	function ogc_primary_nav_current_slug() {
		if ( is_front_page() ) {
			return 'home';
		}
		if ( is_home() || is_singular( 'post' ) || is_category() || is_tag() ) {
			return 'blog';
		}
		if ( is_page( 'about-ogc-newfinity' ) ) {
			return 'about';
		}
		if ( is_page( 'services' ) ) {
			return 'services';
		}
		if ( is_page( 'pricing' ) ) {
			return 'pricing';
		}
		if ( is_page( 'our-mission' ) ) {
			return 'mission';
		}
		if ( is_page( 'support' ) ) {
			return 'support';
		}
		if ( is_page( 'contact' ) ) {
			return 'contact';
		}

		return '';
	}
}

if ( ! function_exists( 'ogc_render_primary_nav' ) ) {
	/**
	 * Echo the primary nav list from the Theme Options nav order.
	 *
	 * Mirrors the markup wp_nav_menu() produces for this theme
	 * (ul.ogc-nav__list > li.menu-item > a > icon chip + .ogc-nav__label)
	 * so header.css needs no branch for the two rendering paths.
	 * Falls back to the full ordered item set when no order is stored.
	 */
	function ogc_render_primary_nav() {
		$items = ogc_primary_nav_items();
		$order = function_exists( 'ogc_get_nav_order' ) ? ogc_get_nav_order( false ) : array();

		if ( ! is_array( $order ) || empty( $order ) ) {
			$order = function_exists( 'ogc_default_nav_order_json' )
				? ogc_decode_json_list( ogc_default_nav_order_json() )
				: array();
		}

		if ( empty( $order ) ) {
			foreach ( array_keys( $items ) as $slug ) {
				$order[] = array(
					'slug'    => $slug,
					'visible' => true,
				);
			}
		}

		$current = ogc_primary_nav_current_slug();

		echo '<ul id="ogc-primary-menu" class="ogc-nav__list">';

		foreach ( $order as $entry ) {
			if ( is_object( $entry ) ) {
				$entry = (array) $entry;
			}

			if ( is_string( $entry ) ) {
				$entry = array( 'slug' => $entry );
			}

			if ( ! is_array( $entry ) || empty( $entry['slug'] ) ) {
				continue;
			}

			$slug = sanitize_key( $entry['slug'] );
			if ( ! isset( $items[ $slug ] ) ) {
				continue;
			}

			$visible = array_key_exists( 'visible', $entry ) ? (bool) $entry['visible'] : true;
			if ( ! $visible ) {
				continue;
			}

			$item    = $items[ $slug ];
			$classes = array( 'menu-item', 'menu-item-' . $slug );

			if ( $slug === $current ) {
				$classes[] = 'current-menu-item';
				$classes[] = 'current_page_item';
			}

			printf(
				'<li class="%1$s"><a href="%2$s"%3$s>',
				esc_attr( implode( ' ', $classes ) ),
				esc_url( $item['url'] ),
				$slug === $current ? ' aria-current="page"' : ''
			);

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
}

/**
 * Whether the future Amy Agent plugin is active and owning Submit Your Idea UI.
 *
 * Theme default: false (manual form). The Amy Agent plugin may override this
 * (e.g. via a later redefinition or filter) so it returns true. See
 * docs/09-amy-agent-plugin-scope.md.
 *
 * @return bool
 */
function ogc_amy_agent_is_active() {
	/**
	 * Filter whether Amy Agent should replace the Submit Your Idea manual form.
	 *
	 * @param bool $active Default false in the theme.
	 */
	return (bool) apply_filters( 'ogc_amy_agent_is_active', false );
}

