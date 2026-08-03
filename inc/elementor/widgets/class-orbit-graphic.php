<?php
/**
 * Elementor widget: OGC Orbit Graphic.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Decorative orbit/atom emblem from hero-orbit-atom.php.
 */
class OGC_Elementor_Widget_Orbit_Graphic extends OGC_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ogc_orbit_graphic';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'OGC Orbit Graphic', 'ogc-newfinity' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-nerd-chuckle';
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
			'size',
			array(
				'label'   => __( 'Size', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 440,
					'unit' => 'px',
				),
				'range'   => array(
					'px' => array(
						'min' => 160,
						'max' => 640,
					),
				),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'   => __( 'Opacity', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'range'   => array(
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
			)
		);

		$this->add_control(
			'position_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Positioning uses Elementor’s Alignment / Position controls — the orbit component itself has no X/Y CSS vars.', 'ogc-newfinity' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
			'align',
			array(
				'label'   => __( 'Alignment', 'ogc-newfinity' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'ogc-newfinity' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'ogc-newfinity' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'ogc-newfinity' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ogc-el-orbit-wrap' => 'display:flex;justify-content:{{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
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
		$size    = isset( $s['size']['size'] ) ? (int) $s['size']['size'] : 440;
		$opacity = isset( $s['opacity']['size'] ) ? ( (int) $s['opacity']['size'] / 100 ) : 1;
		$style   = sprintf( 'width:%dpx;max-width:100%%;opacity:%s;', $size, esc_attr( (string) $opacity ) );
		?>
		<div class="ogc-el-orbit-wrap">
			<div class="ogc-orbit-atom" data-ogc-orbit-emblem aria-hidden="true" style="<?php echo esc_attr( $style ); ?>">
				<canvas class="ogc-orbit-atom__canvas" width="512" height="512"></canvas>
			</div>
		</div>
		<?php
	}
}
