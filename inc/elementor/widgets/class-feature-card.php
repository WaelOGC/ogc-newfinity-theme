<?php
/**
 * Elementor widget: OGC Feature Card (`.glass-card`).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Glass feature/info card.
 */
class OGC_Elementor_Widget_Feature_Card extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_feature_card';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Feature Card', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-box';
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
			'selected_icon',
			array(
				'label'   => __( 'Icon', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Feature title', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Short supporting description for this feature.', 'ogc-newfinity' ),
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
			'icon_color',
			array(
				'label'     => __( 'Icon color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_accent_start(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-feature-card__icon'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .ogc-feature-card__icon svg'      => 'fill: {{VALUE}};',
					'{{WRAPPER}} .ogc-feature-card__icon i'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .ogc-feature-card__icon svg path' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_primary(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-feature-card__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title typography', 'ogc-newfinity' ),
				'selector' => '{{WRAPPER}} .ogc-feature-card__title',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Description color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-feature-card__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => __( 'Description typography', 'ogc-newfinity' ),
				'selector' => '{{WRAPPER}} .ogc-feature-card__text',
			)
		);

		$this->add_control(
			'hover_effects',
			array(
				'label'        => __( 'Hover border / shadow', 'ogc-newfinity' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'ogc-newfinity' ),
				'label_off'    => __( 'Off', 'ogc-newfinity' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hover_border_color',
			array(
				'label'     => __( 'Hover border color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_accent_start(),
				'condition' => array(
					'hover_effects' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .ogc-feature-card--hover:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'hover_shadow',
				'label'     => __( 'Hover shadow', 'ogc-newfinity' ),
				'selector'  => '{{WRAPPER}} .ogc-feature-card--hover:hover',
				'condition' => array(
					'hover_effects' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s       = $this->get_settings_for_display();
		$classes = array( 'glass-card', 'ogc-feature-card' );
		if ( ! empty( $s['hover_effects'] ) && 'yes' === $s['hover_effects'] ) {
			$classes[] = 'ogc-feature-card--hover';
		}
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( ! empty( $s['selected_icon']['value'] ) ) : ?>
				<div class="ogc-feature-card__icon" aria-hidden="true">
					<?php $this->render_icon( $s['selected_icon'] ); ?>
				</div>
			<?php endif; ?>
			<h3 class="ogc-feature-card__title"><?php echo esc_html( isset( $s['title'] ) ? (string) $s['title'] : '' ); ?></h3>
			<p class="ogc-feature-card__text"><?php echo esc_html( isset( $s['description'] ) ? (string) $s['description'] : '' ); ?></p>
		</article>
		<?php
	}
}
