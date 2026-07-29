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
define( 'OGC_THEME_VERSION', '1.5.0' );

/** Absolute path to the theme root. */
define( 'OGC_THEME_DIR', get_template_directory() );

/** URI to the theme root. */
define( 'OGC_THEME_URI', get_template_directory_uri() );

/**
 * Load core includes.
 *
 * Planned (later steps):
 * - inc/customizer.php
 * - inc/theme-options-page.php
 * - inc/ai-chat-widget.php
 */
require_once OGC_THEME_DIR . '/inc/theme-setup.php';
require_once OGC_THEME_DIR . '/inc/enqueue.php';
require_once OGC_THEME_DIR . '/inc/template-tags.php';
require_once OGC_THEME_DIR . '/inc/seed-content.php';
