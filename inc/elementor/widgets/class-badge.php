<?php
/**
 * Elementor widget: OGC Badge/Pill.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Badge/pill eyebrow label using `.ogc-badge`.
 */
class OGC_Elementor_Widget_Badge extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_badge';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Badge/Pill', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-tags';
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
			'text',
			array(
				'label'   => __( 'Text', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'OGC NEWFINITY', 'ogc-newfinity' ),
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
			'gradient_start',
			array(
				'label'   => __( 'Background gradient start', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $this->default_accent_start(),
			)
		);

		$this->add_control(
			'gradient_end',
			array(
				'label'   => __( 'Background gradient end', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $this->default_accent_end(),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_on_accent(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .ogc-badge',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s     = $this->get_settings_for_display();
		$text  = isset( $s['text'] ) ? (string) $s['text'] : '';
		$start = ! empty( $s['gradient_start'] ) ? $s['gradient_start'] : $this->default_accent_start();
		$end   = ! empty( $s['gradient_end'] ) ? $s['gradient_end'] : $this->default_accent_end();

		$style = sprintf(
			'background: linear-gradient(90deg, %1$s, %2$s);',
			esc_attr( $start ),
			esc_attr( $end )
		);
		?>
		<p class="ogc-badge" style="<?php echo esc_attr( $style ); ?>"><?php echo esc_html( $text ); ?></p>
		<?php
	}
}
