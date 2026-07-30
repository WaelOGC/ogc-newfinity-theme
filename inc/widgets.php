<?php
/**
 * Widget areas for Blog & Insights.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Blog Sidebar widget area.
 */
function ogc_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'ogc-newfinity' ),
			'id'            => 'ogc-blog-sidebar',
			'description'   => __( 'Sidebar for the blog archive and single posts (desktop/tablet). Theme defaults render when empty.', 'ogc-newfinity' ),
			'before_widget' => '<section id="%1$s" class="glass-card ogc-sidebar-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="ogc-sidebar-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'ogc_register_sidebars' );
