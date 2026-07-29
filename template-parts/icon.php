<?php
/**
 * Reusable embossed SVG icon chip.
 *
 * Usage: ogc_icon( 'home', array( 'size' => 'sm' ) );
 * Or:    get_template_part( 'template-parts/icon', null, array( 'name' => 'home' ) );
 *
 * @package OGC_NewFinity
 *
 * @var array $args {
 *     @type string $name  Required icon key.
 *     @type string $size  sm|md|lg.
 *     @type string $shape rounded|circle.
 *     @type string $class Extra classes.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'name'  => '',
		'size'  => 'md',
		'shape' => 'rounded',
		'class' => '',
	)
);

$name = sanitize_key( $args['name'] );
if ( ! $name ) {
	return;
}

$svg_size = 24;
if ( 'sm' === $args['size'] ) {
	$svg_size = 16;
} elseif ( 'lg' === $args['size'] ) {
	$svg_size = 28;
}

$svg = ogc_get_icon_svg( $name, $svg_size );
if ( ! $svg ) {
	return;
}

$classes = array( 'ogc-icon' );

if ( 'sm' === $args['size'] ) {
	$classes[] = 'ogc-icon--sm';
} elseif ( 'lg' === $args['size'] ) {
	$classes[] = 'ogc-icon--lg';
}

if ( 'circle' === $args['shape'] ) {
	$classes[] = 'ogc-icon--circle';
}

if ( ! empty( $args['class'] ) ) {
	$classes[] = $args['class'];
}
?>
<span class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-hidden="true">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted static SVG markup.
	echo $svg;
	?>
</span>
