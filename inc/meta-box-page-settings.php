<?php
/**
 * OGC Page Settings meta box — hero background, layout, visibility, subtitle.
 *
 * Registered on Pages only (add_meta_boxes_page). Stores all values as post meta.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default TRBL sides payload (Elementor-style linked dimensions).
 *
 * @param int $value Initial value for every side.
 * @return array{top:int,right:int,bottom:int,left:int,linked:int,unit:string}
 */
function ogc_page_settings_sides_defaults( $value = 0 ) {
	$value = max( 0, (int) $value );
	return array(
		'top'    => $value,
		'right'  => $value,
		'bottom' => $value,
		'left'   => $value,
		'linked' => 1,
		'unit'   => 'px',
	);
}

/**
 * Parse stored sides meta (legacy int or JSON) into a sides array.
 *
 * @param mixed $raw      Raw meta.
 * @param int   $fallback Fallback numeric value.
 * @return array{top:int,right:int,bottom:int,left:int,linked:int,unit:string}
 */
function ogc_page_settings_parse_sides( $raw, $fallback = 0 ) {
	if ( is_array( $raw ) ) {
		$data = $raw;
	} elseif ( is_string( $raw ) && '' !== $raw && ( '{' === $raw[0] || '[' === $raw[0] ) ) {
		$decoded = json_decode( $raw, true );
		$data    = is_array( $decoded ) ? $decoded : null;
	} elseif ( is_numeric( $raw ) ) {
		return ogc_page_settings_sides_defaults( (int) $raw );
	} else {
		$data = null;
	}

	if ( ! is_array( $data ) ) {
		return ogc_page_settings_sides_defaults( $fallback );
	}

	$unit = isset( $data['unit'] ) ? sanitize_key( (string) $data['unit'] ) : 'px';
	if ( ! in_array( $unit, array( 'px', '%', 'em', 'rem' ), true ) ) {
		$unit = 'px';
	}

	return array(
		'top'    => max( 0, (int) ( isset( $data['top'] ) ? $data['top'] : $fallback ) ),
		'right'  => max( 0, (int) ( isset( $data['right'] ) ? $data['right'] : $fallback ) ),
		'bottom' => max( 0, (int) ( isset( $data['bottom'] ) ? $data['bottom'] : $fallback ) ),
		'left'   => max( 0, (int) ( isset( $data['left'] ) ? $data['left'] : $fallback ) ),
		'linked' => empty( $data['linked'] ) ? 0 : 1,
		'unit'   => $unit,
	);
}

/**
 * Sanitize sides payload from POST or stored JSON.
 *
 * @param mixed                $raw   Raw value.
 * @param array<string, mixed> $attrs Optional min/max.
 * @return array{top:int,right:int,bottom:int,left:int,linked:int,unit:string}
 */
function ogc_page_settings_sanitize_sides( $raw, $attrs = array() ) {
	if ( is_string( $raw ) && '' !== $raw && ( '{' === $raw[0] || '[' === $raw[0] ) ) {
		$raw = json_decode( $raw, true );
	}
	if ( is_numeric( $raw ) ) {
		$raw = ogc_page_settings_sides_defaults( (int) $raw );
	}
	if ( ! is_array( $raw ) ) {
		$raw = array();
	}

	$min = isset( $attrs['min'] ) ? (int) $attrs['min'] : 0;
	$max = isset( $attrs['max'] ) ? (int) $attrs['max'] : 200;

	$sides = array();
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		$val           = isset( $raw[ $side ] ) ? (int) $raw[ $side ] : 0;
		$sides[ $side ] = max( $min, min( $max, $val ) );
	}

	$linked = ! empty( $raw['linked'] ) ? 1 : 0;
	if ( $linked ) {
		$sides['right']  = $sides['top'];
		$sides['bottom'] = $sides['top'];
		$sides['left']   = $sides['top'];
	}

	$unit = isset( $raw['unit'] ) ? sanitize_key( (string) $raw['unit'] ) : 'px';
	if ( ! in_array( $unit, array( 'px', '%', 'em', 'rem' ), true ) ) {
		$unit = 'px';
	}

	$sides['linked'] = $linked;
	$sides['unit']   = $unit;
	return $sides;
}

/**
 * Build a CSS TRBL length list from a sides array.
 *
 * @param array{top:int,right:int,bottom:int,left:int,unit?:string} $sides Sides.
 * @return string
 */
function ogc_page_settings_sides_css( $sides ) {
	$sides = ogc_page_settings_parse_sides( $sides );
	$unit  = $sides['unit'];
	return sprintf(
		'%1$d%5$s %2$d%5$s %3$d%5$s %4$d%5$s',
		$sides['top'],
		$sides['right'],
		$sides['bottom'],
		$sides['left'],
		$unit
	);
}

/**
 * Default box-shadow payload.
 *
 * @return array<string, mixed>
 */
function ogc_page_settings_shadow_defaults() {
	return array(
		'enabled' => 0,
		'h'       => 0,
		'v'       => 0,
		'blur'    => 10,
		'spread'  => 0,
		'color'   => '#000000',
		'opacity' => 50,
	);
}

/**
 * Parse stored box-shadow meta.
 *
 * @param mixed $raw Raw meta.
 * @return array<string, mixed>
 */
function ogc_page_settings_parse_shadow( $raw ) {
	$defaults = ogc_page_settings_shadow_defaults();
	if ( is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
		$raw     = is_array( $decoded ) ? $decoded : array();
	}
	if ( ! is_array( $raw ) ) {
		return $defaults;
	}
	return array(
		'enabled' => empty( $raw['enabled'] ) ? 0 : 1,
		'h'       => (int) ( isset( $raw['h'] ) ? $raw['h'] : $defaults['h'] ),
		'v'       => (int) ( isset( $raw['v'] ) ? $raw['v'] : $defaults['v'] ),
		'blur'    => max( 0, (int) ( isset( $raw['blur'] ) ? $raw['blur'] : $defaults['blur'] ) ),
		'spread'  => (int) ( isset( $raw['spread'] ) ? $raw['spread'] : $defaults['spread'] ),
		'color'   => sanitize_hex_color( isset( $raw['color'] ) ? (string) $raw['color'] : '' ) ?: $defaults['color'],
		'opacity' => max( 0, min( 100, (int) ( isset( $raw['opacity'] ) ? $raw['opacity'] : $defaults['opacity'] ) ) ),
	);
}

