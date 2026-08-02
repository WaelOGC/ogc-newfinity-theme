<?php
/**
 * Customizer bridge — live-preview UI over the same theme_mods as Theme Options.
 *
 * One source of truth: every control reads/writes the dashboard schema keys from
 * inc/theme-options.php. Grouped collapsible subsections + (?) tooltips mirror
 * the OGC NewFinity admin layout.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer accordion group heading control.
 */
class OGC_Customize_Group_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'ogc_group';

	/**
	 * Group id used by customize-controls.js to show/hide sibling fields.
	 *
	 * @var string
	 */
	public $group_id = '';

	/**
	 * Whether the group starts expanded.
	 *
	 * @var bool
	 */
	public $expanded = true;

	/**
	 * Expose group metadata to the controls JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['group_id'] = $this->group_id;
		$this->json['expanded'] = (bool) $this->expanded;
	}

	/**
	 * Render the accordion toggle.
	 */
	public function render_content() {
		$expanded = $this->expanded ? 'true' : 'false';
		?>
		<button
			type="button"
			class="ogc-cz-group<?php echo $this->expanded ? ' is-open' : ''; ?>"
			aria-expanded="<?php echo esc_attr( $expanded ); ?>"
			data-ogc-group="<?php echo esc_attr( $this->group_id ); ?>"
		>
			<span class="ogc-cz-group__label"><?php echo esc_html( $this->label ); ?></span>
			<span class="ogc-cz-group__chevron dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
		</button>
		<?php
	}
}

/**
 * Footer link columns builder — label + URL inputs (same JSON theme_mod as the dashboard).
 */
class OGC_Customize_Footer_Columns_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'ogc_footer_columns';

	/**
	 * Expose parsed columns to the controls JS.
	 */
	public function to_json() {
		parent::to_json();
		$decoded = json_decode( (string) $this->value(), true );
		$this->json['columns'] = is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Shell markup; rows are built in customize-controls.js.
	 */
	public function render_content() {
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<div class="ogc-cz-footer-cols" data-ogc-footer-cols></div>
		<textarea
			class="ogc-cz-footer-cols__store"
			<?php $this->link(); ?>
			hidden
			rows="1"
		><?php echo esc_textarea( $this->value() ); ?></textarea>
		<?php
	}
}

/**
 * Dashboard URL for a Theme Options tab.
 *
 * @param string $tab Tab id.
 * @return string
 */
function ogc_customize_dashboard_link( $tab ) {
	return admin_url( 'admin.php?page=' . OGC_OPTIONS_PAGE_SLUG . '&tab=' . rawurlencode( $tab ) );
}

/**
 * Section description HTML with link back to the matching dashboard tab.
 *
 * @param string $tab   Dashboard tab id.
 * @param string $label Link label.
 * @return string
 */
function ogc_customize_section_desc( $tab, $label = '' ) {
	if ( '' === $label ) {
		$label = __( 'Open full options in OGC NewFinity →', 'ogc-newfinity' );
	}
	return sprintf(
		'<p class="ogc-cz-dash-link"><a href="%1$s">%2$s</a></p>',
		esc_url( ogc_customize_dashboard_link( $tab ) ),
		esc_html( $label )
	);
}

/**
 * Register a collapsible group heading inside a section.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $section     Section id.
 * @param string               $group_id    Group slug.
 * @param string               $label       Visible label.
 * @param int                  $priority    Control priority.
 * @param bool                 $expanded    Start open.
 */
function ogc_customize_add_group( $wp_customize, $section, $group_id, $label, $priority, $expanded = true ) {
	$wp_customize->add_setting(
		'ogc_cz_group_' . $section . '_' . $group_id,
		array(
			'default'   => '',
			'transport' => 'postMessage',
			'type'      => 'theme_mod',
		)
	);
	$wp_customize->add_control(
		new OGC_Customize_Group_Control(
			$wp_customize,
			'ogc_cz_group_' . $section . '_' . $group_id,
			array(
				'label'    => $label,
				'section'  => $section,
				'priority' => $priority,
				'group_id' => $group_id,
				'expanded' => $expanded,
			)
		)
	);
}

