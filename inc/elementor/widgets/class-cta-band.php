<?php
/**
 * Elementor widget: OGC CTA Band (reuses `.ogc-home-cta`).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Full-width CTA section matching home / services CTA band.
 */
class OGC_Elementor_Widget_CTA_Band extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_cta_band';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC CTA Band', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
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
			'headline',
			array(
				'label'   => __( 'Headline', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Not sure which service fits?', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'body',
			array(
				'label'   => __( 'Body text', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( "Tell us what you're building — we'll point you in the right direction, free.", 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Submit Your Idea', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'   => __( 'Button link', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array(
					'url' => home_url( '/submit-idea/' ),
				),
			)
		);

		$this->add_control(
			'bg_style',
			array(
				'label'   => __( 'Background style', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => __( 'None (glass card only)', 'ogc-newfinity' ),
					'solid'    => __( 'Solid brand color', 'ogc-newfinity' ),
					'gradient' => __( 'Brand gradient', 'ogc-newfinity' ),
					'image'    => __( 'Image + overlay', 'ogc-newfinity' ),
				),
			)
		);

		$this->add_control(
			'bg_solid',
			array(
				'label'     => __( 'Background color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_bg(),
				'condition' => array(
					'bg_style' => 'solid',
				),
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'     => __( 'Background image', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'bg_style' => 'image',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_bg(),
				'condition' => array(
					'bg_style' => 'image',
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Overlay opacity', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 50,
					'unit' => '%',
				),
				'range'     => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition' => array(
					'bg_style' => 'image',
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
			'title_color',
			array(
				'label'     => __( 'Headline color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_primary(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-home-cta__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ogc-home-cta__title',
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => __( 'Body color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-home-cta__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'body_typography',
				'selector' => '{{WRAPPER}} .ogc-home-cta__text',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$link   = $this->parse_link( isset( $s['button_link'] ) ? $s['button_link'] : array() );
		$href   = $link['href'] ? $link['href'] : '#';
		$uid    = 'ogc-cta-' . $this->get_id();
		$style  = '';
		$extra  = '';

		$bg = isset( $s['bg_style'] ) ? $s['bg_style'] : 'none';
		if ( 'solid' === $bg && ! empty( $s['bg_solid'] ) ) {
			$style = 'background-color:' . esc_attr( $s['bg_solid'] ) . ';';
		} elseif ( 'gradient' === $bg ) {
			$style = sprintf(
				'background: linear-gradient(90deg, %1$s, %2$s);',
				esc_attr( $this->default_accent_start() ),
				esc_attr( $this->default_accent_end() )
			);
		} elseif ( 'image' === $bg && ! empty( $s['bg_image']['url'] ) ) {
			$op     = isset( $s['overlay_opacity']['size'] ) ? ( (int) $s['overlay_opacity']['size'] / 100 ) : 0.5;
			$ov     = ! empty( $s['overlay_color'] ) ? $s['overlay_color'] : $this->default_bg();
			$style  = sprintf( 'background-image:url(%s);background-size:cover;background-position:center;', esc_url( $s['bg_image']['url'] ) );
			$extra  = sprintf(
				'<span class="ogc-el-cta-band__overlay" style="background-color:%1$s;opacity:%2$s;" aria-hidden="true"></span>',
				esc_attr( $ov ),
				esc_attr( (string) $op )
			);
		}
		?>
		<section class="ogc-home-cta ogc-el-cta-band" aria-labelledby="<?php echo esc_attr( $uid ); ?>"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above with escaped attrs.
			echo $extra;
			?>
			<div class="glass-card ogc-home-cta__inner">
				<h2 id="<?php echo esc_attr( $uid ); ?>" class="ogc-home-cta__title"><?php echo esc_html( isset( $s['headline'] ) ? (string) $s['headline'] : '' ); ?></h2>
				<?php if ( ! empty( $s['body'] ) ) : ?>
					<p class="ogc-home-cta__text"><?php echo esc_html( $s['body'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['button_text'] ) ) : ?>
					<a class="ogc-btn" href="<?php echo esc_url( $href ); ?>"<?php echo $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : ''; ?><?php echo $link['rel'] ? ' rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
						<?php echo esc_html( $s['button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
