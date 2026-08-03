<?php
/**
 * Elementor widget: OGC Slider (vanilla JS).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lightweight brand-styled image/content slider.
 */
class OGC_Elementor_Widget_Slider extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_slider';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Slider', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Slides', 'ogc-newfinity' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Image', 'ogc-newfinity' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'caption',
			array(
				'label' => __( 'Caption', 'ogc-newfinity' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Slides', 'ogc-newfinity' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ caption }}}',
				'default'     => array(
					array(
						'caption' => __( 'Slide 1', 'ogc-newfinity' ),
					),
					array(
						'caption' => __( 'Slide 2', 'ogc-newfinity' ),
					),
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Speed (ms)', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 4500,
				'min'     => 1500,
				'max'     => 15000,
				'step'    => 100,
			)
		);

		$this->add_control(
			'transition',
			array(
				'label'   => __( 'Transition', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'fade'  => __( 'Fade', 'ogc-newfinity' ),
					'slide' => __( 'Slide', 'ogc-newfinity' ),
				),
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
				'default'   => $this->default_text_primary(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-el-slider__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .ogc-el-slider__caption',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$slides = ! empty( $s['slides'] ) && is_array( $s['slides'] ) ? $s['slides'] : array();
		if ( empty( $slides ) ) {
			return;
		}

		$transition = ! empty( $s['transition'] ) ? $s['transition'] : 'fade';
		$autoplay   = ! empty( $s['autoplay'] ) && 'yes' === $s['autoplay'] ? '1' : '0';
		$speed      = ! empty( $s['speed'] ) ? (int) $s['speed'] : 4500;
		?>
		<div
			class="ogc-el-slider ogc-el-slider--<?php echo esc_attr( $transition ); ?>"
			data-ogc-slider
			data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
			data-speed="<?php echo esc_attr( (string) $speed ); ?>"
			data-transition="<?php echo esc_attr( $transition ); ?>"
		>
			<div class="ogc-el-slider__track">
				<?php foreach ( $slides as $i => $slide ) : ?>
					<?php
					$url = ! empty( $slide['image']['url'] ) ? $slide['image']['url'] : '';
					if ( ! $url ) {
						$url = \Elementor\Utils::get_placeholder_image_src();
					}
					?>
					<figure class="ogc-el-slider__slide<?php echo 0 === (int) $i ? ' is-active' : ''; ?>" data-index="<?php echo esc_attr( (string) $i ); ?>">
						<img src="<?php echo esc_url( $url ); ?>" alt="" loading="<?php echo 0 === (int) $i ? 'eager' : 'lazy'; ?>">
						<?php if ( ! empty( $slide['caption'] ) ) : ?>
							<figcaption class="ogc-el-slider__caption"><?php echo esc_html( $slide['caption'] ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
			<div class="ogc-el-slider__nav" role="group" aria-label="<?php esc_attr_e( 'Slider controls', 'ogc-newfinity' ); ?>">
				<button type="button" class="ogc-btn ogc-btn-outline ogc-el-slider__prev" data-ogc-slider-prev aria-label="<?php esc_attr_e( 'Previous slide', 'ogc-newfinity' ); ?>">‹</button>
				<button type="button" class="ogc-btn ogc-btn-outline ogc-el-slider__next" data-ogc-slider-next aria-label="<?php esc_attr_e( 'Next slide', 'ogc-newfinity' ); ?>">›</button>
			</div>
			<div class="ogc-el-slider__dots" data-ogc-slider-dots></div>
		</div>
		<?php
	}
}
