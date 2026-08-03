<?php
/**
 * Elementor widget: OGC Image With Effects.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single image with brand-styled hover / reveal effects.
 */
class OGC_Elementor_Widget_Image_Effects extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_image_effects';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Image With Effects', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image';
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
			'image',
			array(
				'label'   => __( 'Image', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Effect', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'fade-in',
				'options' => array(
					'fade-in'         => __( 'Fade in', 'ogc-newfinity' ),
					'zoom-on-hover'   => __( 'Zoom on hover', 'ogc-newfinity' ),
					'reveal-on-scroll'=> __( 'Reveal on scroll', 'ogc-newfinity' ),
				),
			)
		);

		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Caption', 'ogc-newfinity' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Optional caption', 'ogc-newfinity' ),
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
			'caption_color',
			array(
				'label'     => __( 'Caption color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-el-image-effects__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .ogc-el-image-effects__caption',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$url    = ! empty( $s['image']['url'] ) ? $s['image']['url'] : '';
		$effect = ! empty( $s['effect'] ) ? $s['effect'] : 'fade-in';
		if ( ! $url ) {
			return;
		}
		$classes = array(
			'ogc-el-image-effects',
			'ogc-el-image-effects--' . sanitize_html_class( $effect ),
		);
		?>
		<figure class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-ogc-image-effect="<?php echo esc_attr( $effect ); ?>">
			<div class="ogc-el-image-effects__frame">
				<img
					src="<?php echo esc_url( $url ); ?>"
					alt="<?php echo esc_attr( ! empty( $s['image']['alt'] ) ? $s['image']['alt'] : '' ); ?>"
					loading="lazy"
				>
			</div>
			<?php if ( ! empty( $s['caption'] ) ) : ?>
				<figcaption class="ogc-el-image-effects__caption"><?php echo esc_html( $s['caption'] ); ?></figcaption>
			<?php endif; ?>
		</figure>
		<?php
	}
}
