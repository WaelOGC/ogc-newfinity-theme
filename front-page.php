<?php
/**
 * Front page (Home) template.
 *
 * Used when Settings → Reading shows a static front page (configured in theme-setup).
 *
 * @package OGC_NewFinity
 */

get_header();

$services_url = home_url( '/services/' );
$submit_url   = home_url( '/submit-idea/' );

$services = array(
	array(
		'slug'  => 'software-app-development',
		'icon'  => 'code',
		'title' => 'Software & App Development',
		'text'  => 'Scalable, secure applications built with modern engineering practices, from concept to launch.',
	),
	array(
		'slug'  => 'custom-wordpress-development',
		'icon'  => 'wordpress',
		'title' => 'Custom WordPress Development',
		'text'  => 'Bespoke WordPress builds and themes designed around how your business actually works.',
	),
	array(
		'slug'  => 'ui-ux-design',
		'icon'  => 'design',
		'title' => 'UI/UX Design',
		'text'  => 'Interfaces people enjoy using, grounded in research and built for conversion.',
	),
	array(
		'slug'  => 'marketing-strategy-consulting',
		'icon'  => 'strategy',
		'title' => 'Marketing & Strategy Consulting',
		'text'  => 'Data-informed marketing plans and growth strategy for teams that want direction, not guesswork.',
	),
	array(
		'slug'  => 'cybersecurity',
		'icon'  => 'shield',
		'title' => 'Cybersecurity',
		'text'  => 'Practical security audits and hardening for web platforms and internal systems.',
	),
	array(
		'slug'  => 'ai-solutions',
		'icon'  => 'ai',
		'title' => 'AI Solutions',
		'text'  => 'Custom AI tooling and automation that removes repetitive work and unlocks new capability.',
	),
);

$trust = array(
	array(
		'icon'  => 'ethics',
		'title' => 'Ethical Innovation',
		'text'  => 'We build technology that respects the people who use it — transparent, secure, and accountable by design.',
	),
	array(
		'icon'  => 'collab',
		'title' => 'Direct Collaboration',
		'text'  => 'You work directly with the people building your project, not a relay of account managers.',
	),
	array(
		'icon'  => 'clock',
		'title' => '48h Response',
		'text'  => 'Every inquiry gets a real, considered response within 48 hours — guaranteed.',
	),
);
?>

<article class="ogc-home">

	<section class="ogc-home-hero" aria-labelledby="ogc-home-hero-heading">
		<div class="ogc-home-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-home-hero-heading" class="ogc-home-hero__title">
				<?php esc_html_e( 'Empowering ideas. Engineering the', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'future.', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-home-hero__lead">
				<?php
				esc_html_e(
					'OGC NewFinity is a digital agency in The Hague, Netherlands, building AI-driven software, secure web platforms, and ethical technology strategy for ambitious teams.',
					'ogc-newfinity'
				);
				?>
			</p>
			<div class="ogc-home-hero__actions">
				<a class="ogc-btn" href="<?php echo esc_url( $submit_url ); ?>">
					<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
				</a>
				<a class="ogc-btn" href="<?php echo esc_url( $services_url ); ?>">
					<?php esc_html_e( 'Our Services', 'ogc-newfinity' ); ?>
				</a>
			</div>
		</div>
		<div class="ogc-home-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'brand-cube' ); ?>
		</div>
	</section>

	<section class="ogc-home-services" aria-labelledby="ogc-home-services-heading">
		<header class="ogc-section-header">
			<p class="ogc-section-eyebrow"><?php esc_html_e( 'WHAT WE DO', 'ogc-newfinity' ); ?></p>
			<h2 id="ogc-home-services-heading" class="ogc-section-title">
				<?php esc_html_e( 'Our Services', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-section-intro">
				<?php
				echo esc_html(
					'Six core capabilities, delivered end-to-end — from first concept to long-term'
				);
				?>
				&nbsp;<?php esc_html_e( 'support.', 'ogc-newfinity' ); ?>
			</p>
		</header>

		<div class="ogc-glass-grid ogc-home-services__grid">
			<?php foreach ( $services as $service ) : ?>
				<a
					class="glass-card ogc-service-card"
					href="<?php echo esc_url( $services_url . '#' . $service['slug'] ); ?>"
				>
					<?php
					ogc_icon(
						$service['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-service-card__icon',
						)
					);
					?>
					<h3 class="ogc-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
					<p class="ogc-service-card__text"><?php echo esc_html( $service['text'] ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-home-trust" aria-labelledby="ogc-home-trust-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-home-trust-heading" class="ogc-section-title">
				<?php esc_html_e( 'Why teams work with us', 'ogc-newfinity' ); ?>
			</h2>
		</header>

		<div class="ogc-home-trust__grid">
			<?php foreach ( $trust as $item ) : ?>
				<div class="glass-card ogc-trust-card">
					<?php
					ogc_icon(
						$item['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-trust-card__icon',
						)
					);
					?>
					<h3 class="ogc-trust-card__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="ogc-trust-card__text"><?php echo esc_html( $item['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-home-cta" aria-labelledby="ogc-home-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-home-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Have a project in mind?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					"Tell us what you're building. Every submission gets a free, confidential review.",
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
