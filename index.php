<?php
/**
 * Main fallback template.
 *
 * Used when no more-specific template matches (see WP template hierarchy).
 *
 * @package OGC_NewFinity
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ogc-entry' ); ?>>
			<header class="ogc-entry__header">
				<?php the_title( '<h1 class="ogc-entry__title">', '</h1>' ); ?>
			</header>
			<div class="ogc-entry__content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
<?php else : ?>
	<p class="ogc-no-posts"><?php esc_html_e( 'No content found.', 'ogc-newfinity' ); ?></p>
<?php endif; ?>

<?php
get_footer();
