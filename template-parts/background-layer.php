<?php
/**
 * Site-wide Three.js deep-space background (WebGL canvas).
 * Included once from header.php — fixed behind all content, pointer-events: none.
 *
 * @package OGC_NewFinity
 */
?>
<div class="ogc-bg-layer" aria-hidden="true">
	<canvas id="ogc-space-canvas" class="ogc-bg-layer__canvas"></canvas>
	<div class="ogc-bg-layer__fallback"></div>
</div>
