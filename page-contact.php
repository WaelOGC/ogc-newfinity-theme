<?php
/**
 * Template Name: Contact
 *
 * Contact / Get in Touch page template — slug: contact (/contact/).
 *
 * @package OGC_NewFinity
 */

get_header();

$services_url = home_url( '/services/' );
$form_action  = admin_url( 'admin-post.php' );

$form_sent  = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- status flag only.
$form_error = isset( $_GET['error'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- status flag only.

$service_pills = array(
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

$categories = ogc_contact_categories();

$capability_cards = array(
	array(
		'icon'  => 'ai',
		'title' => 'AI & Automation',
		'text'  => 'Custom AI tooling and workflow automation that removes repetitive work — built around how your team actually operates, not a generic chatbot bolted on top.',
	),
	array(
		'icon'  => 'code',
		'title' => 'Web, App & Design',
		'text'  => 'Software and app development, bespoke WordPress builds, and research-informed UI/UX — from first concept through a polished interface people enjoy using.',
	),
	array(
		'icon'  => 'shield',
		'title' => 'Security & Strategy',
		'text'  => 'Practical cybersecurity hardening plus marketing and growth strategy grounded in your real numbers — so what you ship stays secure and keeps moving forward.',
	),
);
?>

<article class="ogc-page ogc-page--contact">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-contact-hero-heading">
		<div class="ogc-split-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-contact-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'Get in', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'Touch', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php
				esc_html_e(
					'The Hague, Netherlands — every message gets a real reply within 48 hours.',
					'ogc-newfinity'
				);
				?>
			</p>

			<ul class="ogc-service-tags ogc-contact-pills" aria-label="<?php echo esc_attr__( 'Our services', 'ogc-newfinity' ); ?>">
				<?php foreach ( $service_pills as $pill ) : ?>
					<li>
						<a
							class="ogc-service-tag"
							href="<?php echo esc_url( $services_url . '#' . $pill['slug'] ); ?>"
						>
							<?php
							ogc_icon(
								$pill['icon'],
								array(
									'size'  => 'sm',
									'class' => 'ogc-service-tag__icon',
								)
							);
							?>
							<span class="ogc-service-tag__label"><?php echo esc_html( $pill['title'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="ogc-split-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'envelope-send' ); ?>
		</div>
	</section>

	<section class="ogc-contact-layout" aria-labelledby="ogc-contact-form-heading">
		<div class="ogc-contact-layout__form">
			<header class="ogc-section-header">
				<h2 id="ogc-contact-form-heading" class="ogc-section-title">
					<?php esc_html_e( 'Send a Message', 'ogc-newfinity' ); ?>
				</h2>
				<p class="ogc-section-intro">
					<?php
					esc_html_e(
						'Tell us what you need — a project question, a partnership idea, or something else. We read every message ourselves.',
						'ogc-newfinity'
					);
					?>
				</p>
			</header>

			<?php if ( $form_sent ) : ?>
				<div class="glass-card ogc-contact-notice ogc-contact-notice--success" role="status">
					<p><?php esc_html_e( 'Thanks — your message is on its way. We\'ll get back to you within 48 hours.', 'ogc-newfinity' ); ?></p>
				</div>
			<?php elseif ( $form_error ) : ?>
				<div class="glass-card ogc-contact-notice ogc-contact-notice--error" role="alert">
					<p><?php esc_html_e( 'Something went wrong. Please check the required fields and try again.', 'ogc-newfinity' ); ?></p>
				</div>
			<?php endif; ?>

			<form
				class="ogc-contact-form glass-card"
				method="post"
				action="<?php echo esc_url( $form_action ); ?>"
				novalidate
			>
				<input type="hidden" name="action" value="ogc_contact_submit">
				<?php wp_nonce_field( 'ogc_contact_submit', 'ogc_contact_nonce' ); ?>

				<div class="ogc-contact-honeypot" aria-hidden="true">
					<label for="ogc_company_url"><?php esc_html_e( 'Company website', 'ogc-newfinity' ); ?></label>
					<input
						type="text"
						id="ogc_company_url"
						name="ogc_company_url"
						value=""
						tabindex="-1"
						autocomplete="off"
					>
				</div>

				<div class="ogc-contact-form__field">
					<label for="ogc_name">
						<?php esc_html_e( 'Name', 'ogc-newfinity' ); ?>
						<span class="required" aria-hidden="true">*</span>
					</label>
					<input
						type="text"
						id="ogc_name"
						name="ogc_name"
						required
						autocomplete="name"
					>
				</div>

				<div class="ogc-contact-form__field">
					<label for="ogc_email">
						<?php esc_html_e( 'Email', 'ogc-newfinity' ); ?>
						<span class="required" aria-hidden="true">*</span>
					</label>
					<input
						type="email"
						id="ogc_email"
						name="ogc_email"
						required
						autocomplete="email"
					>
				</div>

				<div class="ogc-contact-form__field">
					<label for="ogc_subject">
						<?php esc_html_e( 'Subject', 'ogc-newfinity' ); ?>
						<span class="required" aria-hidden="true">*</span>
					</label>
					<select id="ogc_subject" name="ogc_subject" required>
						<?php foreach ( $categories as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, 'general' ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ogc-contact-form__field">
					<label for="ogc_message">
						<?php esc_html_e( 'Message', 'ogc-newfinity' ); ?>
						<span class="required" aria-hidden="true">*</span>
					</label>
					<textarea
						id="ogc_message"
						name="ogc_message"
						rows="6"
						required
					></textarea>
				</div>

				<button type="submit" class="ogc-btn">
					<?php esc_html_e( 'Send a Message', 'ogc-newfinity' ); ?>
				</button>
			</form>
		</div>

		<aside class="ogc-contact-layout__aside" aria-labelledby="ogc-contact-aside-heading">
			<h2 id="ogc-contact-aside-heading" class="screen-reader-text">
				<?php esc_html_e( 'How we can help', 'ogc-newfinity' ); ?>
			</h2>

			<div class="ogc-contact-aside__cards">
				<?php foreach ( $capability_cards as $card ) : ?>
					<div class="glass-card ogc-pillar-card">
						<?php
						ogc_icon(
							$card['icon'],
							array(
								'size'  => 'lg',
								'class' => 'ogc-pillar-card__icon',
							)
						);
						?>
						<h3 class="ogc-pillar-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="ogc-pillar-card__text"><?php echo esc_html( $card['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="glass-card ogc-contact-location">
				<?php
				ogc_icon(
					'building',
					array(
						'size'  => 'md',
						'class' => 'ogc-contact-location__icon',
					)
				);
				?>
				<div class="ogc-contact-location__copy">
					<p class="ogc-contact-location__label"><?php esc_html_e( 'Based in', 'ogc-newfinity' ); ?></p>
					<p class="ogc-contact-location__place"><?php esc_html_e( 'The Hague, Netherlands', 'ogc-newfinity' ); ?></p>
				</div>
			</div>
		</aside>
	</section>

</article>

<?php
get_footer();
