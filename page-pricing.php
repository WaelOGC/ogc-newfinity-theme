<?php
/**
 * Template Name: Pricing
 * Pricing catalogue — assign to a page with slug `pricing` when ready to publish.
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url    = home_url( '/submit-idea/' );
$service_lines = ogc_get_pricing_data();
$faq_items     = ogc_get_pricing_faq();

$ogc_page_id      = (int) get_queried_object_id();
$ogc_layout_class = ogc_get_page_layout_class( $ogc_page_id );
$ogc_show_widgets = ogc_page_enable_widget_area( $ogc_page_id ) && ! ogc_page_should_hide( 'widget_area', $ogc_page_id );

/**
 * Index of the “most popular” package within a list (middle; second when two).
 *
 * @param array<int, array<string, mixed>> $packages Package rows.
 * @return int
 */
$ogc_pricing_popular_index = static function ( array $packages ) {
	$count = count( $packages );
	if ( $count < 1 ) {
		return -1;
	}
	if ( 2 === $count ) {
		return 1;
	}

	return (int) floor( ( $count - 1 ) / 2 );
};

/**
 * Render package cards for one group.
 *
 * @param array<int, array<string, mixed>> $packages Packages.
 * @param string                           $submit_url CTA URL.
 * @param callable                         $ogc_pricing_popular_index Popular index resolver.
 */
