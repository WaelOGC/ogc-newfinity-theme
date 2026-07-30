<?php
/**
 * Blog sidebar — recent posts, categories, ad slot, newsletter.
 * Used on single posts and the blog archive (desktop/tablet only via CSS).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$recent = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$categories = ogc_blog_categories();
?>

<aside class="ogc-blog-sidebar" aria-label="<?php esc_attr_e( 'Blog sidebar', 'ogc-newfinity' ); ?>">

	<?php if ( is_active_sidebar( 'ogc-blog-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'ogc-blog-sidebar' ); ?>
	<?php else : ?>

		<section class="glass-card ogc-sidebar-widget ogc-sidebar-widget--recent">
			<h2 class="ogc-sidebar-widget__title"><?php esc_html_e( 'Recent Posts', 'ogc-newfinity' ); ?></h2>
			<?php if ( $recent->have_posts() ) : ?>
				<ul class="ogc-sidebar-recent">
					<?php
					while ( $recent->have_posts() ) :
						$recent->the_post();
						?>
						<li class="ogc-sidebar-recent__item">
							<a class="ogc-sidebar-recent__link" href="<?php the_permalink(); ?>">
								<span class="ogc-sidebar-recent__thumb">
									<?php
									ogc_the_post_cover(
										array(
											'class' => 'ogc-sidebar-recent__img',
											'size'  => 'thumbnail',
										)
									);
									?>
								</span>
								<span class="ogc-sidebar-recent__title"><?php the_title(); ?></span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="ogc-sidebar-widget__empty"><?php esc_html_e( 'No posts yet.', 'ogc-newfinity' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="glass-card ogc-sidebar-widget ogc-sidebar-widget--categories">
			<h2 class="ogc-sidebar-widget__title"><?php esc_html_e( 'Categories', 'ogc-newfinity' ); ?></h2>
			<ul class="ogc-sidebar-cats">
				<?php
				$blog_url = get_permalink( (int) get_option( 'page_for_posts' ) );
				if ( ! $blog_url ) {
					$blog_url = home_url( '/blog/' );
				}
				foreach ( $categories as $cat ) :
					$url = add_query_arg( 'category', $cat['slug'], $blog_url );
					?>
					<li>
						<a class="ogc-badge ogc-sidebar-cats__pill" href="<?php echo esc_url( $url ); ?>">
							<?php echo esc_html( $cat['name'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="glass-card ogc-sidebar-widget ogc-sidebar-widget--ad">
			<?php
			get_template_part(
				'template-parts/ad',
				'slot',
				array( 'variant' => 'sidebar' )
			);
			?>
		</section>

		<section class="glass-card ogc-sidebar-widget ogc-sidebar-widget--newsletter">
			<h2 class="ogc-sidebar-widget__title"><?php esc_html_e( 'Newsletter', 'ogc-newfinity' ); ?></h2>
			<p class="ogc-sidebar-widget__text">
				<?php esc_html_e( 'Get occasional updates from the team — no spam.', 'ogc-newfinity' ); ?>
			</p>
			<?php /* TODO: connect to email provider */ ?>
			<form class="ogc-newsletter-form" action="#" method="post" onsubmit="return false;">
				<label class="screen-reader-text" for="ogc-newsletter-email">
					<?php esc_html_e( 'Email address', 'ogc-newfinity' ); ?>
				</label>
				<input
					type="email"
					id="ogc-newsletter-email"
					name="ogc_newsletter_email"
					class="ogc-newsletter-form__input"
					placeholder="<?php esc_attr_e( 'you@example.com', 'ogc-newfinity' ); ?>"
					required
					autocomplete="email"
				>
				<button type="submit" class="ogc-btn ogc-btn--sm ogc-newsletter-form__btn">
					<?php esc_html_e( 'Subscribe', 'ogc-newfinity' ); ?>
				</button>
			</form>
		</section>

	<?php endif; ?>

</aside>
