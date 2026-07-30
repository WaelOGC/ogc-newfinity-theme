<?php
/**
 * Blog & Insights posts index — /blog/ (page_for_posts).
 *
 * Simple text hero only — no 3D/canvas animation.
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url      = home_url( '/submit-idea/' );
$categories      = ogc_blog_categories();
$active_category = '';
$paged           = ogc_get_blog_paged();

if ( isset( $_GET['category'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$requested = sanitize_title( wp_unslash( $_GET['category'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$allowed   = wp_list_pluck( $categories, 'slug' );
	if ( in_array( $requested, $allowed, true ) ) {
		$active_category = $requested;
	}
}

$listing = ogc_get_blog_listing( $active_category, $paged );
?>

<article class="ogc-page ogc-page--blog">

	<section class="ogc-page-hero ogc-blog-hero" aria-labelledby="ogc-blog-hero-heading">
		<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
		<h1 id="ogc-blog-hero-heading" class="ogc-page-hero__title">
			<?php esc_html_e( 'Blog &', 'ogc-newfinity' ); ?>
			<span class="hero-shimmer"><?php esc_html_e( 'Insights', 'ogc-newfinity' ); ?></span>
		</h1>
		<p class="ogc-page-hero__lead">
			<?php
			esc_html_e(
				'Ideas, guides, and updates from the OGC NewFinity team.',
				'ogc-newfinity'
			);
			?>
		</p>
	</section>

	<div class="ogc-blog-layout">
		<div class="ogc-blog-layout__main">

			<section class="ogc-blog-listing" aria-labelledby="ogc-blog-listing-heading">
				<h2 id="ogc-blog-listing-heading" class="screen-reader-text">
					<?php esc_html_e( 'Articles', 'ogc-newfinity' ); ?>
				</h2>

				<div
					class="ogc-blog-filters"
					role="group"
					aria-label="<?php esc_attr_e( 'Filter articles by category', 'ogc-newfinity' ); ?>"
				>
					<button
						type="button"
						class="ogc-btn ogc-btn--sm ogc-blog-filter<?php echo '' === $active_category ? ' is-active' : ''; ?>"
						data-category="all"
						aria-pressed="<?php echo '' === $active_category ? 'true' : 'false'; ?>"
					>
						<?php esc_html_e( 'All', 'ogc-newfinity' ); ?>
					</button>
					<?php foreach ( $categories as $cat ) : ?>
						<?php $is_active = ( $active_category === $cat['slug'] ); ?>
						<button
							type="button"
							class="ogc-btn ogc-btn--sm ogc-blog-filter<?php echo $is_active ? ' is-active' : ''; ?>"
							data-category="<?php echo esc_attr( $cat['slug'] ); ?>"
							aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
						>
							<?php echo esc_html( $cat['name'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div
					id="ogc-blog-grid-wrap"
					class="ogc-blog-grid-wrap"
					aria-live="polite"
					aria-busy="false"
				>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built via escaped template parts.
					echo $listing['html'];
					?>
				</div>

				<div id="ogc-blog-pagination-wrap" class="ogc-blog-pagination-wrap">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside helper.
					echo $listing['pagination_html'];
					?>
				</div>
			</section>

		</div>

		<?php get_template_part( 'template-parts/blog', 'sidebar' ); ?>
	</div>

	<section class="ogc-home-cta" aria-labelledby="ogc-blog-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-blog-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Have a project in mind?', 'ogc-newfinity' ); ?>
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
