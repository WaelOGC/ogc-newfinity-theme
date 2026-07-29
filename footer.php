<?php
/**
 * Site footer template.
 *
 * Structure only — final copy and meta fields come later via Theme Options.
 *
 * @package OGC_NewFinity
 */
?>
</main><!-- #ogc-content -->

<footer class="ogc-footer" role="contentinfo">
	<div class="ogc-footer__inner">
		<p class="ogc-footer__meta"></p>
		<p class="ogc-footer__brand"><?php bloginfo( 'name' ); ?></p>
	</div>
	<div class="ogc-accent-bar" aria-hidden="true"></div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
