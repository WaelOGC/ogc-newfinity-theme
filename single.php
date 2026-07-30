<?php
/**
 * Single blog post template.
 *
 * @package OGC_NewFinity
 */

get_header();

$submit_url = home_url( '/submit-idea/' );
?>

<?php while ( have_posts() ) : ?>
	<?php
	the_post();
	$category     = ogc_get_post_primary_category();
	$reading_mins = ogc_get_reading_time_minutes();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ogc-page ogc-page--single-post' ); ?>>

		<div class="ogc-blog-layout">
			<div class="ogc-blog-layout__main">

				<figure class="ogc-single-cover glass-card">
					<div class="ogc-single-cover__media">
						<?php
						ogc_the_post_cover(
							array(
								'class'   => 'ogc-single-cover__img',
								'size'    => 'large',
								'loading' => 'eager',
							)
						);
						?>
						<span class="ogc-single-cover__scrim" aria-hidden="true"></span>
					</div>
					<figcaption class="ogc-single-cover__overlay">
						<?php if ( $category ) : ?>
							<p class="ogc-badge ogc-single-cover__badge"><?php echo esc_html( $category->name ); ?></p>
						<?php endif; ?>

						<?php the_title( '<h1 class="ogc-single-cover__title">', '</h1>' ); ?>

						<p class="ogc-single-cover__meta">
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
							<span class="ogc-single-cover__sep" aria-hidden="true">·</span>
							<span class="ogc-single-cover__reading">
								<?php
								printf(
									/* translators: %d: estimated reading time in minutes */
									esc_html( _n( '%d min read', '%d min read', $reading_mins, 'ogc-newfinity' ) ),
									(int) $reading_mins
								);
								?>
							</span>
						</p>
					</figcaption>
				</figure>

				<?php get_template_part( 'template-parts/blog', 'engage' ); ?>

				<div class="ogc-single-content ogc-entry__content">
					<?php the_content(); ?>
				</div>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>

			</div>

			<?php get_template_part( 'template-parts/blog', 'sidebar' ); ?>
		</div>

		<?php
		$related_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( $category ) {
			$related_args['cat'] = (int) $category->term_id;
		}

		$related = new WP_Query( $related_args );

		if ( $related->have_posts() ) :
			?>
			<section class="ogc-single-related" aria-labelledby="ogc-related-heading">
				<header class="ogc-section-header">
					<h2 id="ogc-related-heading" class="ogc-section-title">
						<?php esc_html_e( 'Related articles', 'ogc-newfinity' ); ?>
					</h2>
				</header>
				<div class="ogc-glass-grid ogc-blog-grid">
					<?php
					while ( $related->have_posts() ) {
						$related->the_post();
						get_template_part( 'template-parts/blog', 'post-card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>

		<section class="ogc-home-cta" aria-labelledby="ogc-single-cta-heading">
			<div class="glass-card ogc-home-cta__inner">
				<h2 id="ogc-single-cta-heading" class="ogc-home-cta__title">
					<?php esc_html_e( 'Ready to build something?', 'ogc-newfinity' ); ?>
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

<?php endwhile; ?>

<?php
get_footer();
