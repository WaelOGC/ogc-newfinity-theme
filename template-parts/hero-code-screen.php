<?php
/**
 * Our Services hero — decorative code-screen panel (page-specific).
 * Initialized by assets/js/services-code-screen.js via [data-ogc-code-screen].
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ogc-code-screen" data-ogc-code-screen aria-hidden="true">
	<div class="ogc-code-screen__bezel">
		<div class="ogc-code-screen__inner">
			<canvas class="ogc-code-screen__canvas" width="480" height="360"></canvas>
			<div class="ogc-code-screen__glass"></div>
			<div class="ogc-code-screen__bubble" hidden>
				<span class="ogc-code-screen__bubble-char"></span>
			</div>
		</div>
	</div>
</div>
