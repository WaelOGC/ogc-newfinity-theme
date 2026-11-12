<?php
/**
 * Site footer template.
 *
 * @package OGC_NewFinity
 */

if ( is_singular( 'page' ) && function_exists( 'ogc_page_should_hide' ) && ogc_page_should_hide( 'footer' ) ) :
	?>
	</main><!-- #ogc-content -->
	<?php wp_footer(); ?>
	</body>
	</html>
	<?php
	return;
endif;

$ogc_footer_logo   = function_exists( 'ogc_get_footer_logo_url' ) ? ogc_get_footer_logo_url() : '';
$ogc_footer_logo_w = max( 16, (int) get_theme_mod( 'ogc_footer_logo_width', 54 ) );

$ogc_footer_columns = function_exists( 'ogc_get_footer_columns' ) ? ogc_get_footer_columns() : array();
if ( ! is_array( $ogc_footer_columns ) || empty( $ogc_footer_columns ) ) {
	$ogc_footer_columns = array(
		array(
			'title' => __( 'Quick Links', 'ogc-newfinity' ),
			'links' => array(
				array(
					'label' => __( 'Our Services', 'ogc-newfinity' ),
					'url'   => home_url( '/services/' ),
				),
				array(
					'label' => __( 'Pricing', 'ogc-newfinity' ),
					'url'   => home_url( '/pricing/' ),
				),
				array(
					'label' => __( 'Our Mission', 'ogc-newfinity' ),
					'url'   => home_url( '/our-mission/' ),
				),
				array(
					'label' => __( 'Blog & Insights', 'ogc-newfinity' ),
					'url'   => home_url( '/blog/' ),
				),
				array(
					'label' => __( 'Help & Support', 'ogc-newfinity' ),
					'url'   => home_url( '/support/' ),
				),
				array(
					'label' => __( 'Contact', 'ogc-newfinity' ),
					'url'   => home_url( '/contact/' ),
				),
			),
		),
		array(
			'title' => __( 'Legal', 'ogc-newfinity' ),
			'links' => array(
				array(
					'label' => __( 'Privacy Policy', 'ogc-newfinity' ),
					'url'   => home_url( '/privacy-policy/' ),
				),
				array(
					'label' => __( 'Terms and Conditions', 'ogc-newfinity' ),
					'url'   => home_url( '/terms-and-conditions/' ),
				),
				array(
					'label' => __( 'Cookie Policy', 'ogc-newfinity' ),
					'url'   => home_url( '/cookie-policy/' ),
				),
				array(
					'label' => __( 'Refund Policy', 'ogc-newfinity' ),
					'url'   => home_url( '/refund-policy/' ),
				),
			),
		),
	);
}
$ogc_footer_columns = array_values( $ogc_footer_columns );

/**
 * Render one link column from the Theme Options footer-columns structure.
 * Skips columns with no usable links so an emptied column leaves no bare heading.
 */
