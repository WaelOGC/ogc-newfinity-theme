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

<?php get_template_part( 'template-parts/background', 'layer' ); ?>

<div class="ogc-site-header">
	<div class="ogc-accent-bar" aria-hidden="true"></div>

	<header class="ogc-header" role="banner">
		<div class="ogc-header__inner">
			<div class="ogc-header__brand">
				<?php
				ogc_get_logo(
					array(
						'class'  => 'ogc-header__logo',
						'width'  => 42,
						'height' => 40,
						'lazy'   => false,
						'alt'    => 'OGC NewFinity logo',
					)
				);
				?>
			</div>

			<nav class="ogc-nav" id="ogc-primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'ogc-newfinity' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'ogc-nav__list',
						'menu_id'        => 'ogc-primary-menu',
						'depth'          => 1,
						'fallback_cb'    => 'ogc_primary_nav_fallback',
					)
				);
				?>
			</nav>

			<div class="ogc-header__actions">
				<a class="ogc-btn ogc-header__cta" href="<?php echo esc_url( home_url( '/submit-idea/' ) ); ?>">
					<?php esc_html_e( 'Submit Idea', 'ogc-newfinity' ); ?>
				</a>

				<button
					type="button"
					class="ogc-nav-toggle"
					id="ogc-nav-toggle"
					aria-controls="ogc-primary-nav"
					aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Open menu', 'ogc-newfinity' ); ?>"
				>
					<span class="ogc-nav-toggle__bar" aria-hidden="true"></span>
					<span class="ogc-nav-toggle__bar" aria-hidden="true"></span>
					<span class="ogc-nav-toggle__bar" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</header>
</div>

<main id="ogc-content" class="ogc-main">
