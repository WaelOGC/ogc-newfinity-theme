<?php
/**
 * Blog post card — used on archive and AJAX filter results.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category      = ogc_get_post_primary_category();
$show_image    = (bool) get_theme_mod( 'ogc_blog_card_show_image', true );
$show_date     = (bool) get_theme_mod( 'ogc_blog_card_show_date', true );
$show_category = (bool) get_theme_mod( 'ogc_blog_card_show_category', true );
?>

<article <?php post_class( 'glass-card ogc-blog-card' ); ?>>
	<?php if ( $show_image ) : ?>
		<a class="ogc-blog-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			ogc_the_post_cover(
				array(
					'class' => 'ogc-blog-card__img',
					'size'  => 'medium_large',
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="ogc-blog-card__body">
		<?php if ( $show_category && $category ) : ?>
			<p class="ogc-badge ogc-blog-card__badge"><?php echo esc_html( $category->name ); ?></p>
		<?php endif; ?>

		<?php if ( $show_date ) : ?>
			<time class="ogc-blog-card__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		<?php endif; ?>

		<h3 class="ogc-blog-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php
		$excerpt = ogc_get_blog_card_excerpt();
		if ( $excerpt ) :
			?>
			<p class="ogc-blog-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>

		<a class="ogc-blog-card__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'ogc-newfinity' ); ?>
			<span class="ogc-blog-card__arrow" aria-hidden="true">→</span>
		</a>
	</div>
</article>