/**
 * Register one setting + control from a Theme Options schema field.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $key          theme_mod / schema key.
 * @param string               $section      Section id.
 * @param string               $group_id     Accordion group id.
 * @param int                  $priority     Control priority.
 * @param array                $overrides    Optional control/setting overrides.
 * @return bool True when registered.
 */
function ogc_customize_add_schema_field( $wp_customize, $key, $section, $group_id, $priority, $overrides = array() ) {
	$field = function_exists( 'ogc_theme_options_field' ) ? ogc_theme_options_field( $key ) : null;
	if ( ! $field ) {
		return false;
	}

	$type      = $field['type'];
	$transport = ( 'image' === $type ) ? 'refresh' : 'postMessage';
	if ( in_array( $type, array( 'footer_columns', 'nav_order', 'ads_config', 'monospace' ), true ) ) {
		$transport = 'refresh';
	}
	if ( ! empty( $overrides['transport'] ) ) {
		$transport = $overrides['transport'];
	}

	$sanitize = ! empty( $field['sanitize'] ) ? $field['sanitize'] : 'sanitize_text_field';

	$wp_customize->add_setting(
		$key,
		array(
			'default'           => isset( $field['default'] ) ? $field['default'] : '',
			'sanitize_callback' => $sanitize,
			'transport'         => $transport,
			'type'              => 'theme_mod',
		)
	);

	$label = ! empty( $overrides['label'] ) ? $overrides['label'] : $field['label'];

	$control_args = array(
		'label'       => $label,
		'section'     => $section,
		'priority'    => $priority,
		'settings'    => $key,
		'description' => '', // Tooltips render as (?) — not permanent description text.
	);

	$attrs = isset( $field['attrs'] ) && is_array( $field['attrs'] ) ? $field['attrs'] : array();
	if ( ! empty( $attrs ) ) {
		$control_args['input_attrs'] = array_intersect_key(
			$attrs,
			array_flip( array( 'min', 'max', 'step', 'placeholder', 'maxlength' ) )
		);
	}

	switch ( $type ) {
		case 'color':
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$key,
					$control_args
				)
			);
			break;

		case 'image':
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					$key,
					$control_args
				)
			);
			break;

		case 'checkbox':
			$control_args['type'] = 'checkbox';
			$wp_customize->add_control( $key, $control_args );
			break;

		case 'select':
			$control_args['type']    = 'select';
			$control_args['choices'] = ! empty( $field['options'] ) ? $field['options'] : array();
			// Font maps store Google URL fragments as values — use keys as labels.
			if ( in_array( $key, array( 'ogc_font_heading', 'ogc_font_body' ), true ) && ! empty( $field['options'] ) ) {
				$names = array_keys( $field['options'] );
				$control_args['choices'] = array_combine( $names, $names );
			}
			$wp_customize->add_control( $key, $control_args );
			break;

		case 'range':
			$control_args['type'] = 'range';
			$wp_customize->add_control( $key, $control_args );
			break;

		case 'number':
			$control_args['type'] = 'number';
			$wp_customize->add_control( $key, $control_args );
			break;

		case 'textarea':
			$control_args['type'] = 'textarea';
			$wp_customize->add_control( $key, $control_args );
			break;

		case 'footer_columns':
			$wp_customize->add_control(
				new OGC_Customize_Footer_Columns_Control(
					$wp_customize,
					$key,
					$control_args
				)
			);
			break;

		case 'email':
		case 'url':
		case 'text':
		default:
			$control_args['type'] = ( 'email' === $type || 'url' === $type ) ? $type : 'text';
			$wp_customize->add_control( $key, $control_args );
			break;
	}

	$control = $wp_customize->get_control( $key );
	if ( $control ) {
		$control->ogc_group   = $group_id;
		$control->ogc_tooltip = ! empty( $field['tooltip'] ) ? $field['tooltip'] : '';
	}

	return true;
}

/**
 * Collect control metadata for the controls JS (groups + tooltips).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @return array{groups:array<string,string>,tooltips:array<string,string>}
 */
function ogc_customize_control_meta( $wp_customize ) {
	$groups   = array();
	$tooltips = array();
	foreach ( $wp_customize->controls() as $id => $control ) {
		if ( isset( $control->ogc_group ) && '' !== $control->ogc_group ) {
			$groups[ $id ] = $control->ogc_group;
		}
		if ( ! empty( $control->ogc_tooltip ) ) {
			$tooltips[ $id ] = $control->ogc_tooltip;
		}
	}
	return array(
		'groups'   => $groups,
		'tooltips' => $tooltips,
	);
}

