<?php
/**
 * Advertisement slot placeholder.
 *
 * Expected $args:
 * - variant: sidebar|inline (default sidebar)
 * - label: optional visible label
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'variant' => 'sidebar',
		'label'   => __( 'Advertisement', 'ogc-newfinity' ),
	)
);

$variant = sanitize_key( $args['variant'] );
$classes = array( 'ogc-ad-slot', 'ogc-ad-slot--' . $variant );
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<span class="ogc-ad-slot__label"><?php echo esc_html( $args['label'] ); ?></span>
	<?php /* TODO: wire to Theme Options / Customizer once built */ ?>
</div>
