<?php
/**
 * Our Services page template — slug: services (/services/).
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url = home_url( '/submit-idea/' );

$services = array(
	array(
		'slug'  => 'software-app-development',
		'icon'  => 'code',
		'title' => 'Software & App Development',
		'text'  => 'Custom applications built for how your business actually operates — not forced into an off-the-shelf tool. From technical scoping through launch and post-launch support, we build for scale from day one.',
	),
	array(
		'slug'  => 'custom-wordpress-development',
		'icon'  => 'wordpress',
		'title' => 'Custom WordPress Development',
		'text'  => 'Bespoke WordPress builds and themes, not page-builder templates wearing your logo. We design around your actual content and workflows, so editing your own site never feels like a fight.',
	),
	array(
		'slug'  => 'ui-ux-design',
		'icon'  => 'design',
		'title' => 'UI/UX Design',
		'text'  => 'Interfaces grounded in how people actually behave, not how we assume they will. Research-informed design, built to convert — from wireframe to a polished, tested interface.',
	),
	array(
		'slug'  => 'marketing-strategy-consulting',
		'icon'  => 'strategy',
		'title' => 'Marketing & Strategy Consulting',
		'text'  => "Growth plans built on your actual numbers, not industry guesswork. We help you figure out what's working, what isn't, and what to do about it.",
	),
	array(
		'slug'  => 'cybersecurity',
		'icon'  => 'shield',
		'title' => 'Cybersecurity',
		'text'  => 'Practical security audits and hardening for web platforms and internal systems — the kind of security work that actually gets implemented, not just reported.',
	),
	array(
		'slug'  => 'ai-solutions',
		'icon'  => 'ai',
		'title' => 'AI Solutions',
		'text'  => 'Custom AI tooling and automation that removes repetitive work and unlocks new capability — built around your real workflows, not a generic chatbot bolted on top.',
	),
);
?>

<article class="ogc-page ogc-page--services">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-services-hero-heading">
		<div class="ogc-split-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-services-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'Our', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'Services', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php
				esc_html_e(
					'Six core capabilities, delivered end-to-end — from first concept to long-term support.',
					'ogc-newfinity'
				);
				?>
			</p>
		</div>
		<div class="ogc-split-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'orbit-atom' ); ?>
		</div>
	</section>

	<section class="ogc-page-services" aria-labelledby="ogc-services-grid-heading">
		<h2 id="ogc-services-grid-heading" class="screen-reader-text">
			<?php esc_html_e( 'Service capabilities', 'ogc-newfinity' ); ?>
		</h2>

		<div class="ogc-glass-grid ogc-page-services__grid">
			<?php foreach ( $services as $service ) : ?>
				<article
					class="glass-card ogc-service-card"
					id="<?php echo esc_attr( $service['slug'] ); ?>"
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
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-home-cta" aria-labelledby="ogc-services-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-services-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Not sure which service fits?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					"Tell us what you're building — we'll point you in the right direction, free.",
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