/**
 * Register all OGC Customizer panels / sections / controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function ogc_customize_bridge_register( $wp_customize ) {
	$pages = array(
		'home'     => __( 'Home', 'ogc-newfinity' ),
		'about'    => __( 'About', 'ogc-newfinity' ),
		'services' => __( 'Services', 'ogc-newfinity' ),
		'mission'  => __( 'Mission', 'ogc-newfinity' ),
	);

	/* ============================================================ Hero BGs */
	$wp_customize->add_panel(
		'ogc_hero_backgrounds',
		array(
			'title'       => __( 'OGC Hero Backgrounds', 'ogc-newfinity' ),
			'description' => __( 'Live preview for hero background images. Same settings as OGC NewFinity → Images / Pages.', 'ogc-newfinity' ),
			'priority'    => 40,
		)
	);

	foreach ( $pages as $page_key => $page_label ) {
		$section = 'ogc_hero_bg_section_' . $page_key;
		$wp_customize->add_section(
			$section,
			array(
				'title'       => sprintf(
					/* translators: %s: page name. */
					__( '%s hero', 'ogc-newfinity' ),
					$page_label
				),
				'panel'       => 'ogc_hero_backgrounds',
				'description' => ogc_customize_section_desc( 'images', __( 'Edit all hero background options →', 'ogc-newfinity' ) ),
			)
		);

		$p = 10;
		ogc_customize_add_group( $wp_customize, $section, 'image', __( 'Image', 'ogc-newfinity' ), $p++, true );
		ogc_customize_add_schema_field( $wp_customize, 'ogc_hero_bg_' . $page_key, $section, 'image', $p++ );

		ogc_customize_add_group( $wp_customize, $section, 'size', __( 'Size & Position', 'ogc-newfinity' ), $p++, false );
		foreach ( array( 'fit', 'width', 'height', 'position', 'repeat' ) as $slug ) {
			ogc_customize_add_schema_field( $wp_customize, "ogc_hero_bg_{$slug}_{$page_key}", $section, 'size', $p++ );
		}

		ogc_customize_add_group( $wp_customize, $section, 'border', __( 'Border', 'ogc-newfinity' ), $p++, false );
		foreach ( array( 'border_width', 'border_color', 'border_style', 'border_radius' ) as $slug ) {
			ogc_customize_add_schema_field( $wp_customize, "ogc_hero_bg_{$slug}_{$page_key}", $section, 'border', $p++ );
		}

		ogc_customize_add_group( $wp_customize, $section, 'effects', __( 'Effects', 'ogc-newfinity' ), $p++, false );
		foreach ( array( 'blur', 'grayscale', 'overlay_color', 'overlay_opacity', 'brightness', 'contrast' ) as $slug ) {
			ogc_customize_add_schema_field( $wp_customize, "ogc_hero_bg_{$slug}_{$page_key}", $section, 'effects', $p++ );
		}

		ogc_customize_add_group( $wp_customize, $section, 'animation', __( 'Animation', 'ogc-newfinity' ), $p++, false );
		ogc_customize_add_schema_field( $wp_customize, 'ogc_hero_bg_animation_' . $page_key, $section, 'animation', $p++ );
		ogc_customize_add_schema_field(
			$wp_customize,
			'ogc_hero_bg_opacity_' . $page_key,
			$section,
			'animation',
			$p++,
			array( 'label' => __( 'Layer opacity', 'ogc-newfinity' ) )
		);
	}

	/* ============================================================== Header */
	$wp_customize->add_section(
		'ogc_cz_header',
		array(
			'title'       => __( 'OGC Header', 'ogc-newfinity' ),
			'priority'    => 41,
			'description' => ogc_customize_section_desc( 'header' ),
		)
	);
	$p = 10;
	ogc_customize_add_group( $wp_customize, 'ogc_cz_header', 'logo', __( 'Logo', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_logo', 'ogc_cz_header', 'logo', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_logo_width_desktop', 'ogc_cz_header', 'logo', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_logo_width_mobile', 'ogc_cz_header', 'logo', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_header', 'bar', __( 'Bar', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_bg_color', 'ogc_cz_header', 'bar', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_bg_opacity', 'ogc_cz_header', 'bar', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_text_color', 'ogc_cz_header', 'bar', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_header_sticky', 'ogc_cz_header', 'bar', $p++ );

	/* ============================================================== Footer */
	$wp_customize->add_section(
		'ogc_cz_footer',
		array(
			'title'       => __( 'OGC Footer', 'ogc-newfinity' ),
			'priority'    => 42,
			'description' => ogc_customize_section_desc( 'footer' ),
		)
	);
	$p = 10;
	ogc_customize_add_group( $wp_customize, 'ogc_cz_footer', 'brand', __( 'Brand', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_footer_logo', 'ogc_cz_footer', 'brand', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_footer_logo_width', 'ogc_cz_footer', 'brand', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_footer_bg_color', 'ogc_cz_footer', 'brand', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_footer_tagline', 'ogc_cz_footer', 'brand', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_footer', 'columns', __( 'Link columns', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field(
		$wp_customize,
		'ogc_footer_columns_json',
		'ogc_cz_footer',
		'columns',
		$p++,
		array(
			'label'     => __( 'Footer link columns', 'ogc-newfinity' ),
			'transport' => 'refresh',
		)
	);

	/* ============================================================== Colors */
	$wp_customize->add_section(
		'ogc_cz_colors',
		array(
			'title'       => __( 'OGC Colors', 'ogc-newfinity' ),
			'priority'    => 43,
			'description' => ogc_customize_section_desc( 'colors' ),
		)
	);
	$p = 10;
	ogc_customize_add_group( $wp_customize, 'ogc_cz_colors', 'palette', __( 'Palette', 'ogc-newfinity' ), $p++, true );
	foreach ( array( 'accent_start', 'accent_end', 'bg', 'text_primary', 'text_secondary', 'border' ) as $slug ) {
		ogc_customize_add_schema_field( $wp_customize, 'ogc_color_' . $slug, 'ogc_cz_colors', 'palette', $p++ );
	}

	ogc_customize_add_group( $wp_customize, 'ogc_cz_colors', 'buttons', __( 'Buttons', 'ogc-newfinity' ), $p++, false );
	foreach ( array( 'button_bg_start', 'button_bg_end', 'button_text' ) as $slug ) {
		ogc_customize_add_schema_field( $wp_customize, 'ogc_color_' . $slug, 'ogc_cz_colors', 'buttons', $p++ );
	}
	ogc_customize_add_schema_field( $wp_customize, 'ogc_gradient_angle', 'ogc_cz_colors', 'buttons', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_colors', 'shadows', __( 'Shadows', 'ogc-newfinity' ), $p++, false );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_color_shadow', 'ogc_cz_colors', 'shadows', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_shadow_opacity', 'ogc_cz_colors', 'shadows', $p++ );

	/* ===================================================== Buttons & Motion */
	$wp_customize->add_section(
		'ogc_cz_buttons',
		array(
			'title'       => __( 'OGC Buttons & Motion', 'ogc-newfinity' ),
			'priority'    => 44,
			'description' => ogc_customize_section_desc( 'buttons' ),
		)
	);
	$p = 10;
	ogc_customize_add_group( $wp_customize, 'ogc_cz_buttons', 'shape', __( 'Shape', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_radius', 'ogc_cz_buttons', 'shape', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_shadow_style', 'ogc_cz_buttons', 'shape', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_buttons', 'primary', __( 'Primary button hover', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_effect_primary', 'ogc_cz_buttons', 'primary', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_duration_primary', 'ogc_cz_buttons', 'primary', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_intensity_primary', 'ogc_cz_buttons', 'primary', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_buttons', 'outline', __( 'Outline button hover', 'ogc-newfinity' ), $p++, false );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_effect_outline', 'ogc_cz_buttons', 'outline', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_duration_outline', 'ogc_cz_buttons', 'outline', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_hover_intensity_outline', 'ogc_cz_buttons', 'outline', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_buttons', 'states', __( 'States', 'ogc-newfinity' ), $p++, false );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_active_state', 'ogc_cz_buttons', 'states', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_disabled_style', 'ogc_cz_buttons', 'states', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_button_focus_style', 'ogc_cz_buttons', 'states', $p++ );

	ogc_customize_add_group( $wp_customize, 'ogc_cz_buttons', 'motion', __( 'Motion', 'ogc-newfinity' ), $p++, true );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_transition_speed', 'ogc_cz_buttons', 'motion', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_toggle_orbit_animation', 'ogc_cz_buttons', 'motion', $p++ );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_underline_swoosh_style', 'ogc_cz_buttons', 'motion', $p++ );

	/* =============================================================== Fonts */
	$wp_customize->add_section(
		'ogc_cz_fonts',
		array(
			'title'       => __( 'OGC Fonts', 'ogc-newfinity' ),
			'priority'    => 45,
			'description' => ogc_customize_section_desc( 'fonts' ),
		)
	);
	$p = 10;
	ogc_customize_add_group( $wp_customize, 'ogc_cz_fonts', 'families', __( 'Families', 'ogc-newfinity' ), $p++, true );
	// Font family change needs a refresh so Google Fonts CSS can reload.
	ogc_customize_add_schema_field( $wp_customize, 'ogc_font_heading', 'ogc_cz_fonts', 'families', $p++, array( 'transport' => 'refresh' ) );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_font_body', 'ogc_cz_fonts', 'families', $p++, array( 'transport' => 'refresh' ) );
	ogc_customize_add_schema_field( $wp_customize, 'ogc_font_base_size', 'ogc_cz_fonts', 'families', $p++ );
}
add_action( 'customize_register', 'ogc_customize_bridge_register', 20 );

/**
 * Enqueue Customizer controls UI (accordion + tooltips).
 */
function ogc_customize_bridge_controls_enqueue() {
	global $wp_customize;
	if ( ! $wp_customize instanceof WP_Customize_Manager ) {
		return;
	}

	$meta = ogc_customize_control_meta( $wp_customize );

	wp_enqueue_style(
		'ogc-customize-controls',
		OGC_THEME_URI . '/assets/css/customize-controls.css',
		array(),
		OGC_THEME_VERSION
	);

	wp_enqueue_script(
		'ogc-customize-controls',
		OGC_THEME_URI . '/assets/js/customize-controls.js',
		array( 'customize-controls', 'jquery' ),
		OGC_THEME_VERSION,
		true
	);

	wp_localize_script(
		'ogc-customize-controls',
		'ogcCustomizeControls',
		array(
			'groups'   => $meta['groups'],
			'tooltips' => $meta['tooltips'],
			'i18n'     => array(
				'moreInfo'    => __( 'More info', 'ogc-newfinity' ),
				'columnTitle' => __( 'Column title', 'ogc-newfinity' ),
				'linkLabel'   => __( 'Label', 'ogc-newfinity' ),
				'linkUrl'     => __( 'URL', 'ogc-newfinity' ),
				'addLink'     => __( 'Add link', 'ogc-newfinity' ),
				'addColumn'   => __( 'Add column', 'ogc-newfinity' ),
				'remove'      => __( 'Remove', 'ogc-newfinity' ),
				'removeColumn'=> __( 'Remove column', 'ogc-newfinity' ),
			),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'ogc_customize_bridge_controls_enqueue' );

/**
 * Enqueue live-preview script (iframe).
 */
function ogc_customize_bridge_preview_init() {
	wp_enqueue_script(
		'ogc-customize-preview',
		OGC_THEME_URI . '/assets/js/customize-preview.js',
		array( 'customize-preview', 'jquery' ),
		OGC_THEME_VERSION,
		true
	);

	wp_localize_script(
		'ogc-customize-preview',
		'ogcHeroPreview',
		array(
			'pages'     => array( 'home', 'about', 'services', 'mission' ),
			'positions' => array(
				'center'       => 'center center',
				'top'          => 'center top',
				'bottom'       => 'center bottom',
				'left'         => 'left center',
				'right'        => 'right center',
				'top-left'     => 'left top',
				'top-right'    => 'right top',
				'bottom-left'  => 'left bottom',
				'bottom-right' => 'right bottom',
			),
			'speeds'    => array(
				'fast'   => 120,
				'normal' => 220,
				'slow'   => 350,
			),
			'transitions' => array(
				'fast'   => 150,
				'normal' => 250,
				'slow'   => 400,
			),
		)
	);
}
add_action( 'customize_preview_init', 'ogc_customize_bridge_preview_init' );
