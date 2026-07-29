<?php
/**
 * About Us page template — slug: about-ogc-newfinity (/about-ogc-newfinity/).
 *
 * @package OGC_NewFinity
 */

get_header();

$services_url = home_url( '/services/' );
$mission_url  = home_url( '/our-mission/' );
$contact_url  = home_url( '/contact/' );

$stand_on = array(
	array(
		'icon'  => 'mission',
		'title' => 'Our Mission',
		'text'  => 'Build technology that respects the people who use it: ethical, transparent, and genuinely useful.',
		'link'  => $mission_url,
	),
	array(
		'icon'  => 'eye',
		'title' => 'Our Vision',
		'text'  => "To be the digital partner ambitious teams call first — not because we're the biggest, but because we're the most direct.",
		'link'  => '',
	),
	array(
		'icon'  => 'heart',
		'title' => 'Our Values',
		'text'  => "Direct collaboration, honest timelines, and work we're still proud of a year later.",
		'link'  => '',
	),
);

$service_tags = array(
	array(
		'slug'  => 'software-app-development',
		'icon'  => 'code',
		'title' => 'Software & App Development',
	),
	array(
		'slug'  => 'custom-wordpress-development',
		'icon'  => 'wordpress',
		'title' => 'Custom WordPress Development',
	),
	array(
		'slug'  => 'ui-ux-design',
		'icon'  => 'design',
		'title' => 'UI/UX Design',
	),
	array(
		'slug'  => 'marketing-strategy-consulting',
		'icon'  => 'strategy',
		'title' => 'Marketing & Strategy Consulting',
	),
	array(
		'slug'  => 'cybersecurity',
		'icon'  => 'shield',
		'title' => 'Cybersecurity',
	),
	array(
		'slug'  => 'ai-solutions',
		'icon'  => 'ai',
		'title' => 'AI Solutions',
	),
);
?>

<article class="ogc-page ogc-page--about">

	<section class="ogc-page-hero ogc-about-hero" aria-labelledby="ogc-about-hero-heading">
		<div class="ogc-about-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'THE HAGUE, NETHERLANDS', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-about-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'About OGC', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'NewFinity', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php
				esc_html_e(
					'OGC NewFinity is a digital agency based in The Hague, Netherlands, founded in 2022. We build software, websites, and digital strategy for teams that need real engineering behind their ideas — not templates, not shortcuts. Every project is handled directly by the people who build it.',
					'ogc-newfinity'
				);
				?>
			</p>
		</div>
		<div class="ogc-about-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'orbit-atom' ); ?>
		</div>
	</section>

	<section class="ogc-about-dual" aria-labelledby="ogc-about-dual-heading">
		<h2 id="ogc-about-dual-heading" class="screen-reader-text">
			<?php esc_html_e( 'Our Story and How We Work', 'ogc-newfinity' ); ?>
		</h2>
		<div class="ogc-about-dual__grid">
			<div class="glass-card ogc-about-dual__card">
				<h3 class="ogc-about-dual__title"><?php esc_html_e( 'Our Story', 'ogc-newfinity' ); ?></h3>
				<div class="ogc-about-dual__body">
					<p>
						<?php
						esc_html_e(
							'OGC NewFinity started in 2022 with a simple frustration: too many digital agencies sell polish without substance — a strong pitch, then a handoff to whoever happens to be free that week. We wanted to build something different — a studio where the person who scopes your project is the person who\'s still there when it ships.',
							'ogc-newfinity'
						);
						?>
					</p>
					<p>
						<?php
						esc_html_e(
							'What began as a small web development practice has grown into a full digital agency covering six core capabilities: software and app development, custom WordPress builds, UI/UX design, marketing and strategy consulting, cybersecurity, and AI solutions.',
							'ogc-newfinity'
						);
						?>
					</p>
				</div>
			</div>

			<div class="glass-card ogc-about-dual__card">
				<h3 class="ogc-about-dual__title"><?php esc_html_e( 'How We Work', 'ogc-newfinity' ); ?></h3>
				<div class="ogc-about-dual__body">
					<p>
						<?php
						esc_html_e(
							'Our team works remotely by default, which lets us bring in the right specialist for each project rather than whoever\'s local. Every engagement starts and stays online — clear communication, defined milestones, real progress you can see.',
							'ogc-newfinity'
						);
						?>
					</p>
					<p>
						<?php
						esc_html_e(
							'For clients based in The Hague, Amsterdam, Rotterdam, or Utrecht, an in-person meeting is available once initial contact and scoping have happened online. It\'s not the default — it\'s an option once we already know what we\'re building together.',
							'ogc-newfinity'
						);
						?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="ogc-page-stand" aria-labelledby="ogc-about-stand-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-about-stand-heading" class="ogc-section-title">
				<?php esc_html_e( 'What We Stand On', 'ogc-newfinity' ); ?>
			</h2>
		</header>

		<div class="ogc-page-stand__grid">
			<?php foreach ( $stand_on as $card ) : ?>
				<div class="glass-card ogc-stand-card">
					<?php
					ogc_icon(
						$card['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-stand-card__icon',
						)
					);
					?>
					<h3 class="ogc-stand-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
					<p class="ogc-stand-card__text"><?php echo esc_html( $card['text'] ); ?></p>
					<?php if ( ! empty( $card['link'] ) ) : ?>
						<a class="ogc-stand-card__more" href="<?php echo esc_url( $card['link'] ); ?>">
							<?php esc_html_e( 'Read more', 'ogc-newfinity' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-page-services-recap" aria-labelledby="ogc-about-services-heading">
		<h3 id="ogc-about-services-heading" class="ogc-page-services-recap__title">
			<?php esc_html_e( 'What We Do', 'ogc-newfinity' ); ?>
		</h3>
		<ul class="ogc-service-tags">
			<?php foreach ( $service_tags as $tag ) : ?>
				<li>
					<a
						class="ogc-service-tag"
						href="<?php echo esc_url( $services_url . '#' . $tag['slug'] ); ?>"
					>
						<?php
						ogc_icon(
							$tag['icon'],
							array(
								'size'  => 'sm',
								'class' => 'ogc-service-tag__icon',
							)
						);
						?>
						<span class="ogc-service-tag__label"><?php echo esc_html( $tag['title'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<section class="ogc-home-cta" aria-labelledby="ogc-about-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-about-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Ready to build together?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					"Have a question, an idea, or a project in mind? We're ready to hear about it.",
					'ogc-newfinity'
				);
				?>
			</p>
			<a class="ogc-btn" href="<?php echo esc_url( $contact_url ); ?>">
				<?php esc_html_e( 'Get in Touch', 'ogc-newfinity' ); ?>
			</a>
		</div>
	</section>

</article>

<?php
get_footer();
