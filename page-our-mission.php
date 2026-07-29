<?php
/**
 * Our Mission page template — slug: our-mission (/our-mission/).
 *
 * @package OGC_NewFinity
 */

get_header();

$pillars = array(
	array(
		'icon'  => 'ai',
		'title' => 'Intelligence With Purpose',
		'text'  => "We don't build AI or automation for its own sake. Every tool we ship has to solve a real problem for a real person — otherwise it doesn't ship.",
	),
	array(
		'icon'  => 'shield',
		'title' => 'Ethical by Design',
		'text'  => "Security, privacy, and transparency aren't add-ons we bolt on at the end. They're part of how we scope a project from the first conversation.",
	),
	array(
		'icon'  => 'building',
		'title' => 'Future Innovation',
		'text'  => "Technology moves fast. We stay close to what's actually useful — not what's trending — so what we build for you still makes sense in three years, not three months.",
	),
);
?>

<article class="ogc-page ogc-page--mission">

	<section class="ogc-page-hero" aria-labelledby="ogc-mission-hero-heading">
		<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
		<h1 id="ogc-mission-hero-heading" class="ogc-page-hero__title">
			<?php esc_html_e( 'Our Mission', 'ogc-newfinity' ); ?>
		</h1>
		<p class="ogc-page-hero__lead">
			<?php esc_html_e( 'Ethical Innovation — Software · AI · Strategy', 'ogc-newfinity' ); ?>
		</p>
	</section>

	<section class="ogc-page-pillars" aria-labelledby="ogc-mission-pillars-heading">
		<h2 id="ogc-mission-pillars-heading" class="screen-reader-text">
			<?php esc_html_e( 'Mission pillars', 'ogc-newfinity' ); ?>
		</h2>

		<div class="ogc-page-pillars__grid">
			<?php foreach ( $pillars as $pillar ) : ?>
				<div class="glass-card ogc-pillar-card">
					<?php
					ogc_icon(
						$pillar['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-pillar-card__icon',
						)
					);
					?>
					<h3 class="ogc-pillar-card__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
					<p class="ogc-pillar-card__text"><?php echo esc_html( $pillar['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<p class="ogc-page-pillars__closing">
			<?php
			esc_html_e(
				'We measure success by whether our work actually earns your trust — not by how impressive it looks in a pitch deck.',
				'ogc-newfinity'
			);
			?>
		</p>
	</section>

</article>

<?php
get_footer();
