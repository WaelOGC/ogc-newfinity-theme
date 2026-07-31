<?php
/**
 * Template Name: Legal Page
 *
 * Shared template for Privacy Policy, Terms, Cookie Policy, and Refund Policy.
 *
 * @package OGC_NewFinity
 */

get_header();

$slug = get_post_field( 'post_name', get_queried_object_id() );

$updated_dates = array(
	'privacy-policy'       => '[DATE — update before launch]',
	'terms-and-conditions' => '[DATE — update before launch]',
	'cookie-policy'        => '[DATE — update before launch]',
	'refund-policy'        => '[DATE — update before launch]',
);

$last_updated = isset( $updated_dates[ $slug ] ) ? $updated_dates[ $slug ] : '[DATE — update before launch]';
$contact_url  = home_url( '/contact/' );

while ( have_posts() ) :
	the_post();

	$raw_content = get_the_content();
	$filtered    = apply_filters( 'the_content', $raw_content );
	$prepared    = function_exists( 'ogc_legal_prepare_content' )
		? ogc_legal_prepare_content( $filtered )
		: array(
			'html' => $filtered,
			'toc'  => array(),
		);
	?>

<article class="ogc-page ogc-page--legal">

	<header class="ogc-page-hero ogc-legal-hero">
		<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
		<h1 class="ogc-page-hero__title"><?php the_title(); ?></h1>
		<p class="ogc-legal-hero__updated">
			<?php
			printf(
				/* translators: %s: last-updated date string */
				esc_html__( 'Last updated: %s', 'ogc-newfinity' ),
				esc_html( $last_updated )
			);
			?>
		</p>
	</header>

	<div class="ogc-legal-layout">

		<aside class="ogc-legal-notice glass-card" role="note">
			<p>
				<?php
				esc_html_e(
					'This page is a general template for informational purposes only. It is not legal advice. Have a qualified Dutch/EU lawyer review and adapt it before this site handles real client payments.',
					'ogc-newfinity'
				);
				?>
			</p>
		</aside>

		<?php if ( ! empty( $prepared['toc'] ) ) : ?>
			<nav class="ogc-legal-toc" aria-label="<?php esc_attr_e( 'On this page', 'ogc-newfinity' ); ?>">
				<p class="ogc-legal-toc__heading"><?php esc_html_e( 'On this page', 'ogc-newfinity' ); ?></p>
				<ol class="ogc-legal-toc__list">
					<?php foreach ( $prepared['toc'] as $item ) : ?>
						<li>
							<a href="#<?php echo esc_attr( $item['id'] ); ?>">
								<?php echo esc_html( $item['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>

		<div class="ogc-legal-prose">
			<?php echo $prepared['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content with injected heading ids. ?>
		</div>

		<p class="ogc-legal-cta">
			<?php esc_html_e( 'Questions about this policy?', 'ogc-newfinity' ); ?>
			<a href="<?php echo esc_url( $contact_url ); ?>">
				<?php esc_html_e( 'Contact us', 'ogc-newfinity' ); ?>
			</a>
		</p>

	</div>

</article>

	<?php
endwhile;

get_footer();
