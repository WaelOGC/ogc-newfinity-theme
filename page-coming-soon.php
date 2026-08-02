<?php
/**
 * Coming Soon / maintenance curtain — slug: coming-soon.
 *
 * Standalone shell (no header/footer/nav). Visible page content comes from the
 * seeded "Coming Soon" Page via the_content() so admins edit it in the block editor.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name = get_bloginfo( 'name' );
$fonts_url = function_exists( 'ogc_get_google_fonts_url' ) ? ogc_get_google_fonts_url() : '';
?><!DOCTYPE html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html( $site_name ); ?></title>
	<?php if ( $fonts_url ) : ?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link rel="stylesheet" href="<?php echo esc_url( $fonts_url ); ?>">
	<?php endif; ?>
	<style>
		:root {
			--bg-primary: #0A0A0A;
			--border-accent: #FFD27A;
			--accent-gradient-start: #FFD27A;
			--accent-gradient-end: #FFEAC2;
			--text-primary: #F5F5F5;
			--text-secondary: #C9C9C9;
			--text-on-accent: #1A1200;
			--font-heading: "Space Grotesk", system-ui, -apple-system, "Segoe UI", sans-serif;
			--font-body: Inter, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
		}

		*,
		*::before,
		*::after {
			box-sizing: border-box;
		}

		html {
			font-size: 16px;
		}

		body {
			margin: 0;
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 2rem 1.25rem;
			background-color: #0A0A0A;
			color: #F5F5F5;
			font-family: var(--font-body);
			line-height: 1.6;
			-webkit-font-smoothing: antialiased;
			text-align: center;
		}

		.ogc-coming-soon {
			width: 100%;
			max-width: 36rem;
			margin: 0 auto;
		}

		.ogc-coming-soon__content > * {
			margin-left: auto;
			margin-right: auto;
		}

		.ogc-coming-soon__content .wp-block-image {
			margin: 0 auto 1.5rem;
			line-height: 0;
		}

		.ogc-coming-soon__content .wp-block-image:not(:has(img[src]:not([src=""]))) {
			display: none;
		}

		.ogc-coming-soon__content .wp-block-image img {
			display: block;
			width: 5.5rem;
			height: 5.5rem;
			margin: 0 auto;
			object-fit: contain;
			padding: 0.85rem;
			border-radius: 50%;
			border: 1px solid #FFD27A;
			background: #141414;
			box-sizing: border-box;
		}

		.ogc-coming-soon__content .ogc-coming-soon__location,
		.ogc-coming-soon__content p.ogc-coming-soon__location {
			margin: 0 0 1.25rem;
			font-family: var(--font-body);
			font-size: 0.75rem;
			font-weight: 500;
			letter-spacing: 0.14em;
			text-transform: uppercase;
			color: #C9C9C9;
		}

		.ogc-coming-soon__content h1,
		.ogc-coming-soon__content .ogc-coming-soon__heading {
			margin: 0 auto;
			max-width: 18ch;
			font-family: var(--font-heading);
			font-size: clamp(1.85rem, 5vw, 2.75rem);
			font-weight: 500;
			font-style: italic;
			letter-spacing: -0.02em;
			line-height: 1.15;
			color: #F5F5F5;
		}

		.ogc-coming-soon__content h1::after {
			content: "";
			display: block;
			width: 3.5rem;
			height: 3px;
			margin: 1.25rem auto 1.5rem;
			border-radius: 999px;
			background: linear-gradient(90deg, #FFD27A, #FFEAC2);
		}

		.ogc-coming-soon__content p:not(.ogc-coming-soon__location),
		.ogc-coming-soon__content .ogc-coming-soon__message {
			margin: 0 auto 1.75rem;
			max-width: 28rem;
			color: #C9C9C9;
			font-family: var(--font-body);
			font-size: 1rem;
		}

		.ogc-coming-soon__content .wp-block-buttons {
			margin: 0 0 1.75rem;
			display: flex;
			justify-content: center;
		}

		.ogc-coming-soon__content .wp-block-button__link {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-height: 2.75rem;
			padding: 0.7rem 1.75rem;
			border-radius: 999px;
			border: 0;
			background: linear-gradient(145deg, #FFD27A, #FFEAC2);
			color: #1A1200 !important;
			font-family: var(--font-heading);
			font-size: 0.95rem;
			font-weight: 600;
			letter-spacing: 0.01em;
			text-decoration: none;
			box-shadow: none;
		}

		.ogc-coming-soon__content .wp-block-button__link:hover,
		.ogc-coming-soon__content .wp-block-button__link:focus-visible {
			opacity: 0.92;
		}

		.ogc-coming-soon__content .wp-block-button__link:focus-visible {
			outline: 2px solid #FFEAC2;
			outline-offset: 3px;
		}

		.ogc-coming-soon__content .wp-block-social-links {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 0.65rem;
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.ogc-coming-soon__content .wp-social-link {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 40px;
			height: 40px;
			margin: 0;
			padding: 0;
			border-radius: 50%;
			border: 1px solid rgba(255, 210, 122, 0.5);
			background: linear-gradient(145deg, #1a1a1a, #0d0d0d) !important;
			box-shadow:
				inset 0 1px 0 rgba(255, 255, 255, 0.08),
				0 2px 6px rgba(0, 0, 0, 0.4);
		}

		.ogc-coming-soon__content .wp-social-link a {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			height: 100%;
			color: #FFD27A !important;
		}

		.ogc-coming-soon__content .wp-social-link svg {
			width: 16px;
			height: 16px;
			fill: currentColor;
		}

		.ogc-coming-soon__content .wp-social-link:hover,
		.ogc-coming-soon__content .wp-social-link:focus-within {
			border-color: rgba(255, 234, 194, 0.9);
		}

		.ogc-coming-soon__content .wp-social-link:hover a,
		.ogc-coming-soon__content .wp-social-link:focus-within a {
			color: #FFEAC2 !important;
		}
	</style>
</head>
<body>
	<main class="ogc-coming-soon" role="main">
		<div class="ogc-coming-soon__content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</main>
</body>
</html>
