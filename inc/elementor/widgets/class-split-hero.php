<?php
/**
 * Elementor widget: OGC Split Hero.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Two-column split hero matching page-services / front-page pattern.
 */
class OGC_Elementor_Widget_Split_Hero extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_split_hero';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Split Hero', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-banner';
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
			'badge_text',
			array(
				'label'   => __( 'Badge text', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'OGC NEWFINITY', 'ogc-newfinity' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Word / phrase', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our', 'ogc-newfinity' ),
			)
		);

		$repeater->add_control(
			'shimmer',
			array(
				'label'        => __( 'Accent underline (hero-shimmer)', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'color',
			array(
				'label'   => __( 'Color', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			)
		);

		$this->add_control(
			'headline_words',
			array(
				'label'       => __( 'Headline words', 'ogc-newfinity' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array(
						'text'    => __( 'Our', 'ogc-newfinity' ),
						'shimmer' => '',
						'color'   => '',
					),
					array(
						'text'    => __( 'Services', 'ogc-newfinity' ),
						'shimmer' => 'yes',
						'color'   => '',
					),
				),
			)
		);

		$this->add_control(
			'lead',
			array(
				'label'   => __( 'Supporting text', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Six core capabilities, delivered end-to-end — from first concept to long-term support.', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'visual_image',
			array(
				'label' => __( 'Right-side image (optional)', 'ogc-newfinity' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'show_orbit',
			array(
				'label'        => __( 'Show orbit graphic when no image', 'ogc-newfinity' ),
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
			'headline_color',
			array(
				'label'     => __( 'Default headline color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_primary(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-page-hero__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .ogc-page-hero__title',
			)
		);

		$this->add_control(
			'lead_color',
			array(
				'label'     => __( 'Lead text color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-page-hero__lead' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'lead_typography',
				'selector' => '{{WRAPPER}} .ogc-page-hero__lead',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$uid    = 'ogc-split-hero-' . $this->get_id();
		$words  = ! empty( $s['headline_words'] ) && is_array( $s['headline_words'] ) ? $s['headline_words'] : array();
		$has_img = ! empty( $s['visual_image']['url'] );
		?>
		<section class="ogc-page-hero ogc-split-hero ogc-el-split-hero" aria-labelledby="<?php echo esc_attr( $uid ); ?>">
			<div class="ogc-split-hero__copy">
				<?php if ( ! empty( $s['badge_text'] ) ) : ?>
					<p class="ogc-badge"><?php echo esc_html( $s['badge_text'] ); ?></p>
				<?php endif; ?>
				<h1 id="<?php echo esc_attr( $uid ); ?>" class="ogc-page-hero__title">
					<?php
					foreach ( $words as $word ) {
						$text     = isset( $word['text'] ) ? (string) $word['text'] : '';
						$shimmer  = ! empty( $word['shimmer'] ) && 'yes' === $word['shimmer'];
						$color    = ! empty( $word['color'] ) ? (string) $word['color'] : '';
						$class    = $shimmer ? 'hero-shimmer' : '';
						$style    = $color ? 'color:' . esc_attr( $color ) . ';' : '';
						$tag_open = $class || $style ? '<span' : '';
						if ( $tag_open ) {
							printf(
								'<span%s%s>%s</span> ',
								$class ? ' class="' . esc_attr( $class ) . '"' : '',
								$style ? ' style="' . esc_attr( $style ) . '"' : '',
								esc_html( $text )
							);
						} else {
							echo esc_html( $text ) . ' ';
						}
					}
					?>
				</h1>
				<?php if ( ! empty( $s['lead'] ) ) : ?>
					<p class="ogc-page-hero__lead"><?php echo esc_html( $s['lead'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="ogc-split-hero__visual">
				<?php if ( $has_img ) : ?>
					<img
						class="ogc-el-split-hero__img"
						src="<?php echo esc_url( $s['visual_image']['url'] ); ?>"
						alt="<?php echo esc_attr( ! empty( $s['visual_image']['alt'] ) ? $s['visual_image']['alt'] : '' ); ?>"
						loading="lazy"
					>
				<?php elseif ( ! empty( $s['show_orbit'] ) && 'yes' === $s['show_orbit'] ) : ?>
					<?php get_template_part( 'template-parts/hero', 'orbit-atom' ); ?>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
