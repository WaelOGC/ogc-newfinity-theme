<?php
/**
 * Elementor widget: OGC Footer Meta Block.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Live contact/location meta — same source of truth as footer.php.
 */
class OGC_Elementor_Widget_Footer_Meta extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_footer_meta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Footer Meta Block', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-sitemap';
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
			'info',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Shows the live Theme Options contact email and location — same values as the site footer. No manual text entry.', 'ogc-newfinity' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'show_location',
			array(
				'label'        => __( 'Show location', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_email',
			array(
				'label'        => __( 'Show email', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'text_color',
			array(
				'label'     => __( 'Text color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-el-footer-meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Link color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_accent_start(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-el-footer-meta a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .ogc-el-footer-meta',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s        = $this->get_settings_for_display();
		$location = function_exists( 'ogc_get_location_text' ) ? ogc_get_location_text() : '';
		$email    = function_exists( 'ogc_get_contact_email' ) ? ogc_get_contact_email() : '';
		?>
		<div class="ogc-el-footer-meta">
			<ul class="ogc-footer__contact ogc-el-footer-meta__list">
				<?php if ( ! empty( $s['show_location'] ) && 'yes' === $s['show_location'] && $location ) : ?>
					<li class="ogc-footer__contact-item">
						<?php if ( function_exists( 'ogc_footer_icon' ) ) : ?>
							<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'map' ); ?></span>
						<?php endif; ?>
						<span><?php echo esc_html( $location ); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( ! empty( $s['show_email'] ) && 'yes' === $s['show_email'] && $email ) : ?>
					<li class="ogc-footer__contact-item">
						<?php if ( function_exists( 'ogc_footer_icon' ) ) : ?>
							<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'mail' ); ?></span>
						<?php endif; ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}
}
