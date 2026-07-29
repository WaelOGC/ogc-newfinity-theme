<?php
/**
 * Homepage hero brand cube — markup only; behaviour in assets/js/brand-cube.js.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faces = array(
	array(
		'id'    => 'about',
		'title' => __( 'About us', 'ogc-newfinity' ),
		'body'  => __( 'OGC NewFinity is a digital agency based in The Hague, Netherlands, founded in 2022. We build technology with a simple standard: it has to actually work for the people using it.', 'ogc-newfinity' ),
		'btn'   => __( 'Learn more', 'ogc-newfinity' ),
		'url'   => home_url( '/about-ogc-newfinity/' ),
	),
	array(
		'id'    => 'services',
		'title' => __( 'Our services', 'ogc-newfinity' ),
		'body'  => __( 'Software, WordPress, UI/UX, marketing strategy, cybersecurity, and AI — six capabilities, one team.', 'ogc-newfinity' ),
		'btn'   => __( 'View services', 'ogc-newfinity' ),
		'url'   => home_url( '/services/' ),
	),
	array(
		'id'    => 'mission',
		'title' => __( 'Our mission', 'ogc-newfinity' ),
		'body'  => __( 'Ethical innovation, built with intention. We measure success by whether our work actually earns your trust.', 'ogc-newfinity' ),
		'btn'   => __( 'Read our mission', 'ogc-newfinity' ),
		'url'   => home_url( '/our-mission/' ),
	),
	array(
		'id'    => 'contact',
		'title' => __( 'Contact us', 'ogc-newfinity' ),
		'body'  => __( 'Based in The Hague, Netherlands. contact@ogcnewfinity.com — every message gets a real reply within 48 hours.', 'ogc-newfinity' ),
		'btn'   => __( 'Get in touch', 'ogc-newfinity' ),
		'url'   => home_url( '/contact/' ),
	),
	array(
		'id'    => 'feedback',
		'title' => __( 'Leave feedback', 'ogc-newfinity' ),
		'body'  => __( "Had an experience with us? We'd like to hear about it, good or bad.", 'ogc-newfinity' ),
		'btn'   => __( 'Share feedback', 'ogc-newfinity' ),
		'url'   => home_url( '/support/' ),
	),
	array(
		'id'    => 'ai-support',
		'title' => __( 'AI support', 'ogc-newfinity' ),
		'body'  => __( 'Talk to our assistant for quick answers about our services and process.', 'ogc-newfinity' ),
		'btn'   => __( 'Chat now', 'ogc-newfinity' ),
		'url'   => home_url( '/support/#ai-support' ),
	),
);
?>
<div class="ogc-brand-cube" id="ogc-brand-cube" data-ogc-brand-cube>
	<canvas class="ogc-brand-cube__canvas" id="ogc-brand-cube-canvas" aria-hidden="true"></canvas>

	<div class="ogc-brand-cube__faces" aria-live="polite">
		<?php foreach ( $faces as $index => $face ) : ?>
			<div
				class="ogc-brand-cube__face glass-card<?php echo 0 === $index ? ' is-front' : ''; ?>"
				data-face-index="<?php echo esc_attr( (string) $index ); ?>"
				data-face-id="<?php echo esc_attr( $face['id'] ); ?>"
				role="button"
				tabindex="-1"
				aria-expanded="false"
				aria-label="<?php echo esc_attr( $face['title'] ); ?>"
			>
				<span class="ogc-brand-cube__face-title"><?php echo esc_html( $face['title'] ); ?></span>
				<span class="ogc-brand-cube__face-detail">
					<span class="ogc-brand-cube__face-body"><?php echo esc_html( $face['body'] ); ?></span>
					<a class="ogc-btn ogc-btn--sm" href="<?php echo esc_url( $face['url'] ); ?>">
						<?php echo esc_html( $face['btn'] ); ?>
					</a>
				</span>
			</div>
		<?php endforeach; ?>
	</div>

	<p class="ogc-brand-cube__hint" data-cube-hint>
		<span class="ogc-brand-cube__hint-dot" aria-hidden="true"></span>
		<span class="ogc-brand-cube__hint-text"><?php esc_html_e( 'Tap a face', 'ogc-newfinity' ); ?></span>
	</p>
</div>
