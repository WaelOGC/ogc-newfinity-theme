<?php
/**
 * Elementor widget: OGC Service Card.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service card matching page-services.php glass cards.
 */
class OGC_Elementor_Widget_Service_Card extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_service_card';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Service Card', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-icon-box';
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
			'theme_icon',
			array(
				'label'   => __( 'Theme icon', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'code',
				'options' => array(
					''           => __( 'None (use Elementor icon)', 'ogc-newfinity' ),
					'code'       => __( 'Code', 'ogc-newfinity' ),
					'wordpress'  => __( 'WordPress', 'ogc-newfinity' ),
					'design'     => __( 'Design', 'ogc-newfinity' ),
					'strategy'   => __( 'Strategy', 'ogc-newfinity' ),
					'shield'     => __( 'Shield', 'ogc-newfinity' ),
					'ai'         => __( 'AI', 'ogc-newfinity' ),
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'     => __( 'Elementor icon', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'theme_icon' => '',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Software & App Development', 'ogc-newfinity' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Custom applications built for how your business actually operates.', 'ogc-newfinity' ),
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
				'label'     => __( 'Title color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_primary(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-service-card__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title typography', 'ogc-newfinity' ),
				'selector' => '{{WRAPPER}} .ogc-service-card__title',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text color', 'ogc-newfinity' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => $this->default_text_muted(),
				'selectors' => array(
					'{{WRAPPER}} .ogc-service-card__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => __( 'Text typography', 'ogc-newfinity' ),
				'selector' => '{{WRAPPER}} .ogc-service-card__text',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<article class="glass-card ogc-service-card">
			<?php
			if ( ! empty( $s['theme_icon'] ) && function_exists( 'ogc_icon' ) ) {
				ogc_icon(
					$s['theme_icon'],
					array(
						'size'  => 'lg',
						'class' => 'ogc-service-card__icon',
					)
				);
			} elseif ( ! empty( $s['selected_icon']['value'] ) ) {
				echo '<div class="ogc-service-card__icon ogc-el-service-icon" aria-hidden="true">';
				$this->render_icon( $s['selected_icon'] );
				echo '</div>';
			}
			?>
			<h3 class="ogc-service-card__title"><?php echo esc_html( isset( $s['title'] ) ? (string) $s['title'] : '' ); ?></h3>
			<p class="ogc-service-card__text"><?php echo esc_html( isset( $s['description'] ) ? (string) $s['description'] : '' ); ?></p>
		</article>
		<?php
	}
}