$ogc_render_package_cards = static function ( array $packages, $submit_url, $ogc_pricing_popular_index ) {
	if ( empty( $packages ) ) {
		return;
	}
	$popular = $ogc_pricing_popular_index( $packages );
	?>
	<div class="ogc-glass-grid ogc-pricing-packages">
		<?php foreach ( $packages as $index => $package ) : ?>
			<?php
			$is_popular = ( $index === $popular );
			$included   = isset( $package['included'] ) && is_array( $package['included'] ) ? $package['included'] : array();
			$shown      = array_slice( $included, 0, 6 );
			$extra      = count( $included ) - count( $shown );
			?>
			<article class="glass-card ogc-pricing-package<?php echo $is_popular ? ' ogc-pricing-package--popular' : ''; ?>">
				<?php if ( $is_popular ) : ?>
					<p class="ogc-pricing-package__badge"><?php esc_html_e( 'Most popular', 'ogc-newfinity' ); ?></p>
				<?php endif; ?>
				<h3 class="ogc-pricing-package__name"><?php echo esc_html( $package['name'] ); ?></h3>
				<p class="ogc-pricing-package__price">
					<span class="ogc-pricing-package__amount"><?php echo esc_html( $package['price'] ); ?></span>
					<?php if ( ! empty( $package['price_note'] ) ) : ?>
						<span class="ogc-pricing-package__note"><?php echo esc_html( $package['price_note'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $package['price_recurring'] ) ) : ?>
						<span class="ogc-pricing-package__recurring">
							<?php
							printf(
								/* translators: %s: monthly management fee */
								esc_html__( '+ %s management', 'ogc-newfinity' ),
								esc_html( $package['price_recurring'] )
							);
							?>
						</span>
					<?php endif; ?>
				</p>
				<?php if ( ! empty( $shown ) ) : ?>
					<ul class="ogc-pricing-package__list">
						<?php foreach ( $shown as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
						<?php if ( $extra > 0 ) : ?>
							<li class="ogc-pricing-package__more">
								<?php
								printf(
									/* translators: %d: additional included items not shown */
									esc_html( _n( '+%d more included', '+%d more included', $extra, 'ogc-newfinity' ) ),
									(int) $extra
								);
								?>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
				<a class="ogc-btn ogc-pricing-package__cta" href="<?php echo esc_url( $submit_url ); ?>">
					<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
};

/**
 * Label + price rows inside an accordion (add-ons, repair).
 *
 * @param array<int, array{label?: string, price?: string, name?: string, included?: string}> $rows Rows.
 */
$ogc_render_price_rows = static function ( array $rows ) {
	if ( empty( $rows ) ) {
		return;
	}
	?>
	<ul class="ogc-pricing-price-list">
		<?php foreach ( $rows as $row ) : ?>
			<?php
			$label = isset( $row['label'] ) ? $row['label'] : ( isset( $row['name'] ) ? $row['name'] : '' );
			$price = isset( $row['price'] ) ? $row['price'] : '';
			?>
			<li class="ogc-pricing-price-list__item">
				<span class="ogc-pricing-price-list__label"><?php echo esc_html( $label ); ?></span>
				<span class="ogc-pricing-price-list__price"><?php echo esc_html( $price ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
};

/**
 * Subscription-style rows with included copy.
 *
 * @param array<int, array{name: string, price: string, included: string}> $rows Rows.
 */
$ogc_render_plan_cards = static function ( array $rows ) {
	if ( empty( $rows ) ) {
		return;
	}
	?>
	<div class="ogc-pricing-plan-cards">
		<?php foreach ( $rows as $row ) : ?>
			<div class="ogc-pricing-plan-card">
				<h4 class="ogc-pricing-plan-card__name"><?php echo esc_html( $row['name'] ); ?></h4>
				<p class="ogc-pricing-plan-card__price"><?php echo esc_html( $row['price'] ); ?></p>
				<?php if ( ! empty( $row['included'] ) ) : ?>
					<p class="ogc-pricing-plan-card__included"><?php echo esc_html( $row['included'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
};
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
					'Fixed packages where scope is clear. Discovery and custom quotes where it is not.',
					'ogc-newfinity'
				);
				?>
			</p>
			<p class="ogc-pricing-vat-notice" role="note">
				<?php esc_html_e( 'All prices in EUR, excluding 21% VAT (excl. BTW).', 'ogc-newfinity' ); ?>
			</p>
		</div>
	</section>

	<?php if ( $ogc_show_widgets ) : ?>
		<div class="ogc-page-body ogc-page-body--with-widgets">
			<div class="ogc-page-body__main">
	<?php endif; ?>

	<div class="ogc-pricing-tabs" data-ogc-pricing-tabs>
		<div class="ogc-pricing-tabs__bar-wrap">
			<div
				class="ogc-pricing-tabs__list"
				role="tablist"
				aria-label="<?php esc_attr_e( 'Service line pricing', 'ogc-newfinity' ); ?>"
			>
				<?php foreach ( $service_lines as $tab_index => $line ) : ?>
					<button
						type="button"
						class="ogc-pricing-tabs__tab"
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
								'class' => 'ogc-pricing-tabs__tab-icon',
							)
						);
						?>
						<span class="ogc-pricing-tabs__tab-label"><?php echo esc_html( $line['title'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<?php foreach ( $service_lines as $panel_index => $line ) : ?>
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

				<?php
				$package_groups = array();
				if ( ! empty( $line['packages'] ) ) {
					$package_groups[] = array(
						'heading'  => '',
						'packages' => $line['packages'],
					);
				}
				if ( ! empty( $line['package_groups'] ) ) {
					$package_groups = array_merge( $package_groups, $line['package_groups'] );
				}

				foreach ( $package_groups as $group ) :
					if ( empty( $group['packages'] ) ) {
						continue;
					}
					?>
					<?php if ( ! empty( $group['heading'] ) ) : ?>
						<h3 class="ogc-pricing-group-heading"><?php echo esc_html( $group['heading'] ); ?></h3>
					<?php endif; ?>
					<?php
					$ogc_render_package_cards( $group['packages'], $submit_url, $ogc_pricing_popular_index );
				endforeach;
				?>

				<div class="ogc-pricing-accordions">
					<?php if ( ! empty( $line['addons'] ) ) : ?>
						<details class="ogc-pricing-accordion">
							<summary class="ogc-pricing-accordion__summary">
								<span><?php esc_html_e( 'Add-ons', 'ogc-newfinity' ); ?></span>
							</summary>
							<div class="ogc-pricing-accordion__body">
								<?php $ogc_render_price_rows( $line['addons'] ); ?>
							</div>
						</details>
					<?php endif; ?>

					<?php
					$subscription_blocks = array();
					if ( ! empty( $line['subscriptions'] ) ) {
						if ( isset( $line['subscriptions']['rows'] ) ) {
							if ( ! empty( $line['subscriptions']['rows'] ) ) {
								$subscription_blocks[] = $line['subscriptions'];
							}
						} else {
							foreach ( $line['subscriptions'] as $block ) {
								if ( ! empty( $block['rows'] ) ) {
									$subscription_blocks[] = $block;
								}
							}
						}
					}

					foreach ( $subscription_blocks as $block ) :
						$block_heading = ! empty( $block['heading'] ) ? $block['heading'] : __( 'Plans', 'ogc-newfinity' );
						?>
						<details class="ogc-pricing-accordion">
							<summary class="ogc-pricing-accordion__summary">
								<span><?php echo esc_html( $block_heading ); ?></span>
							</summary>
							<div class="ogc-pricing-accordion__body">
								<?php $ogc_render_plan_cards( $block['rows'] ); ?>
							</div>
						</details>
					<?php endforeach; ?>

					<?php if ( ! empty( $line['repair']['rows'] ) ) : ?>
						<details class="ogc-pricing-accordion">
							<summary class="ogc-pricing-accordion__summary">
								<span><?php echo esc_html( $line['repair']['heading'] ); ?></span>
							</summary>
							<div class="ogc-pricing-accordion__body">
								<?php $ogc_render_price_rows( $line['repair']['rows'] ); ?>
							</div>
						</details>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $line['notes'] ) ) : ?>
					<ul class="ogc-pricing-footnotes" role="note">
						<?php foreach ( array_slice( $line['notes'], 0, 3 ) as $note ) : ?>
							<li><?php echo esc_html( $note ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</div>

	<section class="ogc-pricing-faq" aria-labelledby="ogc-pricing-faq-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-pricing-faq-heading" class="ogc-section-title">
				<?php esc_html_e( 'Pricing questions', 'ogc-newfinity' ); ?>
			</h2>
		</header>
		<div class="ogc-pricing-faq__list">
			<?php foreach ( $faq_items as $faq ) : ?>
				<details class="glass-card ogc-pricing-faq__item">
					<summary class="ogc-pricing-faq__summary">
						<span class="ogc-pricing-faq__question"><?php echo esc_html( $faq['question'] ); ?></span>
					</summary>
					<div class="ogc-pricing-faq__answer">
						<p><?php echo esc_html( $faq['answer'] ); ?></p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-home-cta" aria-labelledby="ogc-pricing-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-pricing-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Ready for a quote?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					'Tell us what you need — we confirm scope, timeline, and cost before anything is signed.',
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
