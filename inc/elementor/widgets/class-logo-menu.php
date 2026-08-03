<?php
/**
 * Elementor widget: OGC Logo/Menu.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme logo + primary nav for Elementor-built headers.
 */
class OGC_Elementor_Widget_Logo_Menu extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_logo_menu';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Logo/Menu Widget', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'show_logo',
			array(
				'label'        => __( 'Show logo', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_menu',
			array(
				'label'        => __( 'Show primary menu', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_cta',
			array(
				'label'        => __( 'Show Submit Idea CTA', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'ogc-newfinity' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Nav link color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_accent_start(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-nav a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .ogc-nav a',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s               = $this->get_settings_for_display();
		$ogc_header_logo = function_exists( 'ogc_get_header_logo_url' ) ? ogc_get_header_logo_url() : '';
		$logo_w          = max( 16, (int) get_theme_mod( 'ogc_header_logo_width_desktop', 40 ) );
		?>
		<div class="ogc-el-logo-menu">
			<?php if ( ! empty( $s['show_logo'] ) && 'yes' === $s['show_logo'] && function_exists( 'ogc_get_logo' ) ) : ?>
				<div class="ogc-header__brand ogc-el-logo-menu__brand">
					<?php
					ogc_get_logo(
						array(
							'class'  => 'ogc-header__logo',
							'src'    => $ogc_header_logo,
							'width'  => (int) round( $logo_w * 21 / 20 ),
							'height' => $logo_w,
							'lazy'   => false,
							'alt'    => 'OGC NewFinity logo',
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $s['show_menu'] ) && 'yes' === $s['show_menu'] ) : ?>
				<nav class="ogc-nav ogc-el-logo-menu__nav" aria-label="<?php esc_attr_e( 'Primary', 'ogc-newfinity' ); ?>">
					<?php
					if ( function_exists( 'ogc_get_nav_order' ) && function_exists( 'ogc_render_primary_nav' ) ) {
						ogc_render_primary_nav();
					} else {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'ogc-nav__list',
								'menu_id'        => 'ogc-el-primary-menu-' . $this->get_id(),
								'depth'          => 1,
								'fallback_cb'    => function_exists( 'ogc_primary_nav_fallback' ) ? 'ogc_primary_nav_fallback' : false,
							)
						);
					}
					?>
				</nav>
			<?php endif; ?>

			<?php if ( ! empty( $s['show_cta'] ) && 'yes' === $s['show_cta'] ) : ?>
				<div class="ogc-el-logo-menu__cta">
					<a class="ogc-btn ogc-header__cta" href="<?php echo esc_url( home_url( '/submit-idea/' ) ); ?>">
						<?php esc_html_e( 'Submit Idea', 'ogc-newfinity' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
