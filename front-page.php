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
		'title' => ogc_get_response_time_text(),
		'text'  => 'Every inquiry gets a real, considered response within 48 hours — guaranteed.',
	),
);

$ogc_page_id      = (int) get_queried_object_id();
$ogc_layout_class = ogc_get_page_layout_class( $ogc_page_id );
$ogc_show_widgets = ogc_page_enable_widget_area( $ogc_page_id ) && ! ogc_page_should_hide( 'widget_area', $ogc_page_id );
?>

<article class="ogc-home<?php echo $ogc_layout_class ? ' ' . esc_attr( $ogc_layout_class ) : ''; ?><?php echo $ogc_show_widgets ? ' ogc-page--has-widgets' : ''; ?>">

	<section class="ogc-home-hero" aria-labelledby="ogc-home-hero-heading">
		<?php ogc_render_hero_bg( $ogc_page_id ); ?>
		<div class="ogc-home-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<?php if ( ! ogc_page_should_hide( 'title', $ogc_page_id ) ) : ?>
				<h1 id="ogc-home-hero-heading" class="ogc-home-hero__title">
					<?php esc_html_e( 'Empowering ideas. Engineering the', 'ogc-newfinity' ); ?>
					<span class="hero-shimmer"><?php esc_html_e( 'future.', 'ogc-newfinity' ); ?></span>
				</h1>
			<?php endif; ?>
			<?php $ogc_subtitle = ogc_get_page_subtitle( $ogc_page_id ); ?>
			<?php if ( $ogc_subtitle ) : ?>
				<p class="ogc-page-hero__subtitle"><?php echo esc_html( $ogc_subtitle ); ?></p>
			<?php endif; ?>
			<p class="ogc-home-hero__lead">
				<?php
				printf(
					/* translators: %s: location text */
					esc_html__( 'OGC NewFinity is a digital agency in %s, building AI-driven software, secure web platforms, and ethical technology strategy for ambitious teams.', 'ogc-newfinity' ),
					esc_html( ogc_get_location_text() )
				);
				?>
			</p>
			<div class="ogc-home-hero__actions">
				<a class="ogc-btn" href="<?php echo esc_url( $submit_url ); ?>">
					<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
				</a>
				<a class="ogc-btn ogc-btn-outline" href="<?php echo esc_url( $services_url ); ?>">
					<?php esc_html_e( 'Our Services', 'ogc-newfinity' ); ?>
				</a>
			</div>
		</div>
		<div class="ogc-home-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'brand-cube' ); ?>
		</div>
	</section>

	<?php if ( $ogc_show_widgets ) : ?>
		<div class="ogc-page-body ogc-page-body--with-widgets">
			<div class="ogc-page-body__main">
	<?php endif; ?>

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

	<?php if ( $ogc_show_widgets ) : ?>
			</div>
			<?php get_template_part( 'template-parts/page', 'widget-area' ); ?>
		</div>
	<?php endif; ?>

</article>

<?php
get_footer();
