<?php
/**
 * Site footer template.
 *
 * @package OGC_NewFinity
 */
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
							'width'  => 54,
							'height' => 52,
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
					<?php
					esc_html_e(
						'OGC NewFinity is a digital agency in The Hague, Netherlands, delivering ethical innovation across software and app development, custom WordPress, UI/UX, marketing strategy, cybersecurity, and AI solutions.',
						'ogc-newfinity'
					);
					?>
				</p>
				<ul class="ogc-social" aria-label="<?php esc_attr_e( 'Social media', 'ogc-newfinity' ); ?>">
					<li>
						<a class="ogc-social__link" href="https://x.com/OGCNewfinity" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php esc_html_e( 'OGC NewFinity on X', 'ogc-newfinity' ); ?></span>
							<?php ogc_social_icon( 'x' ); ?>
						</a>
					</li>
					<li>
						<a class="ogc-social__link" href="https://www.linkedin.com/company/ogc-newfinity" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php esc_html_e( 'OGC NewFinity on LinkedIn', 'ogc-newfinity' ); ?></span>
							<?php ogc_social_icon( 'linkedin' ); ?>
						</a>
					</li>
					<li>
						<a class="ogc-social__link" href="https://github.com/OGC-NewFinity" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php esc_html_e( 'OGC NewFinity on GitHub', 'ogc-newfinity' ); ?></span>
							<?php ogc_social_icon( 'github' ); ?>
						</a>
					</li>
					<li>
						<a class="ogc-social__link" href="https://discord.com/invite/AyzKNHdn" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php esc_html_e( 'OGC NewFinity on Discord', 'ogc-newfinity' ); ?></span>
							<?php ogc_social_icon( 'discord' ); ?>
						</a>
					</li>
				</ul>
			</div>

			<nav class="ogc-footer__col" aria-label="<?php esc_attr_e( 'Quick Links', 'ogc-newfinity' ); ?>">
				<p class="ogc-footer__heading"><?php esc_html_e( 'Quick Links', 'ogc-newfinity' ); ?></p>
				<ul class="ogc-footer__links">
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Our Services', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/our-mission/' ) ); ?>"><?php esc_html_e( 'Our Mission', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/submit-idea/' ) ); ?>"><?php esc_html_e( 'Submit Your Idea', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog & Insights', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php esc_html_e( 'Help & Support', 'ogc-newfinity' ); ?></a></li>
				</ul>
			</nav>

			<div class="ogc-footer__col">
				<p class="ogc-footer__heading"><?php esc_html_e( 'Contact', 'ogc-newfinity' ); ?></p>
				<ul class="ogc-footer__contact">
					<li class="ogc-footer__contact-item">
						<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'map' ); ?></span>
						<span><?php esc_html_e( 'The Hague, Netherlands', 'ogc-newfinity' ); ?></span>
					</li>
					<li class="ogc-footer__contact-item">
						<span class="ogc-footer__contact-icon" aria-hidden="true"><?php ogc_footer_icon( 'mail' ); ?></span>
						<a href="mailto:contact@ogcnewfinity.com">contact@ogcnewfinity.com</a>
					</li>
				</ul>
			</div>

			<nav class="ogc-footer__col" aria-label="<?php esc_attr_e( 'Legal', 'ogc-newfinity' ); ?>">
				<p class="ogc-footer__heading"><?php esc_html_e( 'Legal', 'ogc-newfinity' ); ?></p>
				<ul class="ogc-footer__links">
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms and Conditions', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'ogc-newfinity' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/refund-policy/' ) ); ?>"><?php esc_html_e( 'Refund Policy', 'ogc-newfinity' ); ?></a></li>
				</ul>
			</nav>

		</div>
	</div>

	<div class="ogc-accent-bar" aria-hidden="true"></div>

	<div class="ogc-footer__copyright">
		<p>
			&copy; 2022&ndash;<?php echo esc_html( date( 'Y' ) ); ?>
			<?php esc_html_e( 'OGC NewFinity. All rights reserved.', 'ogc-newfinity' ); ?>
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
