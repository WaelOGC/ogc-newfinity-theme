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

$how_we_work = array(
	array(
		'title' => "Tell us what you're building",
		'text'  => 'Submit your idea or send us a message — project title, category, a few lines about what you need. No forms with twenty fields, no sales call required to get started.',
	),
	array(
		'title' => 'Our AI assistant gets the full picture',
		'text'  => 'Tell us what you\'re building through a quick guided conversation with our AI assistant — answer a few questions, upload files if you have them. Within minutes, we already have most of what we need to understand your project.',
	),
	array(
		'title' => 'We move straight to a focused meeting',
		'text'  => "Within 48 hours, you'll hear back for a quick online meeting — grounded in what we already know, not starting from zero. We confirm scope, timeline, and cost together before anything is signed.",
	),
	array(
		'title' => 'We build, you stay in the loop',
		'text'  => "Defined milestones, direct access to the people building your project — no relay through account managers. You always know what's happening and why.",
	),
	array(
		'title' => 'Launch, then support',
		'text'  => "We don't disappear after launch. Post-launch support is part of how we work, not an upsell you have to ask for.",
	),
);
?>

<article class="ogc-page ogc-page--services">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-services-hero-heading">
		<?php ogc_render_hero_bg( 'services' ); ?>
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
			<?php get_template_part( 'template-parts/hero', 'code-screen' ); ?>
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

	<section class="ogc-services-process" aria-labelledby="ogc-services-process-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-services-process-heading" class="ogc-section-title">
				<?php esc_html_e( 'How We Work', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-section-intro">
				<?php esc_html_e( "From first message to launch — here's what the process actually looks like.", 'ogc-newfinity' ); ?>
			</p>
		</header>

		<ol class="ogc-services-process__list">
			<?php foreach ( $how_we_work as $index => $step ) : ?>
				<li class="glass-card ogc-process-step">
					<span class="ogc-process-step__num" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
					<div class="ogc-process-step__body">
						<h3 class="ogc-process-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="ogc-process-step__text"><?php echo esc_html( $step['text'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
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
