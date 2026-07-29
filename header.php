<?php
/**
 * Site header template.
 *
 * @package OGC_NewFinity
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="ogc-accent-bar" aria-hidden="true"></div>

<header class="ogc-header" role="banner">
	<div class="ogc-header__inner">
		<div class="ogc-header__logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="ogc-header__logo-fallback" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img
						src="<?php echo esc_url( OGC_THEME_URI . '/assets/images/Identity/logo-ogc-newfinity-light.png' ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						width="180"
						height="48"
					>
				</a>
			<?php endif; ?>
		</div>

		<nav class="ogc-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary', 'ogc-newfinity' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'ogc-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			} else {
				echo '<span class="ogc-nav__placeholder">' . esc_html__( 'Assign a menu to Primary Navigation', 'ogc-newfinity' ) . '</span>';
			}
			?>
		</nav>
	</div>
</header>

<main id="ogc-content" class="ogc-main">
