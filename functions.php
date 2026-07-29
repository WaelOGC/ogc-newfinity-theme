<?php
/**
 * OGC NewFinity theme bootstrap.
 *
 * Loads modular includes from /inc. Feature modules (Customizer, Theme Options,
 * AI chat) will be required here in later build steps — not yet.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Theme version — bump when shipping asset cache-busts. */
define( 'OGC_THEME_VERSION', '1.0.0' );

/** Absolute path to the theme root. */
define( 'OGC_THEME_DIR', get_template_directory() );

/** URI to the theme root. */
define( 'OGC_THEME_URI', get_template_directory_uri() );

/**
 * Load core includes.
 *
 * Planned (later steps — do not require yet):
 * - inc/customizer.php
 * - inc/theme-options-page.php
 * - inc/ai-chat-widget.php
 * - inc/template-tags.php
 */
require_once OGC_THEME_DIR . '/inc/theme-setup.php';
require_once OGC_THEME_DIR . '/inc/enqueue.php';
