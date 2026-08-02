<?php
/**
 * Page widget area — reuses the Blog Sidebar (`ogc-blog-sidebar`).
 *
 * Unlike template-parts/blog-sidebar.php, this renders nothing when the sidebar
 * has no widgets (no recent-posts / categories fallback).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'ogc-blog-sidebar' ) ) {
	return;
}
?>
<aside class="ogc-page-widget-area" aria-label="<?php esc_attr_e( 'Widget area', 'ogc-newfinity' ); ?>">
	<?php dynamic_sidebar( 'ogc-blog-sidebar' ); ?>
</aside>
