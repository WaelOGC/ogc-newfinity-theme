<?php
/**
 * Template Name: Pricing
 * Pricing catalogue — assign to a page with slug `pricing` when ready to publish.
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url    = home_url( '/submit-idea/' );
$contact_url   = home_url( '/contact/' );
$service_lines = ogc_get_pricing_data();
$faq_items     = ogc_get_pricing_faq();
$faq_allowed   = ogc_pricing_faq_allowed_html();

$ogc_page_id      = (int) get_queried_object_id();
$ogc_layout_class = ogc_get_page_layout_class( $ogc_page_id );
$ogc_show_widgets = ogc_page_enable_widget_area( $ogc_page_id ) && ! ogc_page_should_hide( 'widget_area', $ogc_page_id );
?>

<article
	class="ogc-page ogc-page--pricing<?php echo $ogc_layout_class ? ' ' . esc_attr( $ogc_layout_class ) : ''; ?><?php echo $ogc_show_widgets ? ' ogc-page--has-widgets' : ''; ?>"
	data-ogc-pricing
>

	<section class="ogc-page-hero ogc-split-hero ogc-pricing-hero" aria-labelledby="ogc-pricing-hero-heading">
		<?php ogc_render_hero_bg( $ogc_page_id ); ?>
		<div class="ogc-split-hero__copy ogc-pricing-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<?php if ( ! ogc_page_should_hide( 'title', $ogc_page_id ) ) : ?>
				<h1 id="ogc-pricing-hero-heading" class="ogc-page-hero__title">
					<?php esc_html_e( 'Packages &', 'ogc-newfinity' ); ?>
					<span class="hero-shimmer"><?php esc_html_e( 'Pricing', 'ogc-newfinity' ); ?></span>
				</h1>
			<?php endif; ?>
			<?php $ogc_subtitle = ogc_get_page_subtitle( $ogc_page_id ); ?>
			<?php if ( $ogc_subtitle ) : ?>
				<p class="ogc-page-hero__subtitle"><?php echo esc_html( $ogc_subtitle ); ?></p>
			<?php endif; ?>
			<p class="ogc-page-hero__lead">
				<?php
				esc_html_e(
					'Clear prices for clear scope. Pick a service, compare three packages, and send us your idea when one fits.',
					'ogc-newfinity'
				);
				?>
			</p>
			<ul class="ogc-pricing-hero__pills" aria-label="<?php esc_attr_e( 'Pricing summary', 'ogc-newfinity' ); ?>">
				<li><?php esc_html_e( 'Prices in EUR, excluding VAT', 'ogc-newfinity' ); ?></li>
				<li><?php esc_html_e( 'Fixed scope, written quote', 'ogc-newfinity' ); ?></li>
				<li><?php esc_html_e( 'Reply within 48 hours', 'ogc-newfinity' ); ?></li>
			</ul>
		</div>
		<div class="ogc-split-hero__visual ogc-pricing-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'pricing-tiers' ); ?>
		</div>
	</section>

	<?php if ( $ogc_show_widgets ) : ?>
		<div class="ogc-page-body ogc-page-body--with-widgets">
			<div class="ogc-page-body__main">
	<?php endif; ?>

	<div class="ogc-pricing-layout" data-ogc-pricing-tabs>
		<div class="ogc-pricing-layout__columns">
			<aside class="glass-card ogc-pricing-sidebar" aria-label="<?php esc_attr_e( 'Service line pricing', 'ogc-newfinity' ); ?>">
				<p class="ogc-pricing-sidebar__eyebrow" id="ogc-pricing-nav-label"><?php esc_html_e( 'Choose a service', 'ogc-newfinity' ); ?></p>
				<div
					class="ogc-pricing-nav"
					role="tablist"
					aria-labelledby="ogc-pricing-nav-label"
					aria-orientation="vertical"
					data-ogc-pricing-tablist
				>
					<?php foreach ( $service_lines as $tab_index => $line ) : ?>
						<button
							type="button"
							class="ogc-pricing-nav__tab"
							role="tab"
							id="<?php echo esc_attr( 'ogc-pricing-tab-' . $line['slug'] ); ?>"
							aria-selected="<?php echo 0 === $tab_index ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $line['slug'] ); ?>"
							tabindex="<?php echo 0 === $tab_index ? '0' : '-1'; ?>"
							data-ogc-pricing-tab="<?php echo esc_attr( $line['slug'] ); ?>"
						>
							<?php
							ogc_icon(
								$line['icon'],
								array(
									'size'  => 'sm',
									'class' => 'ogc-pricing-nav__tab-icon',
								)
							);
							?>
							<span class="ogc-pricing-nav__tab-label"><?php echo esc_html( $line['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="ogc-pricing-sidebar__footer">
					<p class="ogc-pricing-sidebar__helper">
						<?php
						esc_html_e(
							'Not sure which package fits? Describe your project and we will point you to the right one.',
							'ogc-newfinity'
						);
						?>
					</p>
					<a class="ogc-btn ogc-pricing-sidebar__quote ogc-pricing-btn--gold" href="<?php echo esc_url( $submit_url ); ?>">
						<?php esc_html_e( 'Get a quote', 'ogc-newfinity' ); ?>
					</a>
				</div>
			</aside>

			<div class="ogc-pricing-content">
				<?php foreach ( $service_lines as $line ) : ?>
					<section
						class="ogc-pricing-tabpanel"
						role="tabpanel"
						id="<?php echo esc_attr( $line['slug'] ); ?>"
						aria-labelledby="<?php echo esc_attr( 'ogc-pricing-tab-' . $line['slug'] ); ?>"
						data-ogc-pricing-panel="<?php echo esc_attr( $line['slug'] ); ?>"
						tabindex="-1"
					>
						<header class="ogc-pricing-tabpanel__header">
							<h2 class="ogc-section-title"><?php echo esc_html( $line['title'] ); ?></h2>
							<p class="ogc-section-intro"><?php echo esc_html( $line['intro'] ); ?></p>
						</header>

						<div class="ogc-pricing-packages">
							<?php foreach ( $line['packages'] as $package ) : ?>
								<?php
								$is_popular   = ! empty( $package['popular'] );
								$amount_class = 'ogc-pricing-package__amount';
								if ( ! preg_match( '/^€/', (string) $package['price'] ) ) {
									$amount_class .= ' ogc-pricing-package__amount--text';
								}
								$cta_label  = sprintf(
									/* translators: %s: package name */
									__( 'Choose %s', 'ogc-newfinity' ),
									$package['name']
								);
								?>
								<article class="glass-card ogc-pricing-package<?php echo $is_popular ? ' ogc-pricing-package--popular' : ''; ?>">
									<div class="ogc-pricing-package__head">
										<h3 class="ogc-pricing-package__name"><?php echo esc_html( $package['name'] ); ?></h3>
										<?php if ( $is_popular ) : ?>
											<span class="ogc-pricing-package__badge"><?php esc_html_e( 'MOST POPULAR', 'ogc-newfinity' ); ?></span>
										<?php endif; ?>
									</div>
									<p class="ogc-pricing-package__fit"><?php echo esc_html( $package['fit'] ); ?></p>
									<div class="ogc-pricing-package__price-block">
										<?php if ( ! empty( $package['price_prefix'] ) ) : ?>
											<span class="ogc-pricing-package__prefix"><?php echo esc_html( $package['price_prefix'] ); ?></span>
										<?php else : ?>
											<span class="ogc-pricing-package__prefix" aria-hidden="true"></span>
										<?php endif; ?>
										<span class="<?php echo esc_attr( $amount_class ); ?>"><?php echo esc_html( $package['price'] ); ?></span>
										<span class="ogc-pricing-package__unit"><?php echo esc_html( $package['unit'] ); ?></span>
									</div>
									<a
										class="ogc-btn ogc-pricing-package__cta<?php echo $is_popular ? ' ogc-pricing-btn--gold' : ' ogc-btn-outline'; ?>"
										href="<?php echo esc_url( $submit_url ); ?>"
									>
										<?php echo esc_html( $cta_label ); ?>
									</a>
									<ul class="ogc-pricing-package__features">
										<?php foreach ( $package['features'] as $feature ) : ?>
											<li>
												<svg class="ogc-pricing-package__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
													<path d="M5 12.5l4.5 4.5L19 7.5"></path>
												</svg>
												<span><?php echo esc_html( $feature ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								</article>
							<?php endforeach; ?>
						</div>

						<?php if ( ! empty( $line['extras'] ) ) : ?>
							<div class="ogc-pricing-extras">
								<?php foreach ( $line['extras'] as $group ) : ?>
									<section class="ogc-pricing-extras__panel">
										<h3 class="ogc-pricing-extras__title"><?php echo esc_html( $group['title'] ); ?></h3>
										<?php foreach ( $group['rows'] as $row ) : ?>
											<div class="ogc-pricing-extras__row">
												<span class="ogc-pricing-extras__label"><?php echo esc_html( $row['label'] ); ?></span>
												<span class="ogc-pricing-extras__price"><?php echo esc_html( $row['price'] ); ?></span>
											</div>
										<?php endforeach; ?>
									</section>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $line['notes'] ) ) : ?>
							<ul class="ogc-pricing-footnotes">
								<?php foreach ( $line['notes'] as $note ) : ?>
									<li><?php echo esc_html( $note ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<section class="ogc-pricing-faq" id="faq" aria-labelledby="ogc-pricing-faq-heading">
		<div class="ogc-pricing-faq__layout">
			<div class="ogc-pricing-faq__intro">
				<h2 id="ogc-pricing-faq-heading" class="ogc-section-title">
					<?php esc_html_e( 'Pricing questions', 'ogc-newfinity' ); ?>
				</h2>
				<p class="ogc-pricing-faq__lead">
					<?php esc_html_e( 'Short answers, with a link to the page that covers each one in full.', 'ogc-newfinity' ); ?>
				</p>
				<a class="ogc-pricing-faq__more" href="<?php echo esc_url( $contact_url ); ?>">
					<?php esc_html_e( 'Ask something else', 'ogc-newfinity' ); ?>
				</a>
			</div>
			<div class="ogc-pricing-faq__list">
				<?php foreach ( $faq_items as $faq_index => $faq ) : ?>
					<details class="ogc-pricing-faq__item"<?php echo 0 === $faq_index ? ' open' : ''; ?>>
						<summary class="ogc-pricing-faq__summary">
							<span class="ogc-pricing-faq__question"><?php echo esc_html( $faq['question'] ); ?></span>
							<svg class="ogc-pricing-faq__chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M6 9l6 6 6-6"></path>
							</svg>
						</summary>
						<div class="ogc-pricing-faq__answer">
							<p><?php echo wp_kses( $faq['answer'], $faq_allowed ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="ogc-pricing-cta" aria-labelledby="ogc-pricing-cta-heading">
		<div class="glass-card ogc-pricing-cta__inner">
			<h2 id="ogc-pricing-cta-heading" class="ogc-pricing-cta__title">
				<?php esc_html_e( 'Ready for a quote?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-pricing-cta__text">
				<?php
				esc_html_e(
					'Share your brief or book a call — we confirm scope, timeline, and cost before anything is signed.',
					'ogc-newfinity'
				);
				?>
			</p>
			<div class="ogc-pricing-cta__actions">
				<a class="ogc-btn ogc-pricing-btn--gold ogc-pricing-cta__btn" href="<?php echo esc_url( $submit_url ); ?>">
					<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
				</a>
				<a class="ogc-btn ogc-btn-outline ogc-pricing-cta__btn" href="<?php echo esc_url( $contact_url ); ?>">
					<?php esc_html_e( 'Contact us', 'ogc-newfinity' ); ?>
				</a>
			</div>
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