$ogc_render_footer_column = static function ( $column ) {
	if ( is_object( $column ) ) {
		$column = (array) $column;
	}
	if ( ! is_array( $column ) ) {
		return;
	}

	$title = isset( $column['title'] ) ? sanitize_text_field( (string) $column['title'] ) : '';
	$links = isset( $column['links'] ) && is_array( $column['links'] ) ? $column['links'] : array();

	$rows = array();
	foreach ( $links as $link ) {
		if ( is_object( $link ) ) {
			$link = (array) $link;
		}
		if ( ! is_array( $link ) ) {
			continue;
		}
		$label = isset( $link['label'] ) ? sanitize_text_field( (string) $link['label'] ) : '';
		$url   = isset( $link['url'] ) ? esc_url( (string) $link['url'] ) : '';
		if ( '' === $label || '' === $url ) {
			continue;
		}
		$rows[] = array(
			'label' => $label,
			'url'   => $url,
		);
	}

	if ( empty( $rows ) ) {
		return;
	}
	?>
	<nav class="ogc-footer__col" aria-label="<?php echo esc_attr( $title ); ?>">
		<?php if ( '' !== $title ) : ?>
			<p class="ogc-footer__heading"><?php echo esc_html( $title ); ?></p>
		<?php endif; ?>
		<ul class="ogc-footer__links">
			<?php foreach ( $rows as $row ) : ?>
				<li><a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['label'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
};
?>
</main><!-- #ogc-content -->

<footer class="ogc-footer" role="contentinfo">
	<div class="ogc-accent-bar" aria-hidden="true"></div>

	<div class="ogc-footer__main">
		<div class="ogc-footer__grid">

			<div class="ogc-footer__col ogc-footer__col--brand">
				<div class="ogc-footer__brand-row">
					<?php
					ogc_get_logo(
						array(
							'class'  => 'ogc-footer__logo',
							'src'    => $ogc_footer_logo,
							'width'  => $ogc_footer_logo_w,
							// Mark is 21:20 — keep attributes proportional to the configured width.
							'height' => (int) round( $ogc_footer_logo_w * 20 / 21 ),
							'lazy'   => true,
							'alt'    => 'OGC NewFinity logo',
						)
					);
					?>
					<p class="ogc-footer__wordmark">
						<span class="ogc-footer__wordmark-ogc">OGC</span>
						<span class="ogc-footer__wordmark-new">NewFinity</span>
					</p>
				</div>
				<p class="ogc-footer__about">
					<?php echo esc_html( ogc_get_footer_tagline() ); ?>
				</p>
				<ul class="ogc-social" aria-label="<?php esc_attr_e( 'Social media', 'ogc-newfinity' ); ?>">
					<?php
					$ogc_socials = array(
						'x'        => __( 'OGC NewFinity on X', 'ogc-newfinity' ),
						'linkedin' => __( 'OGC NewFinity on LinkedIn', 'ogc-newfinity' ),
						'github'   => __( 'OGC NewFinity on GitHub', 'ogc-newfinity' ),
						'discord'  => __( 'OGC NewFinity on Discord', 'ogc-newfinity' ),
					);
					foreach ( $ogc_socials as $network => $label ) :
						$url = ogc_get_social_url( $network );
						if ( ! $url ) {
							continue;
						}
						?>
						<li>
							<a class="ogc-social__link" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
								<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
								<?php ogc_social_icon( $network ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<?php
					$ogc_custom_socials = function_exists( 'ogc_get_custom_social_links' ) ? ogc_get_custom_social_links() : array();
					foreach ( $ogc_custom_socials as $ogc_custom_social ) :
						?>
						<li>
							<a class="ogc-social__link" href="<?php echo esc_url( $ogc_custom_social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<span class="screen-reader-text"><?php echo esc_html( $ogc_custom_social['label'] ); ?></span>
								<?php ogc_social_icon( $ogc_custom_social['icon'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php
			if ( isset( $ogc_footer_columns[0] ) ) {
				$ogc_render_footer_column( $ogc_footer_columns[0] );
			}
			?>

			<div class="ogc-footer__col">
				<p class="ogc-footer__heading"><?php esc_html_e( 'Contact', 'ogc-newfinity' ); ?></p>
				<ul class="ogc-footer__contact">
					<li class="ogc-footer__contact-item">
						<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'map' ); ?></span>
						<span><?php echo esc_html( ogc_get_location_text() ); ?></span>
					</li>
					<li class="ogc-footer__contact-item">
						<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'mail' ); ?></span>
						<a href="mailto:<?php echo esc_attr( ogc_get_contact_email() ); ?>"><?php echo esc_html( ogc_get_contact_email() ); ?></a>
					</li>
				</ul>
			</div>

			<?php
			foreach ( array_slice( $ogc_footer_columns, 1 ) as $ogc_footer_column ) {
				$ogc_render_footer_column( $ogc_footer_column );
			}
			?>

		</div>
	</div>

	<div class="ogc-accent-bar" aria-hidden="true"></div>

	<div class="ogc-footer__copyright">
		<?php
		$ogc_registration_line = function_exists( 'ogc_get_footer_company_registration_line' )
			? ogc_get_footer_company_registration_line()
			: '';
		?>
		<p class="ogc-footer__copyright-line">
			<span class="ogc-footer__copyright-text">
				<?php
				if ( function_exists( 'ogc_get_footer_copyright' ) ) {
					echo esc_html( ogc_get_footer_copyright() );
				} else {
					printf(
						/* translators: %s: current year */
						esc_html__( '© 2022–%s OGC NewFinity. All rights reserved.', 'ogc-newfinity' ),
						esc_html( wp_date( 'Y' ) )
					);
				}
				?>
			</span>
			<?php if ( '' !== $ogc_registration_line ) : ?>
				<span class="ogc-footer__copyright-sep" aria-hidden="true"> · </span>
				<span class="ogc-footer__registration"><?php echo esc_html( $ogc_registration_line ); ?></span>
			<?php endif; ?>
		</p>
	</div>
</footer>

<button
	type="button"
	class="ogc-back-top"
	id="ogc-back-top"
	aria-label="<?php esc_attr_e( 'Back to top', 'ogc-newfinity' ); ?>"
>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 19V5"/>
		<path d="M5 12l7-7 7 7"/>
	</svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
