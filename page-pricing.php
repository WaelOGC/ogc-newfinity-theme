<?php
/**
 * Template Name: Pricing
 * Pricing catalogue — assign to a page with slug `pricing` when ready to publish.
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url   = home_url( '/submit-idea/' );
$pricing_url  = home_url( '/pricing/' );
$service_lines = ogc_get_pricing_data();
$commercial    = ogc_get_pricing_commercial_terms();

$ogc_page_id      = (int) get_queried_object_id();
$ogc_layout_class = ogc_get_page_layout_class( $ogc_page_id );
$ogc_show_widgets = ogc_page_enable_widget_area( $ogc_page_id ) && ! ogc_page_should_hide( 'widget_area', $ogc_page_id );

/**
 * Render a pricing table (add-ons or subscriptions).
 *
 * @param string               $caption Accessible table caption.
 * @param array<int, string>   $columns Column header labels.
 * @param array<int, array>    $rows    Row cell values keyed by column index.
 */
$ogc_render_pricing_table = static function ( $caption, $columns, $rows ) {
	if ( empty( $rows ) ) {
		return;
	}
	?>
	<div class="ogc-pricing-table-wrap">
		<table class="ogc-pricing-table">
			<caption class="screen-reader-text"><?php echo esc_html( $caption ); ?></caption>
			<thead>
				<tr>
					<?php foreach ( $columns as $col ) : ?>
						<th scope="col"><?php echo esc_html( $col ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<?php foreach ( $row as $cell ) : ?>
							<td><?php echo esc_html( $cell ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
};
?>

<article class="ogc-page ogc-page--pricing<?php echo $ogc_layout_class ? ' ' . esc_attr( $ogc_layout_class ) : ''; ?><?php echo $ogc_show_widgets ? ' ogc-page--has-widgets' : ''; ?>">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-pricing-hero-heading">
		<?php ogc_render_hero_bg( $ogc_page_id ); ?>
		<div class="ogc-split-hero__copy">
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
		<div class="ogc-split-hero__visual ogc-pricing-hero__visual" aria-hidden="true">
			<div class="glass-card ogc-pricing-hero__card">
				<p class="ogc-pricing-hero__card-label"><?php esc_html_e( 'Six service lines', 'ogc-newfinity' ); ?></p>
				<ul class="ogc-pricing-hero__card-list">
					<?php foreach ( $service_lines as $line ) : ?>
						<li><?php echo esc_html( $line['title'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<nav class="ogc-pricing-jump" aria-label="<?php esc_attr_e( 'Service line pricing', 'ogc-newfinity' ); ?>">
		<ul class="ogc-pricing-jump__list">
			<?php foreach ( $service_lines as $line ) : ?>
				<li>
					<a class="ogc-pricing-jump__link" href="<?php echo esc_url( $pricing_url . '#' . $line['slug'] ); ?>">
						<?php echo esc_html( $line['title'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php if ( $ogc_show_widgets ) : ?>
		<div class="ogc-page-body ogc-page-body--with-widgets">
			<div class="ogc-page-body__main">
	<?php endif; ?>

	<?php foreach ( $service_lines as $line ) : ?>
		<section
			class="ogc-pricing-line"
			id="<?php echo esc_attr( $line['slug'] ); ?>"
			aria-labelledby="<?php echo esc_attr( 'ogc-pricing-' . $line['slug'] . '-heading' ); ?>"
		>
			<header class="ogc-pricing-line__header">
				<?php
				ogc_icon(
					$line['icon'],
					array(
						'size'  => 'lg',
						'class' => 'ogc-pricing-line__icon',
					)
				);
				?>
				<div class="ogc-pricing-line__heading-wrap">
					<h2 id="<?php echo esc_attr( 'ogc-pricing-' . $line['slug'] . '-heading' ); ?>" class="ogc-section-title">
						<?php echo esc_html( $line['title'] ); ?>
					</h2>
					<p class="ogc-section-intro"><?php echo esc_html( $line['intro'] ); ?></p>
				</div>
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
				?>
				<?php if ( ! empty( $group['heading'] ) ) : ?>
					<h3 class="ogc-pricing-subheading"><?php echo esc_html( $group['heading'] ); ?></h3>
				<?php endif; ?>
				<div class="ogc-glass-grid ogc-pricing-packages">
					<?php foreach ( $group['packages'] as $package ) : ?>
						<article class="glass-card ogc-pricing-package">
							<h4 class="ogc-pricing-package__name"><?php echo esc_html( $package['name'] ); ?></h4>
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
							<?php if ( ! empty( $package['included'] ) ) : ?>
								<ul class="ogc-pricing-package__list">
									<?php foreach ( $package['included'] as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<a class="ogc-btn ogc-pricing-package__cta" href="<?php echo esc_url( $submit_url ); ?>">
								<?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<?php if ( ! empty( $line['addons'] ) ) : ?>
				<div class="ogc-pricing-block">
					<h3 class="ogc-pricing-subheading"><?php esc_html_e( 'Add-ons', 'ogc-newfinity' ); ?></h3>
					<?php
					$addon_rows = array();
					foreach ( $line['addons'] as $addon ) {
						$addon_rows[] = array( $addon['label'], $addon['price'] );
					}
					$ogc_render_pricing_table(
						sprintf(
							/* translators: %s: service line title */
							__( 'Add-ons for %s', 'ogc-newfinity' ),
							$line['title']
						),
						array(
							__( 'Add-on', 'ogc-newfinity' ),
							__( 'Price', 'ogc-newfinity' ),
						),
						$addon_rows
					);
					?>
				</div>
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
				?>
				<div class="ogc-pricing-block">
					<?php if ( ! empty( $block['heading'] ) ) : ?>
						<h3 class="ogc-pricing-subheading"><?php echo esc_html( $block['heading'] ); ?></h3>
					<?php endif; ?>
					<?php
					$sub_rows = array();
					foreach ( $block['rows'] as $row ) {
						$sub_rows[] = array( $row['name'], $row['price'], $row['included'] );
					}
					$ogc_render_pricing_table(
						! empty( $block['heading'] ) ? $block['heading'] : $line['title'],
						array(
							__( 'Plan', 'ogc-newfinity' ),
							__( 'Price', 'ogc-newfinity' ),
							__( 'Included', 'ogc-newfinity' ),
						),
						$sub_rows
					);
					?>
				</div>
			<?php endforeach; ?>

			<?php if ( ! empty( $line['repair']['rows'] ) ) : ?>
				<div class="ogc-pricing-block">
					<h3 class="ogc-pricing-subheading"><?php echo esc_html( $line['repair']['heading'] ); ?></h3>
					<?php
					$repair_rows = array();
					foreach ( $line['repair']['rows'] as $row ) {
						$repair_rows[] = array( $row['label'], $row['price'] );
					}
					$ogc_render_pricing_table(
						$line['repair']['heading'],
						array(
							__( 'Service', 'ogc-newfinity' ),
							__( 'Price', 'ogc-newfinity' ),
						),
						$repair_rows
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $line['notes'] ) ) : ?>
				<div class="ogc-pricing-notes" role="note">
					<p class="ogc-pricing-notes__label"><?php esc_html_e( 'Good to know', 'ogc-newfinity' ); ?></p>
					<ul class="ogc-pricing-notes__list">
						<?php foreach ( $line['notes'] as $note ) : ?>
							<li><?php echo esc_html( $note ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</section>
	<?php endforeach; ?>

	<section class="ogc-pricing-terms" aria-labelledby="ogc-pricing-terms-heading">
		<header class="ogc-section-header">
			<h2 id="ogc-pricing-terms-heading" class="ogc-section-title">
				<?php esc_html_e( 'How pricing works', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-section-intro">
				<?php esc_html_e( 'What is included, what is billed separately, and how we handle deposits, revisions, and recurring services.', 'ogc-newfinity' ); ?>
			</p>
		</header>

		<div class="ogc-glass-grid ogc-pricing-terms__grid">
			<?php
			$term_blocks = array(
				array(
					'title' => __( 'Billed separately', 'ogc-newfinity' ),
					'items' => $commercial['separate_costs'],
				),
				array(
					'title' => __( 'Out-of-scope work', 'ogc-newfinity' ),
					'items' => $commercial['out_of_scope'],
				),
				array(
					'title' => __( 'VAT and invoicing', 'ogc-newfinity' ),
					'items' => $commercial['vat'],
				),
				array(
					'title' => __( 'Deposits and milestones', 'ogc-newfinity' ),
					'items' => $commercial['deposits'],
				),
				array(
					'title' => __( 'Scope and revisions', 'ogc-newfinity' ),
					'items' => $commercial['revisions'],
				),
				array(
					'title' => __( 'Recurring services', 'ogc-newfinity' ),
					'items' => $commercial['recurring'],
				),
			);

			foreach ( $term_blocks as $block ) :
				?>
				<div class="glass-card ogc-pricing-terms__card">
					<h3 class="ogc-pricing-terms__card-title"><?php echo esc_html( $block['title'] ); ?></h3>
					<ul class="ogc-pricing-terms__list">
						<?php foreach ( $block['items'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
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
