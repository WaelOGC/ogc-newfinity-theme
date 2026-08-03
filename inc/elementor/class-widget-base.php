<?php
/**
 * Shared base for OGC Elementor widgets.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract widget base — category + small helpers.
 */
abstract class OGC_Elementor_Widget_Base extends \Elementor\Widget_Base {

	/**
	 * Widget category.
	 *
	 * @return array<int, string>
	 */
	public function get_categories() {
		return array( 'ogc-newfinity' );
	}

	/**
	 * Default brand accent start hex.
	 *
	 * @return string
	 */
	protected function default_accent_start() {
		return defined( 'OGC_DEFAULT_COLOR_ACCENT_START' ) ? OGC_DEFAULT_COLOR_ACCENT_START : '#FFD27A';
	}

	/**
	 * Default brand accent end hex.
	 *
	 * @return string
	 */
	protected function default_accent_end() {
		return defined( 'OGC_DEFAULT_COLOR_ACCENT_END' ) ? OGC_DEFAULT_COLOR_ACCENT_END : '#FFEAC2';
	}

	/**
	 * Default primary text hex.
	 *
	 * @return string
	 */
	protected function default_text_primary() {
		return defined( 'OGC_DEFAULT_COLOR_TEXT_PRIMARY' ) ? OGC_DEFAULT_COLOR_TEXT_PRIMARY : '#F5F5F5';
	}

	/**
	 * Default muted text hex.
	 *
	 * @return string
	 */
	protected function default_text_muted() {
		return defined( 'OGC_DEFAULT_COLOR_TEXT_SECONDARY' ) ? OGC_DEFAULT_COLOR_TEXT_SECONDARY : '#C9C9C9';
	}

	/**
	 * Default page background hex.
	 *
	 * @return string
	 */
	protected function default_bg() {
		return defined( 'OGC_DEFAULT_COLOR_BG' ) ? OGC_DEFAULT_COLOR_BG : '#0A0A0A';
	}

	/**
	 * Text-on-accent (badge label) — matches style.css --text-on-accent.
	 *
	 * @return string
	 */
	protected function default_text_on_accent() {
		return '#1A1200';
	}

	/**
	 * Render an Elementor icon control value.
	 *
	 * @param array<string, mixed> $icon    Icon control value.
	 * @param array<string, mixed> $attrs   Extra HTML attributes.
	 * @return void
	 */
	protected function render_icon( $icon, $attrs = array() ) {
		if ( empty( $icon['value'] ) ) {
			return;
		}
		\Elementor\Icons_Manager::render_icon( $icon, $attrs );
	}

	/**
	 * Build safe link attributes from an Elementor URL control.
	 *
	 * @param array<string, mixed> $link URL control value.
	 * @return array{href:string, target:string, rel:string}
	 */
	protected function parse_link( $link ) {
		$url    = isset( $link['url'] ) ? (string) $link['url'] : '';
		$target = ! empty( $link['is_external'] ) ? '_blank' : '';
		$rel    = ! empty( $link['nofollow'] ) ? 'nofollow' : '';
		if ( $target && false === strpos( $rel, 'noopener' ) ) {
			$rel = trim( $rel . ' noopener' );
		}
		return array(
			'href'   => $url,
			'target' => $target,
			'rel'    => $rel,
		);
	}
}
