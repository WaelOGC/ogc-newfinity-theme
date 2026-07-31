<?php
/**
 * Our Mission page template — slug: our-mission (/our-mission/).
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url = home_url( '/submit-idea/' );

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

$practice = array(
	array(
		'icon'  => 'stop',
		'title' => 'We say no to scope creep dressed up as innovation',
		'text'  => "If a client asks for an AI feature that doesn't solve a real problem — just because it sounds impressive in a pitch — we'll tell you honestly it's not worth building, even if it costs us the extra invoice.",
	),
	array(
		'icon'  => 'lock',
		'title' => 'Security review happens before the first line of code',
		'text'  => 'Data handling, access control, and privacy questions get answered during scoping, not retrofitted after launch when a client (or a regulator) asks.',
	),
	array(
		'icon'  => 'layers',
		'title' => 'We build on stable foundations, not this month\'s trend',
		'text'  => "We'd rather ship something slightly less flashy that still works in three years than chase a framework or a model that might not exist by the time your project needs an update.",
	),
);
?>

<article class="ogc-page ogc-page--mission">

	<section class="ogc-page-hero ogc-split-hero ogc-mission-hero" aria-labelledby="ogc-mission-hero-heading">
		<?php ogc_render_hero_bg( 'mission' ); ?>
		<div class="ogc-split-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-mission-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'Our', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'Mission', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php esc_html_e( 'Ethical Innovation — Software · AI · Strategy', 'ogc-newfinity' ); ?>
			</p>

			<div class="ogc-mission-intro">
				<h2 id="ogc-mission-intro-heading" class="ogc-mission-intro__title">
					<?php esc_html_e( 'What Ethical Innovation Actually Means', 'ogc-newfinity' ); ?>
				</h2>
				<p class="ogc-mission-intro__text">
					<?php
					esc_html_e(
						'"Ethical innovation" gets used as a slogan a lot. For us it means something specific: every technical decision — what we build, what we automate, what data we touch — gets weighed against whether it actually respects the person on the other end. These three principles aren\'t a mission statement we wrote once and forgot. They\'re what we check a project against before we say yes to it.',
						'ogc-newfinity'
					);
					?>
				</p>
			</div>
		</div>
		<div class="ogc-split-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'mission-compass' ); ?>
		</div>
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
	</section>

	<section class="ogc-mission-practice" aria-labelledby="ogc-mission-practice-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-mission-practice-heading" class="ogc-section-title">
				<?php esc_html_e( 'How This Shows Up in Our Work', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-section-intro">
				<?php esc_html_e( 'Principles are easy to write. Here\'s what they actually mean in practice.', 'ogc-newfinity' ); ?>
			</p>
		</header>

		<div class="ogc-page-pillars__grid">
			<?php foreach ( $practice as $item ) : ?>
				<div class="glass-card ogc-pillar-card">
					<?php
					ogc_icon(
						$item['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-pillar-card__icon',
						)
					);
					?>
					<h3 class="ogc-pillar-card__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="ogc-pillar-card__text"><?php echo esc_html( $item['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<blockquote class="ogc-page-quote">
		<span class="ogc-page-quote__mark" aria-hidden="true">&ldquo;</span>
		<span class="ogc-page-quote__rule" aria-hidden="true"></span>
		<p class="ogc-page-quote__text">
			<?php esc_html_e( 'We measure success by whether our work actually', 'ogc-newfinity' ); ?>
			<span class="hero-shimmer"><?php esc_html_e( 'earns your trust', 'ogc-newfinity' ); ?></span>
			<?php esc_html_e( '— not by how impressive it looks in a pitch deck.', 'ogc-newfinity' ); ?>
		</p>
	</blockquote>

	<section class="ogc-home-cta" aria-labelledby="ogc-mission-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-mission-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'See these principles in action', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					'The best way to understand how we work is to see it on a real project — yours.',
					'ogc-newfinity'
				);
				?>
			</p>
			<a class="ogc-btn" href="<?php echo esc_url( $submit_url ); ?>">
				<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
			</a>
		</div>
	</section>

</article>

<?php
get_footer();
