<?php
/**
 * Site-wide Three.js deep-space background (WebGL canvas).
 * Included once from header.php — fixed behind all content, pointer-events: none.
 * Nebula texture is a subtle CSS atmosphere under the WebGL scene.
 *
 * @package OGC_NewFinity
 */
?>
<div class="ogc-bg-layer" aria-hidden="true">
	<div class="ogc-bg-layer__fallback"></div>
	<div class="ogc-bg-layer__nebula"></div>
	<canvas id="ogc-space-canvas" class="ogc-bg-layer__canvas"></canvas>
</div>