/**
 * Sanitize box-shadow payload.
 *
 * @param mixed $raw Raw value.
 * @return array<string, mixed>
 */
function ogc_page_settings_sanitize_shadow( $raw ) {
	return ogc_page_settings_parse_shadow( $raw );
}

/**
 * CSS box-shadow value from shadow payload.
 *
 * @param array<string, mixed>|string $shadow Shadow data.
 * @return string
 */
function ogc_page_settings_shadow_css( $shadow ) {
	$s = ogc_page_settings_parse_shadow( $shadow );
	if ( empty( $s['enabled'] ) ) {
		return 'none';
	}
	$hex = ltrim( (string) $s['color'], '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		$hex = '000000';
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	$a = $s['opacity'] / 100;
	return sprintf(
		'%dpx %dpx %dpx %dpx rgba(%d, %d, %d, %s)',
		(int) $s['h'],
		(int) $s['v'],
		(int) $s['blur'],
		(int) $s['spread'],
		$r,
		$g,
		$b,
		function_exists( 'ogc_css_num' ) ? ogc_css_num( $a ) : (string) $a
	);
}

/**
 * Hero Background + page settings field definitions (mirrors former Theme Options schema).
 *
 * @return array<string, array<string, mixed>>
 */
function ogc_page_settings_hero_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}

	$fit_options = array(
		'cover'   => __( 'Cover', 'ogc-newfinity' ),
		'contain' => __( 'Contain', 'ogc-newfinity' ),
		'custom'  => __( 'Custom size', 'ogc-newfinity' ),
	);
	$position_options = array(
		'center'       => __( 'Center', 'ogc-newfinity' ),
		'top'          => __( 'Top', 'ogc-newfinity' ),
		'bottom'       => __( 'Bottom', 'ogc-newfinity' ),
		'left'         => __( 'Left', 'ogc-newfinity' ),
		'right'        => __( 'Right', 'ogc-newfinity' ),
		'top-left'     => __( 'Top left', 'ogc-newfinity' ),
		'top-right'    => __( 'Top right', 'ogc-newfinity' ),
		'bottom-left'  => __( 'Bottom left', 'ogc-newfinity' ),
		'bottom-right' => __( 'Bottom right', 'ogc-newfinity' ),
	);
	$repeat_options = array(
		'no-repeat' => __( 'No repeat', 'ogc-newfinity' ),
		'repeat'    => __( 'Repeat', 'ogc-newfinity' ),
		'repeat-x'  => __( 'Repeat X', 'ogc-newfinity' ),
		'repeat-y'  => __( 'Repeat Y', 'ogc-newfinity' ),
	);
	$border_style_options = array(
		'default' => __( 'Default', 'ogc-newfinity' ),
		'solid'   => __( 'Solid', 'ogc-newfinity' ),
		'dashed'  => __( 'Dashed', 'ogc-newfinity' ),
		'dotted'  => __( 'Dotted', 'ogc-newfinity' ),
		'double'  => __( 'Double', 'ogc-newfinity' ),
	);
	$anim_options = array(
		'none'      => __( 'None', 'ogc-newfinity' ),
		'parallax'  => __( 'Parallax drift', 'ogc-newfinity' ),
		'fade-in'   => __( 'Fade in', 'ogc-newfinity' ),
		'ken-burns' => __( 'Ken Burns', 'ogc-newfinity' ),
	);
	$blend_mode_options = array(
		'normal'     => __( 'Normal', 'ogc-newfinity' ),
		'multiply'   => __( 'Multiply', 'ogc-newfinity' ),
		'screen'     => __( 'Screen', 'ogc-newfinity' ),
		'overlay'    => __( 'Overlay', 'ogc-newfinity' ),
		'soft-light' => __( 'Soft Light', 'ogc-newfinity' ),
		'luminosity' => __( 'Luminosity', 'ogc-newfinity' ),
	);

	$fields = array(
		'_ogc_hero_bg_image'           => array(
			'type'     => 'image',
			'label'    => __( 'Background image', 'ogc-newfinity' ),
			'tooltip'  => __( 'Additive layer behind the existing hero canvas. Leave empty to keep the WebGL hero untouched.', 'ogc-newfinity' ),
			'default'  => '',
			'sanitize' => 'ogc_sanitize_image_url',
		),
		'_ogc_hero_bg_fit'             => array(
			'type'     => 'select',
			'label'    => __( 'Image fit', 'ogc-newfinity' ),
			'default'  => 'cover',
			'options'  => $fit_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_width'           => array(
			'type'     => 'text',
			'label'    => __( 'Custom width', 'ogc-newfinity' ),
			'tooltip'  => __( 'Only used when Image fit is Custom. Accepts px or % (e.g. 100% or 800px).', 'ogc-newfinity' ),
			'default'  => '100%',
			'sanitize' => 'ogc_sanitize_css_length',
			'attrs'    => array( 'placeholder' => '100%' ),
			'show_when_fit' => 'custom',
		),
		'_ogc_hero_bg_height'          => array(
			'type'     => 'text',
			'label'    => __( 'Custom height', 'ogc-newfinity' ),
			'tooltip'  => __( 'Only used when Image fit is Custom. Accepts px or % (e.g. auto or 600px).', 'ogc-newfinity' ),
			'default'  => 'auto',
			'sanitize' => 'ogc_sanitize_css_length',
			'attrs'    => array( 'placeholder' => 'auto' ),
			'show_when_fit' => 'custom',
		),
		'_ogc_hero_bg_position'        => array(
			'type'     => 'select',
			'label'    => __( 'Position', 'ogc-newfinity' ),
			'default'  => 'center',
			'options'  => $position_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_repeat'          => array(
			'type'     => 'select',
			'label'    => __( 'Repeat', 'ogc-newfinity' ),
			'default'  => 'no-repeat',
			'options'  => $repeat_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_border_width'    => array(
			'type'     => 'sides',
			'label'    => __( 'Border Width', 'ogc-newfinity' ),
			'default'  => ogc_page_settings_sides_defaults( 0 ),
			'attrs'    => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
			'sanitize' => 'ogc_page_settings_sanitize_sides',
		),
		'_ogc_hero_bg_border_color'    => array(
			'type'     => 'color',
			'label'    => __( 'Border Color', 'ogc-newfinity' ),
			'default'  => OGC_DEFAULT_COLOR_ACCENT_START,
			'sanitize' => 'ogc_sanitize_hex_color',
		),
		'_ogc_hero_bg_border_style'    => array(
			'type'     => 'select',
			'label'    => __( 'Border Type', 'ogc-newfinity' ),
			'default'  => 'default',
			'options'  => $border_style_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_border_radius'   => array(
			'type'     => 'sides',
			'label'    => __( 'Border Radius', 'ogc-newfinity' ),
			'default'  => ogc_page_settings_sides_defaults( 0 ),
			'attrs'    => array( 'min' => 0, 'max' => 200, 'step' => 1 ),
			'sanitize' => 'ogc_page_settings_sanitize_sides',
		),
		'_ogc_hero_bg_box_shadow'      => array(
			'type'     => 'shadow',
			'label'    => __( 'Box Shadow', 'ogc-newfinity' ),
			'default'  => ogc_page_settings_shadow_defaults(),
			'sanitize' => 'ogc_page_settings_sanitize_shadow',
		),
		'_ogc_hero_bg_blur'            => array(
			'type'     => 'range',
			'label'    => __( 'Blur', 'ogc-newfinity' ),
			'default'  => 0,
			'attrs'    => array( 'min' => 0, 'max' => 20, 'step' => 1, 'unit' => 'px' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_grayscale'       => array(
			'type'     => 'range',
			'label'    => __( 'Grayscale', 'ogc-newfinity' ),
			'default'  => 0,
			'attrs'    => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_overlay_color'   => array(
			'type'     => 'color',
			'label'    => __( 'Overlay color', 'ogc-newfinity' ),
			'tooltip'  => __( 'Leave empty for no overlay. Opacity below only applies when a color is set.', 'ogc-newfinity' ),
			'default'  => '',
			'sanitize' => 'ogc_sanitize_hex_color',
			'allow_empty' => true,
		),
		'_ogc_hero_bg_overlay_opacity' => array(
			'type'     => 'range',
			'label'    => __( 'Overlay opacity', 'ogc-newfinity' ),
			'default'  => 0,
			'attrs'    => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_brightness'      => array(
			'type'     => 'range',
			'label'    => __( 'Brightness', 'ogc-newfinity' ),
			'default'  => 100,
			'attrs'    => array( 'min' => 50, 'max' => 150, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_contrast'        => array(
			'type'     => 'range',
			'label'    => __( 'Contrast', 'ogc-newfinity' ),
			'default'  => 100,
			'attrs'    => array( 'min' => 50, 'max' => 150, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_saturation'      => array(
			'type'     => 'range',
			'label'    => __( 'Saturation', 'ogc-newfinity' ),
			'default'  => 100,
			'attrs'    => array( 'min' => 0, 'max' => 200, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_noise'           => array(
			'type'     => 'range',
			'label'    => __( 'Noise', 'ogc-newfinity' ),
			'tooltip'  => __( 'Adds a subtle grain texture over the background image.', 'ogc-newfinity' ),
			'default'  => 0,
			'attrs'    => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_vignette'        => array(
			'type'     => 'range',
			'label'    => __( 'Vignette', 'ogc-newfinity' ),
			'tooltip'  => __( 'Darkens the edges of the background image toward the center.', 'ogc-newfinity' ),
			'default'  => 0,
			'attrs'    => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
		'_ogc_hero_bg_blend_mode'      => array(
			'type'     => 'select',
			'label'    => __( 'Blend Mode', 'ogc-newfinity' ),
			'tooltip'  => __( 'Controls how the overlay color blends with the background image.', 'ogc-newfinity' ),
			'default'  => 'normal',
			'options'  => $blend_mode_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_animation'       => array(
			'type'     => 'select',
			'label'    => __( 'Animation', 'ogc-newfinity' ),
			'tooltip'  => __( 'Disabled automatically when reduced-motion is preferred or orbit animation is turned off in Buttons & Effects.', 'ogc-newfinity' ),
			'default'  => 'none',
			'options'  => $anim_options,
			'sanitize' => 'sanitize_text_field',
		),
		'_ogc_hero_bg_opacity'         => array(
			'type'     => 'range',
			'label'    => __( 'Hero Background Opacity', 'ogc-newfinity' ),
			'tooltip'  => __( 'Only applies when a hero background image is set for this page. Separate from overlay opacity.', 'ogc-newfinity' ),
			'default'  => 35,
			'attrs'    => array( 'min' => 0, 'max' => 100, 'step' => 1, 'unit' => '%' ),
			'sanitize' => 'ogc_sanitize_int',
		),
	);

	return $fields;
}

/**
 * Layout select choices.
 *
 * @return array<string, string>
 */
function ogc_page_layout_choices() {
	return array(
		'default'               => __( 'Default', 'ogc-newfinity' ),
		'full_width_contained'  => __( 'Full Width Contained', 'ogc-newfinity' ),
		'full_width_stretched'  => __( 'Full Width Stretched', 'ogc-newfinity' ),
		'boxed'                 => __( 'Boxed Content', 'ogc-newfinity' ),
	);
}

/**
 * Register the meta box on the Page editor (normal / high — Yoast-adjacent).
 *
 * @param WP_Post $post Post object.
 */
function ogc_register_page_settings_meta_box( $post ) {
	add_meta_box(
		'ogc_page_settings',
		__( 'OGC Page Settings', 'ogc-newfinity' ),
		'ogc_render_page_settings_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'ogc_register_page_settings_meta_box' );

/**
 * Enqueue meta box assets on the Page editor screen.
 *
 * @param string $hook_suffix Current admin page.
 */
function ogc_page_settings_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'ogc-admin-page-settings',
		OGC_THEME_URI . '/assets/css/admin-page-settings.css',
		array(),
		OGC_THEME_VERSION
	);
	wp_enqueue_script(
		'ogc-admin-page-settings',
		OGC_THEME_URI . '/assets/js/admin-page-settings.js',
		array( 'jquery' ),
		OGC_THEME_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ogc_page_settings_admin_assets' );

/**
 * Read a page-settings meta value with default.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param mixed  $default Default.
 * @return mixed
 */
function ogc_page_settings_get_meta( $post_id, $key, $default = '' ) {
	$raw = get_post_meta( $post_id, $key, true );
	if ( '' === $raw || false === $raw || null === $raw ) {
		return $default;
	}
	return $raw;
}

/**
 * Open a collapsible settings card.
 *
 * @param string $id       Unique card id (slug).
 * @param string $title    Card title.
 * @param string $summary  Optional one-line status shown when collapsed.
 * @param bool   $open     Whether the card starts expanded.
 * @param string $icon     Optional dashicons class for nested hero cards.
 * @param int    $number   Optional ordinal badge (nested hero cards only).
 */
function ogc_page_settings_card_open( $id, $title, $summary = '', $open = false, $icon = '', $number = 0 ) {
	?>
	<details class="ogc-ps-card" id="ogc-ps-card-<?php echo esc_attr( $id ); ?>" data-card="<?php echo esc_attr( $id ); ?>"<?php echo $open ? ' open' : ''; ?>>
		<summary class="ogc-ps-card__summary">
			<span class="ogc-ps-card__chevron" aria-hidden="true"></span>
			<?php if ( $number > 0 ) : ?>
				<span class="ogc-ps-card__badge-num"><?php echo (int) $number; ?></span>
				<?php if ( $icon ) : ?>
					<span class="dashicons <?php echo esc_attr( $icon ); ?> ogc-ps-card__icon" aria-hidden="true"></span>
				<?php endif; ?>
			<?php endif; ?>
			<span class="ogc-ps-card__title"><?php echo esc_html( $title ); ?></span>
			<?php if ( '' !== $summary ) : ?>
				<span class="ogc-ps-card__badge"><?php echo esc_html( $summary ); ?></span>
			<?php endif; ?>
		</summary>
		<div class="ogc-ps-card__body">
	<?php
}

/**
 * Close a collapsible settings card.
 */
function ogc_page_settings_card_close() {
	echo '</div></details>';
}

/**
 * Render a group of hero fields by meta-key list.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $hero    Field definitions.
 * @param string[]             $keys    Meta keys to render.
 */
function ogc_page_settings_render_hero_group( $post_id, $hero, $keys ) {
	foreach ( $keys as $key ) {
		if ( ! isset( $hero[ $key ] ) ) {
			continue;
		}
		if ( '_ogc_hero_bg_overlay_color' === $key ) {
			$overlay_set = '' !== (string) ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_overlay_color', '' );
			?>
			<div class="ogc-page-settings__row ogc-page-settings__row--toggle">
				<label class="ogc-page-settings__check ogc-ps-toggle-row">
					<span class="ogc-ps-toggle">
						<input
							type="checkbox"
							id="ogc-ps-overlay-enable"
							data-default-color="<?php echo esc_attr( OGC_DEFAULT_COLOR_ACCENT_START ); ?>"
							<?php checked( $overlay_set ); ?>
						>
						<span class="ogc-ps-toggle__track"></span>
					</span>
					<span><?php esc_html_e( 'Enable overlay', 'ogc-newfinity' ); ?></span>
				</label>
			</div>
			<?php
		}
		$value = ogc_page_settings_get_meta( $post_id, $key, $hero[ $key ]['default'] );
		ogc_page_settings_render_field( $key, $hero[ $key ], $value );
	}
}

/**
 * Render the OGC Page Settings meta box.
 *
 * @param WP_Post $post Current page.
 */
function ogc_render_page_settings_meta_box( $post ) {
	wp_nonce_field( 'ogc_save_page_settings', 'ogc_page_settings_nonce' );

	$post_id = (int) $post->ID;
	$hero    = ogc_page_settings_hero_fields();
	$layout  = (string) ogc_page_settings_get_meta( $post_id, '_ogc_page_layout', 'default' );
	$enable_widgets = (int) ogc_page_settings_get_meta( $post_id, '_ogc_page_enable_widget_area', 0 );
	$subtitle = (string) ogc_page_settings_get_meta( $post_id, '_ogc_page_subtitle', '' );

	$hide_title    = (int) ogc_page_settings_get_meta( $post_id, '_ogc_hide_page_title', 0 );
	$hide_footer   = (int) ogc_page_settings_get_meta( $post_id, '_ogc_hide_footer', 0 );
	$hide_widgets  = (int) ogc_page_settings_get_meta( $post_id, '_ogc_hide_widget_area', 0 );
	$hide_featured = (int) ogc_page_settings_get_meta( $post_id, '_ogc_hide_featured_image', 0 );

	$hero_image = (string) ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_image', '' );
	$layout_choices = ogc_page_layout_choices();
	$layout_label   = isset( $layout_choices[ $layout ] ) ? $layout_choices[ $layout ] : $layout_choices['default'];

	$hide_count = $hide_title + $hide_footer + $hide_widgets + $hide_featured;
	$hero_badge = $hero_image
		? __( 'Image set', 'ogc-newfinity' )
		: __( 'No image', 'ogc-newfinity' );
	$layout_badge = $enable_widgets
		? sprintf(
			/* translators: %s: layout label */
			__( '%s · Widgets on', 'ogc-newfinity' ),
			$layout_label
		)
		: $layout_label;
	$visibility_badge = $hide_count
		? sprintf(
			/* translators: %d: number of hidden elements */
			_n( '%d hidden', '%d hidden', $hide_count, 'ogc-newfinity' ),
			$hide_count
		)
		: __( 'All visible', 'ogc-newfinity' );
	$subtitle_badge = $subtitle
		? wp_html_excerpt( $subtitle, 36, '…' )
		: __( 'None', 'ogc-newfinity' );
	?>
	<div class="ogc-page-settings" id="ogc-page-settings">

		<?php ogc_page_settings_card_open( 'hero', __( 'Hero Background', 'ogc-newfinity' ), $hero_badge, (bool) $hero_image ); ?>
			<p class="ogc-page-settings__intro"><?php esc_html_e( 'Additive layer behind the existing hero canvas. Leave the image empty to keep the WebGL hero untouched.', 'ogc-newfinity' ); ?></p>

			<div class="ogc-ps-card-stack">
				<?php ogc_page_settings_card_open( 'hero-image', __( 'Image', 'ogc-newfinity' ), '', true, 'dashicons-format-image', 1 ); ?>
					<?php ogc_page_settings_render_hero_group( $post_id, $hero, array( '_ogc_hero_bg_image' ) ); ?>
				<?php ogc_page_settings_card_close(); ?>

				<?php ogc_page_settings_card_open( 'hero-size', __( 'Size & Position', 'ogc-newfinity' ), '', false, 'dashicons-editor-expand', 2 ); ?>
					<?php
					ogc_page_settings_render_hero_group(
						$post_id,
						$hero,
						array(
							'_ogc_hero_bg_fit',
							'_ogc_hero_bg_width',
							'_ogc_hero_bg_height',
							'_ogc_hero_bg_position',
							'_ogc_hero_bg_repeat',
						)
					);
					?>
				<?php ogc_page_settings_card_close(); ?>

				<?php ogc_page_settings_card_open( 'hero-border', __( 'Border', 'ogc-newfinity' ), '', false, 'dashicons-admin-appearance', 3 ); ?>
					<?php ogc_page_settings_render_border_panel( $post_id ); ?>
				<?php ogc_page_settings_card_close(); ?>

				<?php ogc_page_settings_card_open( 'hero-effects', __( 'Effects', 'ogc-newfinity' ), '', false, 'dashicons-art', 4 ); ?>
					<?php
					ogc_page_settings_render_hero_group(
						$post_id,
						$hero,
						array(
							'_ogc_hero_bg_blur',
							'_ogc_hero_bg_grayscale',
							'_ogc_hero_bg_overlay_color',
							'_ogc_hero_bg_overlay_opacity',
							'_ogc_hero_bg_blend_mode',
							'_ogc_hero_bg_brightness',
							'_ogc_hero_bg_contrast',
							'_ogc_hero_bg_saturation',
							'_ogc_hero_bg_noise',
							'_ogc_hero_bg_vignette',
						)
					);
					?>
				<?php ogc_page_settings_card_close(); ?>

				<?php ogc_page_settings_card_open( 'hero-animation', __( 'Animation & Opacity', 'ogc-newfinity' ), '', false, 'dashicons-controls-play', 5 ); ?>
					<?php
					ogc_page_settings_render_hero_group(
						$post_id,
						$hero,
						array(
							'_ogc_hero_bg_animation',
							'_ogc_hero_bg_opacity',
						)
					);
					?>
				<?php ogc_page_settings_card_close(); ?>
			</div>
		<?php ogc_page_settings_card_close(); ?>

		<?php ogc_page_settings_card_open( 'layout', __( 'Page Layout', 'ogc-newfinity' ), $layout_badge ); ?>
			<div class="ogc-page-settings__row">
				<label class="ogc-page-settings__label" for="_ogc_page_layout"><?php esc_html_e( 'Layout', 'ogc-newfinity' ); ?></label>
				<select id="_ogc_page_layout" name="_ogc_page_layout" class="ogc-page-settings__select">
					<?php foreach ( $layout_choices as $opt_value => $opt_label ) : ?>
						<option value="<?php echo esc_attr( $opt_value ); ?>" <?php selected( $layout, $opt_value ); ?>>
							<?php echo esc_html( $opt_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="ogc-page-settings__row">
				<label class="ogc-page-settings__check">
					<input type="checkbox" id="_ogc_page_enable_widget_area" name="_ogc_page_enable_widget_area" value="1" <?php checked( $enable_widgets, 1 ); ?>>
					<span><?php esc_html_e( 'Enable Widget Area', 'ogc-newfinity' ); ?></span>
				</label>
				<p class="ogc-page-settings__help"><?php esc_html_e( 'Shows the theme’s Blog Sidebar widget area beside this page’s content. Add widgets under Appearance → Widgets.', 'ogc-newfinity' ); ?></p>
			</div>
		<?php ogc_page_settings_card_close(); ?>

		<?php ogc_page_settings_card_open( 'visibility', __( 'Visibility', 'ogc-newfinity' ), $visibility_badge ); ?>
			<div class="ogc-page-settings__checks">
				<div class="ogc-page-settings__row">
					<label class="ogc-page-settings__check">
						<input type="checkbox" name="_ogc_hide_page_title" value="1" <?php checked( $hide_title, 1 ); ?>>
						<span><?php esc_html_e( 'Hide Page Title', 'ogc-newfinity' ); ?></span>
					</label>
				</div>
				<div class="ogc-page-settings__row">
					<label class="ogc-page-settings__check">
						<input type="checkbox" name="_ogc_hide_footer" value="1" <?php checked( $hide_footer, 1 ); ?>>
						<span><?php esc_html_e( 'Hide Footer', 'ogc-newfinity' ); ?></span>
					</label>
				</div>
				<div class="ogc-page-settings__row" id="ogc-ps-hide-widget-row">
					<label class="ogc-page-settings__check">
						<input type="checkbox" id="_ogc_hide_widget_area" name="_ogc_hide_widget_area" value="1" <?php checked( $hide_widgets, 1 ); ?> <?php disabled( ! $enable_widgets ); ?>>
						<span><?php esc_html_e( 'Hide Widget Area', 'ogc-newfinity' ); ?></span>
					</label>
				</div>
				<div class="ogc-page-settings__row">
					<label class="ogc-page-settings__check">
						<input type="checkbox" name="_ogc_hide_featured_image" value="1" <?php checked( $hide_featured, 1 ); ?>>
						<span><?php esc_html_e( 'Hide Featured Image', 'ogc-newfinity' ); ?></span>
					</label>
				</div>
			</div>
		<?php ogc_page_settings_card_close(); ?>

		<?php ogc_page_settings_card_open( 'subtitle', __( 'Page Subtitle', 'ogc-newfinity' ), $subtitle_badge ); ?>
			<div class="ogc-page-settings__row">
				<label class="ogc-page-settings__label" for="_ogc_page_subtitle"><?php esc_html_e( 'Subtitle', 'ogc-newfinity' ); ?></label>
				<input
					type="text"
					id="_ogc_page_subtitle"
					name="_ogc_page_subtitle"
					class="ogc-page-settings__input widefat"
					value="<?php echo esc_attr( $subtitle ); ?>"
					maxlength="160"
				>
				<p class="ogc-page-settings__help"><?php esc_html_e( 'Displayed under the page title in the hero area. This is NOT the SEO meta description.', 'ogc-newfinity' ); ?></p>
			</div>
		<?php ogc_page_settings_card_close(); ?>

	</div>
	<?php
}

/**
 * Render Elementor-style Border controls (type / width / color / radius / shadow).
 *
 * @param int $post_id Page ID.
 */
function ogc_page_settings_render_border_panel( $post_id ) {
	$hero   = ogc_page_settings_hero_fields();
	$style  = (string) ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_border_style', 'default' );
	$color  = (string) ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_border_color', OGC_DEFAULT_COLOR_ACCENT_START );
	$width  = ogc_page_settings_parse_sides( ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_border_width', 0 ), 0 );
	$radius = ogc_page_settings_parse_sides( ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_border_radius', 0 ), 0 );
	$shadow = ogc_page_settings_parse_shadow( ogc_page_settings_get_meta( $post_id, '_ogc_hero_bg_box_shadow', array() ) );
	$style_options = $hero['_ogc_hero_bg_border_style']['options'];
	$has_border    = ( 'default' !== $style && 'none' !== $style );
	?>
	<div class="ogc-ps-border" data-border-panel>
		<div class="ogc-ps-border__row">
			<span class="ogc-ps-border__label"><?php esc_html_e( 'Border Type', 'ogc-newfinity' ); ?></span>
			<div class="ogc-ps-border__control">
				<div class="ogc-ps-segmented" role="group" aria-label="<?php esc_attr_e( 'Border Type', 'ogc-newfinity' ); ?>">
					<?php foreach ( $style_options as $opt_value => $opt_label ) : ?>
						<button
							type="button"
							class="ogc-ps-segmented__btn<?php echo ( $style === $opt_value ) ? ' is-active' : ''; ?>"
							data-border-type-btn
							data-value="<?php echo esc_attr( $opt_value ); ?>"
						><?php echo esc_html( $opt_label ); ?></button>
					<?php endforeach; ?>
					<input type="hidden" id="_ogc_hero_bg_border_style" name="_ogc_hero_bg_border_style" value="<?php echo esc_attr( $style ); ?>" data-border-type>
				</div>
			</div>
		</div>

		<div class="ogc-ps-border__dependent" data-border-dependent<?php echo $has_border ? '' : ' hidden'; ?>>
			<?php
			ogc_page_settings_render_dimensions_control(
				'_ogc_hero_bg_border_width',
				__( 'Border Width', 'ogc-newfinity' ),
				$width,
				array( 'min' => 0, 'max' => 40 )
			);
			?>

			<div class="ogc-ps-border__row">
				<span class="ogc-ps-border__label"><?php esc_html_e( 'Color', 'ogc-newfinity' ); ?></span>
				<div class="ogc-ps-border__control ogc-ps-border__control--color">
					<input type="color" class="ogc-page-settings__color-swatch" value="<?php echo esc_attr( $color ? $color : OGC_DEFAULT_COLOR_ACCENT_START ); ?>" data-sync="_ogc_hero_bg_border_color" aria-label="<?php esc_attr_e( 'Border Color', 'ogc-newfinity' ); ?>">
					<input type="text" id="_ogc_hero_bg_border_color" name="_ogc_hero_bg_border_color" class="ogc-page-settings__color-text" value="<?php echo esc_attr( $color ); ?>" spellcheck="false" autocomplete="off">
				</div>
			</div>
		</div>

		<?php
		ogc_page_settings_render_dimensions_control(
			'_ogc_hero_bg_border_radius',
			__( 'Border Radius', 'ogc-newfinity' ),
			$radius,
			array( 'min' => 0, 'max' => 200 ),
			true
		);
		?>

		<div class="ogc-ps-border__row">
			<span class="ogc-ps-border__label"><?php esc_html_e( 'Box Shadow', 'ogc-newfinity' ); ?></span>
			<div class="ogc-ps-border__control">
				<button
					type="button"
					class="ogc-ps-border__icon-btn<?php echo ! empty( $shadow['enabled'] ) ? ' is-active' : ''; ?>"
					data-shadow-toggle
					aria-expanded="<?php echo ! empty( $shadow['enabled'] ) ? 'true' : 'false'; ?>"
					aria-controls="ogc-ps-shadow-panel"
					title="<?php esc_attr_e( 'Edit box shadow', 'ogc-newfinity' ); ?>"
				>
					<span class="dashicons dashicons-edit" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Edit box shadow', 'ogc-newfinity' ); ?></span>
				</button>
			</div>
		</div>

		<div class="ogc-ps-shadow-panel" id="ogc-ps-shadow-panel" data-shadow-panel<?php echo ! empty( $shadow['enabled'] ) ? '' : ' hidden'; ?>>
			<input type="hidden" name="_ogc_hero_bg_box_shadow[enabled]" value="<?php echo ! empty( $shadow['enabled'] ) ? '1' : '0'; ?>" data-shadow-enabled>
			<div class="ogc-ps-shadow-panel__grid">
				<label>
					<span><?php esc_html_e( 'Horizontal', 'ogc-newfinity' ); ?></span>
					<input type="number" name="_ogc_hero_bg_box_shadow[h]" value="<?php echo esc_attr( (string) $shadow['h'] ); ?>" step="1">
				</label>
				<label>
					<span><?php esc_html_e( 'Vertical', 'ogc-newfinity' ); ?></span>
					<input type="number" name="_ogc_hero_bg_box_shadow[v]" value="<?php echo esc_attr( (string) $shadow['v'] ); ?>" step="1">
				</label>
				<label>
					<span><?php esc_html_e( 'Blur', 'ogc-newfinity' ); ?></span>
					<input type="number" name="_ogc_hero_bg_box_shadow[blur]" value="<?php echo esc_attr( (string) $shadow['blur'] ); ?>" min="0" step="1">
				</label>
				<label>
					<span><?php esc_html_e( 'Spread', 'ogc-newfinity' ); ?></span>
					<input type="number" name="_ogc_hero_bg_box_shadow[spread]" value="<?php echo esc_attr( (string) $shadow['spread'] ); ?>" step="1">
				</label>
			</div>
			<div class="ogc-ps-border__row ogc-ps-border__row--nested">
				<span class="ogc-ps-border__label"><?php esc_html_e( 'Color', 'ogc-newfinity' ); ?></span>
				<div class="ogc-ps-border__control ogc-ps-border__control--color">
					<input type="color" class="ogc-page-settings__color-swatch" value="<?php echo esc_attr( $shadow['color'] ); ?>" data-sync="_ogc_hero_bg_box_shadow_color" aria-label="<?php esc_attr_e( 'Shadow color', 'ogc-newfinity' ); ?>">
					<input type="text" id="_ogc_hero_bg_box_shadow_color" name="_ogc_hero_bg_box_shadow[color]" class="ogc-page-settings__color-text" value="<?php echo esc_attr( $shadow['color'] ); ?>" spellcheck="false" autocomplete="off">
				</div>
			</div>
			<div class="ogc-ps-border__row ogc-ps-border__row--nested">
				<span class="ogc-ps-border__label"><?php esc_html_e( 'Opacity', 'ogc-newfinity' ); ?></span>
				<div class="ogc-ps-border__control ogc-page-settings__range">
					<input type="range" name="_ogc_hero_bg_box_shadow[opacity]" value="<?php echo esc_attr( (string) $shadow['opacity'] ); ?>" min="0" max="100" step="1" data-range-output="ogc-ps-shadow-opacity-out">
					<output id="ogc-ps-shadow-opacity-out" class="ogc-page-settings__range-output"><?php echo esc_html( (string) (int) $shadow['opacity'] ); ?>%</output>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render a linked TRBL dimensions control (Top / Right / Bottom / Left).
 *
 * @param string               $name   Base input name / meta key.
 * @param string               $label  Field label.
 * @param array<string, mixed> $sides  Parsed sides.
 * @param array<string, int>   $attrs  Min/max.
 * @param bool                 $show_unit Whether to show unit select.
 */
function ogc_page_settings_render_dimensions_control( $name, $label, $sides, $attrs = array(), $show_unit = true ) {
	$min    = isset( $attrs['min'] ) ? (int) $attrs['min'] : 0;
	$max    = isset( $attrs['max'] ) ? (int) $attrs['max'] : 200;
	$linked = ! empty( $sides['linked'] );
	$unit   = isset( $sides['unit'] ) ? $sides['unit'] : 'px';
	$corners = array(
		'top'    => __( 'Top', 'ogc-newfinity' ),
		'right'  => __( 'Right', 'ogc-newfinity' ),
		'bottom' => __( 'Bottom', 'ogc-newfinity' ),
		'left'   => __( 'Left', 'ogc-newfinity' ),
	);
	?>
	<div class="ogc-ps-border__block" data-dimensions="<?php echo esc_attr( $name ); ?>">
		<div class="ogc-ps-border__row ogc-ps-border__row--dimensions-head">
			<span class="ogc-ps-border__label">
				<?php echo esc_html( $label ); ?>
				<span class="dashicons dashicons-desktop ogc-ps-border__device" aria-hidden="true" title="<?php esc_attr_e( 'Desktop', 'ogc-newfinity' ); ?>"></span>
			</span>
			<?php if ( $show_unit ) : ?>
				<div class="ogc-ps-border__control">
					<select class="ogc-ps-border__unit" name="<?php echo esc_attr( $name ); ?>[unit]" aria-label="<?php esc_attr_e( 'Unit', 'ogc-newfinity' ); ?>">
						<?php foreach ( array( 'px', '%', 'em', 'rem' ) as $u ) : ?>
							<option value="<?php echo esc_attr( $u ); ?>" <?php selected( $unit, $u ); ?>><?php echo esc_html( $u ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php else : ?>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>[unit]" value="px">
			<?php endif; ?>
		</div>
		<div class="ogc-ps-dimensions">
			<div class="ogc-ps-dimensions__inputs">
				<?php foreach ( $corners as $side => $side_label ) : ?>
					<label class="ogc-ps-dimensions__cell">
						<input
							type="number"
							class="ogc-ps-dimensions__input"
							name="<?php echo esc_attr( $name ); ?>[<?php echo esc_attr( $side ); ?>]"
							value="<?php echo esc_attr( (string) $sides[ $side ] ); ?>"
							min="<?php echo esc_attr( (string) $min ); ?>"
							max="<?php echo esc_attr( (string) $max ); ?>"
							step="1"
							data-side="<?php echo esc_attr( $side ); ?>"
							<?php echo ( $linked && 'top' !== $side ) ? ' readonly' : ''; ?>
						>
						<span class="ogc-ps-dimensions__side"><?php echo esc_html( $side_label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
			<button
				type="button"
				class="ogc-ps-dimensions__link<?php echo $linked ? ' is-linked' : ''; ?>"
				data-dimensions-link
				aria-pressed="<?php echo $linked ? 'true' : 'false'; ?>"
				title="<?php esc_attr_e( 'Link values', 'ogc-newfinity' ); ?>"
			>
				<span class="dashicons dashicons-admin-links" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Link values', 'ogc-newfinity' ); ?></span>
			</button>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>[linked]" value="<?php echo $linked ? '1' : '0'; ?>" data-dimensions-linked>
		</div>
	</div>
	<?php
}

/**
 * Render one hero field control.
 *
 * @param string               $key   Meta key / input name.
 * @param array<string, mixed> $field Field definition.
 * @param mixed                $value Current value.
 */
function ogc_page_settings_render_field( $key, $field, $value ) {
	$type  = $field['type'];
	$attrs = isset( $field['attrs'] ) && is_array( $field['attrs'] ) ? $field['attrs'] : array();
	$row_attrs = '';
	if ( ! empty( $field['show_when_fit'] ) ) {
		$row_attrs = ' data-show-when-fit="' . esc_attr( $field['show_when_fit'] ) . '"';
	}
	?>
	<div class="ogc-page-settings__row ogc-page-settings__row--<?php echo esc_attr( $type ); ?>" data-field="<?php echo esc_attr( $key ); ?>"<?php echo $row_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<label class="ogc-page-settings__label" for="<?php echo esc_attr( $key ); ?>">
			<?php echo esc_html( $field['label'] ); ?>
		</label>
		<?php if ( ! empty( $field['tooltip'] ) ) : ?>
			<p class="ogc-page-settings__help"><?php echo esc_html( $field['tooltip'] ); ?></p>
		<?php endif; ?>
		<div class="ogc-page-settings__control">
			<?php
			switch ( $type ) {
				case 'image':
					?>
					<div class="ogc-page-settings__image" data-image-field>
						<input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
						<div class="ogc-page-settings__image-preview"<?php echo $value ? '' : ' hidden'; ?>>
							<img src="<?php echo esc_url( $value ); ?>" alt="">
						</div>
						<div class="ogc-page-settings__image-actions">
							<button type="button" class="button button-primary" data-image-select<?php echo $value ? ' hidden' : ''; ?>><?php esc_html_e( 'Choose image', 'ogc-newfinity' ); ?></button>
							<button type="button" class="button" data-image-replace<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Replace', 'ogc-newfinity' ); ?></button>
							<button type="button" class="button" data-image-remove<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'ogc-newfinity' ); ?></button>
						</div>
					</div>
					<?php
					break;

				case 'select':
					echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" class="ogc-page-settings__select">';
					foreach ( $field['options'] as $opt_value => $opt_label ) {
						printf(
							'<option value="%1$s"%2$s>%3$s</option>',
							esc_attr( $opt_value ),
							selected( (string) $value, (string) $opt_value, false ),
							esc_html( $opt_label )
						);
					}
					echo '</select>';
					break;

				case 'text':
					printf(
						'<input type="text" id="%1$s" name="%1$s" class="ogc-page-settings__input" value="%2$s"%3$s>',
						esc_attr( $key ),
						esc_attr( $value ),
						isset( $attrs['placeholder'] ) ? ' placeholder="' . esc_attr( $attrs['placeholder'] ) . '"' : ''
					);
					break;

				case 'number':
					printf(
						'<div class="ogc-page-settings__number"><input type="number" id="%1$s" name="%1$s" class="ogc-page-settings__input" value="%2$s" min="%3$s" max="%4$s" step="%5$s">%6$s</div>',
						esc_attr( $key ),
						esc_attr( (string) (int) $value ),
						esc_attr( isset( $attrs['min'] ) ? $attrs['min'] : 0 ),
						esc_attr( isset( $attrs['max'] ) ? $attrs['max'] : 100 ),
						esc_attr( isset( $attrs['step'] ) ? $attrs['step'] : 1 ),
						isset( $attrs['unit'] ) ? '<span class="ogc-page-settings__unit">' . esc_html( $attrs['unit'] ) . '</span>' : ''
					);
					break;

				case 'range':
					$unit = isset( $attrs['unit'] ) ? $attrs['unit'] : '';
					printf(
						'<div class="ogc-page-settings__range"><input type="range" id="%1$s" name="%1$s" value="%2$d" min="%3$s" max="%4$s" step="%5$s" data-range-output="%1$s-output"><output id="%1$s-output" class="ogc-page-settings__range-output">%2$d%6$s</output></div>',
						esc_attr( $key ),
						(int) $value,
						esc_attr( isset( $attrs['min'] ) ? $attrs['min'] : 0 ),
						esc_attr( isset( $attrs['max'] ) ? $attrs['max'] : 100 ),
						esc_attr( isset( $attrs['step'] ) ? $attrs['step'] : 1 ),
						esc_html( $unit )
					);
					break;

				case 'color':
					$display = $value ? $value : ( ! empty( $field['allow_empty'] ) ? '#000000' : $field['default'] );
					printf(
						'<div class="ogc-page-settings__color"><input type="color" class="ogc-page-settings__color-swatch" value="%1$s" data-sync="%2$s" aria-label="%3$s">'
						. '<input type="text" id="%2$s" name="%2$s" class="ogc-page-settings__color-text" value="%4$s" spellcheck="false" autocomplete="off" placeholder="%5$s">'
						. '</div>',
						esc_attr( $display ),
						esc_attr( $key ),
						esc_attr( $field['label'] ),
						esc_attr( $value ),
						esc_attr( ! empty( $field['allow_empty'] ) ? __( 'Empty = none', 'ogc-newfinity' ) : '' )
					);
					break;
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Sanitize and clamp a hero field value.
 *
 * @param array<string, mixed> $field Field definition.
 * @param mixed                $value Raw value.
 * @return mixed
 */
function ogc_page_settings_sanitize_field( $field, $value ) {
	if ( 'sides' === $field['type'] ) {
		$attrs = isset( $field['attrs'] ) && is_array( $field['attrs'] ) ? $field['attrs'] : array();
		return ogc_page_settings_sanitize_sides( $value, $attrs );
	}

	if ( 'shadow' === $field['type'] ) {
		return ogc_page_settings_sanitize_shadow( $value );
	}

	if ( ! empty( $field['sanitize'] ) && is_callable( $field['sanitize'] ) ) {
		$value = call_user_func( $field['sanitize'], $value );
	}

	if ( 'select' === $field['type'] && ! empty( $field['options'] ) && ! array_key_exists( (string) $value, $field['options'] ) ) {
		$value = $field['default'];
	}

	if ( in_array( $field['type'], array( 'number', 'range' ), true ) ) {
		$attrs = isset( $field['attrs'] ) ? $field['attrs'] : array();
		$value = (int) $value;
		if ( isset( $attrs['min'] ) ) {
			$value = max( (int) $attrs['min'], $value );
		}
		if ( isset( $attrs['max'] ) ) {
			$value = min( (int) $attrs['max'], $value );
		}
	}

	if ( 'color' === $field['type'] && '' === (string) $value && empty( $field['allow_empty'] ) ) {
		$value = $field['default'];
	}

	return $value;
}

/**
 * Save page settings from the meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function ogc_save_page_settings( $post_id, $post ) {
	if ( ! isset( $_POST['ogc_page_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ogc_page_settings_nonce'] ) ), 'ogc_save_page_settings' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return;
	}

	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	foreach ( ogc_page_settings_hero_fields() as $key => $field ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : $field['default']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		$val = ogc_page_settings_sanitize_field( $field, $raw );
		if ( in_array( $field['type'], array( 'sides', 'shadow' ), true ) ) {
			$val = wp_json_encode( $val );
		}
		update_post_meta( $post_id, $key, $val );
	}

	$layout_choices = ogc_page_layout_choices();
	$layout         = isset( $_POST['_ogc_page_layout'] ) ? sanitize_text_field( wp_unslash( $_POST['_ogc_page_layout'] ) ) : 'default';
	if ( ! array_key_exists( $layout, $layout_choices ) ) {
		$layout = 'default';
	}
	update_post_meta( $post_id, '_ogc_page_layout', $layout );

	update_post_meta( $post_id, '_ogc_page_enable_widget_area', ! empty( $_POST['_ogc_page_enable_widget_area'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_ogc_hide_page_title', ! empty( $_POST['_ogc_hide_page_title'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_ogc_hide_footer', ! empty( $_POST['_ogc_hide_footer'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_ogc_hide_widget_area', ! empty( $_POST['_ogc_hide_widget_area'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_ogc_hide_featured_image', ! empty( $_POST['_ogc_hide_featured_image'] ) ? 1 : 0 );

	$subtitle = isset( $_POST['_ogc_page_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['_ogc_page_subtitle'] ) ) : '';
	update_post_meta( $post_id, '_ogc_page_subtitle', $subtitle );
}
add_action( 'save_post_page', 'ogc_save_page_settings', 10, 2 );
