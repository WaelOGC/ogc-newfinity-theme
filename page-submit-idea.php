<?php
/**
 * Template Name: Submit Your Idea
 *
 * Submit Your Idea page template — slug: submit-idea (/submit-idea/).
 * Manual form is the default; Amy Agent may replace it via ogc_amy_agent_is_active().
 *
 * @package OGC_NewFinity
 */

get_header();

$form_action = admin_url( 'admin-post.php' );

$form_sent  = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- status flag only.
$form_error = isset( $_GET['error'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- status flag only.

$categories = ogc_submit_idea_categories();

$trust_cards = array(
	array(
		'icon'  => 'services',
		'title' => 'Expert Review',
		'text'  => 'A senior specialist reads your idea and responds with practical, honest feedback — not a canned reply.',
	),
	array(
		'icon'  => 'lock',
		'title' => 'Confidential',
		'text'  => 'Your submission stays between you and our team. We never share project details with third parties.',
	),
	array(
		'icon'  => 'strategy',
		'title' => 'Fast Kickoff',
		'text'  => 'If there\'s a fit, we move quickly from review to a clear next step — so momentum isn\'t lost.',
	),
);
?>

<article class="ogc-page ogc-page--submit-idea">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-submit-idea-hero-heading">
		<div class="ogc-split-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-submit-idea-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'Submit Your', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'Idea', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php
				esc_html_e(
					'Share a project idea and get a free expert review from our team — no pitch deck required, just tell us what you\'re building.',
					'ogc-newfinity'
				);
				?>
			</p>
		</div>
		<div class="ogc-split-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'idea-blueprint' ); ?>
		</div>
	</section>

	<section class="ogc-contact-layout" aria-labelledby="ogc-submit-idea-form-heading">
		<div class="ogc-contact-layout__form">
			<header class="ogc-section-header">
				<h2 id="ogc-submit-idea-form-heading" class="ogc-section-title">
					<?php esc_html_e( 'Tell Us About Your Project', 'ogc-newfinity' ); ?>
				</h2>
				<p class="ogc-section-intro">
					<?php
					esc_html_e(
						'A clear title, the service area that fits best, and a short description are enough to get started.',
						'ogc-newfinity'
					);
					?>
				</p>
			</header>

			<?php if ( $form_sent ) : ?>
				<div class="glass-card ogc-contact-notice ogc-contact-notice--success" role="status">
					<p><?php esc_html_e( 'Thanks — your idea is on its way. We\'ll review it and get back to you soon.', 'ogc-newfinity' ); ?></p>
				</div>
			<?php elseif ( $form_error ) : ?>
				<div class="glass-card ogc-contact-notice ogc-contact-notice--error" role="alert">
					<p><?php esc_html_e( 'Something went wrong. Please check the required fields and try again.', 'ogc-newfinity' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! ogc_amy_agent_is_active() ) : ?>
				<form
					class="ogc-contact-form glass-card"
					method="post"
					action="<?php echo esc_url( $form_action ); ?>"
					novalidate
				>
					<input type="hidden" name="action" value="ogc_submit_idea">
					<?php wp_nonce_field( 'ogc_submit_idea', 'ogc_submit_idea_nonce' ); ?>
					<input type="hidden" name="ogc_submit_idea_ts" value="<?php echo esc_attr( (string) time() ); ?>">

					<?php /* Do not use name/label words that resemble real form fields (company, website, url, phone) — browser autofill/password managers can fill hidden honeypot fields and cause false-positive spam rejection of real users. */ ?>
					<div class="ogc-contact-honeypot" aria-hidden="true">
						<label for="ogc_hp_token"><?php esc_html_e( 'Leave this field empty', 'ogc-newfinity' ); ?></label>
						<input
							type="text"
							id="ogc_hp_token"
							name="ogc_hp_token"
							value=""
							tabindex="-1"
							autocomplete="off"
						>
					</div>

					<div class="ogc-contact-form__field">
						<label for="ogc_idea_title">
							<?php esc_html_e( 'Project Title', 'ogc-newfinity' ); ?>
							<span class="required" aria-hidden="true">*</span>
						</label>
						<input
							type="text"
							id="ogc_idea_title"
							name="ogc_idea_title"
							required
							autocomplete="off"
						>
					</div>

					<div class="ogc-contact-form__field">
						<label for="ogc_idea_category">
							<?php esc_html_e( 'Category', 'ogc-newfinity' ); ?>
							<span class="required" aria-hidden="true">*</span>
						</label>
						<select id="ogc_idea_category" name="ogc_idea_category" required>
							<?php foreach ( $categories as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>">
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="ogc-contact-form__field">
						<label for="ogc_idea_description">
							<?php esc_html_e( 'Description', 'ogc-newfinity' ); ?>
							<span class="required" aria-hidden="true">*</span>
						</label>
						<textarea
							id="ogc_idea_description"
							name="ogc_idea_description"
							rows="6"
							required
						></textarea>
						<p class="ogc-contact-form__note">
							<?php
							esc_html_e(
								'Links and URLs are not allowed in the description field for security reasons.',
								'ogc-newfinity'
							);
							?>
						</p>
					</div>

					<button type="submit" class="ogc-btn">
						<?php esc_html_e( 'Submit', 'ogc-newfinity' ); ?>
					</button>
				</form>
			<?php endif; ?>

			<?php do_action( 'ogc_submit_idea_render' ); ?>

			<p class="ogc-submit-idea-meta">
				<?php esc_html_e( 'The Hague, Netherlands · Free & Confidential', 'ogc-newfinity' ); ?>
			</p>
		</div>

		<aside class="ogc-contact-layout__aside" aria-labelledby="ogc-submit-idea-aside-heading">
			<h2 id="ogc-submit-idea-aside-heading" class="screen-reader-text">
				<?php esc_html_e( 'Why submit with us', 'ogc-newfinity' ); ?>
			</h2>

			<div class="ogc-contact-aside__cards">
				<?php foreach ( $trust_cards as $card ) : ?>
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
		</aside>
	</section>

</article>

<?php
get_footer();
