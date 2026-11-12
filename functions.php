<?php
/**
 * OGC NewFinity theme bootstrap.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Theme version — bump when shipping asset cache-busts. */
define( 'OGC_THEME_VERSION', '1.26.12' );

/** Absolute path to the theme root. */
define( 'OGC_THEME_DIR', get_template_directory() );

/** URI to the theme root. */
define( 'OGC_THEME_URI', get_template_directory_uri() );

/**
 * Load core includes.
 */
require_once OGC_THEME_DIR . '/inc/theme-setup.php';
require_once OGC_THEME_DIR . '/inc/template-tags.php';
require_once OGC_THEME_DIR . '/inc/blog.php';
require_once OGC_THEME_DIR . '/inc/seed-articles.php';
require_once OGC_THEME_DIR . '/inc/seed-legal.php';
require_once OGC_THEME_DIR . '/inc/seed-coming-soon.php';
require_once OGC_THEME_DIR . '/inc/widgets.php';
require_once OGC_THEME_DIR . '/inc/theme-options.php';
require_once OGC_THEME_DIR . '/inc/migrate-hero-bg-to-postmeta.php';
require_once OGC_THEME_DIR . '/inc/meta-box-page-settings.php';
require_once OGC_THEME_DIR . '/inc/ads.php';
require_once OGC_THEME_DIR . '/inc/customizer-bridge.php';
require_once OGC_THEME_DIR . '/inc/enqueue.php';
require_once OGC_THEME_DIR . '/inc/seed-content.php';
require_once OGC_THEME_DIR . '/inc/form-helpers.php';
require_once OGC_THEME_DIR . '/inc/contact-form.php';
require_once OGC_THEME_DIR . '/inc/submit-idea-form.php';
require_once OGC_THEME_DIR . '/inc/pricing-data.php';

/**
 * Warn when style.css Version header drifts from OGC_THEME_VERSION.
 *
 * WordPress reads the theme version from style.css; keep that header in sync
 * with OGC_THEME_VERSION whenever bumping the constant.
 */
function ogc_version_mismatch_admin_notice() {
	if ( ! current_user_can( OGC_OPTIONS_CAPABILITY ) ) {
		return;
	}

	$style_version = (string) wp_get_theme()->get( 'Version' );
	if ( $style_version === (string) OGC_THEME_VERSION ) {
		return;
	}

	printf(
		'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: 1: style.css Version header, 2: OGC_THEME_VERSION constant value */
				__( 'OGC NewFinity: style.css Version header (%1$s) and OGC_THEME_VERSION (%2$s) are out of sync and must be updated together.', 'ogc-newfinity' ),
				$style_version,
				OGC_THEME_VERSION
			)
		)
	);
}
add_action( 'admin_notices', 'ogc_version_mismatch_admin_notice' );
