<?php
/**
 * Homepage hero brand cube — canvas only.
 * Face text + CTA button are baked into WebGL textures (assets/js/brand-cube.js).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ogc-brand-cube" id="ogc-brand-cube" data-ogc-brand-cube>
	<canvas
		class="ogc-brand-cube__canvas"
		id="ogc-brand-cube-canvas"
		width="512"
		height="512"
		aria-label="<?php esc_attr_e( 'Interactive OGC NewFinity brand cube', 'ogc-newfinity' ); ?>"
	></canvas>
	<p class="ogc-brand-cube__hint" data-cube-hint>
		<span class="ogc-brand-cube__hint-dot" aria-hidden="true"></span>
		<span class="ogc-brand-cube__hint-text"><?php esc_html_e( 'Drag to explore · Click a face', 'ogc-newfinity' ); ?></span>
	</p>
</div>
