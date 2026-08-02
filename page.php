<?php
/**
 * Default Page template.
 *
 * Used for any Page without a more-specific template (see WP template hierarchy).
 * Supports OGC Page Settings: hero background, subtitle, layout, widget area, visibility.
 *
 * @package OGC_NewFinity
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();

		$ogc_page_id      = (int) get_the_ID();
		$ogc_layout_class = ogc_get_page_layout_class( $ogc_page_id );
		$ogc_show_widgets = ogc_page_enable_widget_area( $ogc_page_id ) && ! ogc_page_should_hide( 'widget_area', $ogc_page_id );
		$ogc_classes      = array( 'ogc-page', 'ogc-page--generic' );
		if ( $ogc_layout_class ) {
			$ogc_classes[] = $ogc_layout_class;
		}
		if ( $ogc_show_widgets ) {
			$ogc_classes[] = 'ogc-page--has-widgets';
		}
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( $ogc_classes ); ?>>

			<section class="ogc-page-hero" aria-labelledby="ogc-page-hero-heading">
				<?php ogc_render_hero_bg( $ogc_page_id ); ?>
				<?php if ( ! ogc_page_should_hide( 'title', $ogc_page_id ) ) : ?>
					<h1 id="ogc-page-hero-heading" class="ogc-page-hero__title"><?php the_title(); ?></h1>
				<?php endif; ?>
				<?php $ogc_subtitle = ogc_get_page_subtitle( $ogc_page_id ); ?>
				<?php if ( $ogc_subtitle ) : ?>
					<p class="ogc-page-hero__subtitle"><?php echo esc_html( $ogc_subtitle ); ?></p>
				<?php endif; ?>
			</section>

			<?php if ( $ogc_show_widgets ) : ?>
				<div class="ogc-page-body ogc-page-body--with-widgets">
					<div class="ogc-page-body__main">
			<?php endif; ?>

			<?php if ( has_post_thumbnail() && ! ogc_page_should_hide( 'featured_image', $ogc_page_id ) ) : ?>
				<figure class="ogc-page-featured">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

			<div class="ogc-page-content ogc-entry__content">
				<?php the_content(); ?>
			</div>

			<?php if ( $ogc_show_widgets ) : ?>
					</div>
					<?php get_template_part( 'template-parts/page', 'widget-area' ); ?>
				</div>
			<?php endif; ?>

		</article>
	<?php endwhile; ?>
<?php else : ?>
	<p class="ogc-no-posts"><?php esc_html_e( 'No content found.', 'ogc-newfinity' ); ?></p>
<?php endif; ?>

<?php
get_footer();
