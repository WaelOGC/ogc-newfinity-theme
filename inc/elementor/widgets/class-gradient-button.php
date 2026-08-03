<?php
/**
 * Elementor widget: OGC Gradient Button (primary `.ogc-btn`).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Primary embossed CTA button.
 */
class OGC_Elementor_Widget_Gradient_Button extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_gradient_button';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Gradient Button', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-button';
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
				'default' => __( 'Submit Idea', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array(
					'url' => home_url( '/submit-idea/' ),
				),
			)
		);

		$this->add_control(
			'size',
			array(
				'label'   => __( 'Size', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'ogc-newfinity' ),
					'large'   => __( 'Large', 'ogc-newfinity' ),
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label' => __( 'Icon', 'ogc-newfinity' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
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
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .ogc-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => __( 'Border color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_accent_start(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-btn' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .ogc-btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s    = $this->get_settings_for_display();
		$link = $this->parse_link( isset( $s['link'] ) ? $s['link'] : array() );
		$href = $link['href'] ? $link['href'] : '#';

		$classes = array( 'ogc-btn' );
		if ( ! empty( $s['size'] ) && 'large' === $s['size'] ) {
			$classes[] = 'ogc-btn--lg';
		}

		$this->add_render_attribute(
			'btn',
			array(
				'class' => implode( ' ', $classes ),
				'href'  => esc_url( $href ),
			)
		);
		if ( $link['target'] ) {
			$this->add_render_attribute( 'btn', 'target', $link['target'] );
		}
		if ( $link['rel'] ) {
			$this->add_render_attribute( 'btn', 'rel', $link['rel'] );
		}
		?>
		<a <?php $this->print_render_attribute_string( 'btn' ); ?>>
			<?php
			if ( ! empty( $s['selected_icon']['value'] ) ) {
				echo '<span class="ogc-btn__icon" aria-hidden="true">';
				$this->render_icon( $s['selected_icon'] );
				echo '</span>';
			}
			echo esc_html( isset( $s['text'] ) ? (string) $s['text'] : '' );
			?>
		</a>
		<?php
	}
}
