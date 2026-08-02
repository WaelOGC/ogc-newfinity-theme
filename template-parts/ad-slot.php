<?php
/**
 * Advertisement slot — AdSense unit or advertiser creative.
 *
 * Expected $args:
 * - slot: normalized slot array from ogc_get_ads_for_placement()
 *
 * Renders nothing when ads are off or the slot has no usable content.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ogc_mod' ) || ! ogc_mod( 'ogc_ads_enabled' ) ) {
	return;
}

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'slot' => null,
	)
);

$slot = $args['slot'];
if ( ! is_array( $slot ) || empty( $slot['source'] ) ) {
	return;
}

$source = sanitize_key( $slot['source'] );
$label  = isset( $slot['label'] ) && '' !== (string) $slot['label']
	? (string) $slot['label']
	: __( 'Advertisement', 'ogc-newfinity' );

$classes = array( 'ogc-ad-slot', 'ogc-ad-slot--' . $source, 'ogc-ad-slot--filled' );

if ( ! empty( $slot['placement'] ) ) {
	$classes[] = 'ogc-ad-slot--' . sanitize_html_class( (string) $slot['placement'] );
}

if ( ! empty( $slot['hide_mobile'] ) ) {
	$classes[] = 'ogc-ad-hide-mobile';
}

if ( ! empty( $slot['hide_tablet'] ) ) {
	$classes[] = 'ogc-ad-hide-tablet';
}

$style_attr = '';
if ( ! empty( $slot['width'] ) && ! empty( $slot['height'] ) ) {
	$style_attr = sprintf(
		'max-width:%dpx;min-height:%dpx;',
		(int) $slot['width'],
		(int) $slot['height']
	);
}

if ( 'adsense' === $source ) {
	$client  = function_exists( 'ogc_get_adsense_client_id' ) ? ogc_get_adsense_client_id() : '';
	$slot_id = isset( $slot['slot_id'] ) ? (string) $slot['slot_id'] : '';
	if ( '' === $client || '' === $slot_id ) {
		return;
	}

	$format_map = array(
		'auto'       => 'auto',
		'horizontal' => 'horizontal',
		'vertical'   => 'vertical',
		'rectangle'  => 'rectangle',
		'inarticle'  => 'fluid',
	);
	$format_key = isset( $slot['format'] ) ? (string) $slot['format'] : 'auto';
	$format     = isset( $format_map[ $format_key ] ) ? $format_map[ $format_key ] : 'auto';
	$full_width = ! empty( $slot['full_width'] );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $label ); ?>">
		<span class="ogc-ad-slot__label"><?php echo esc_html( $label ); ?></span>
		<ins
			class="adsbygoogle"
			style="display:block;<?php echo 'inarticle' === $format_key ? 'text-align:center;' : ''; ?>"
			data-ad-client="<?php echo esc_attr( $client ); ?>"
			data-ad-slot="<?php echo esc_attr( $slot_id ); ?>"
			data-ad-format="<?php echo esc_attr( $format ); ?>"
			<?php if ( 'inarticle' === $format_key ) : ?>
				data-ad-layout="in-article"
			<?php endif; ?>
			<?php if ( $full_width ) : ?>
				data-full-width-responsive="true"
			<?php endif; ?>
		></ins>
		<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
	</div>
	<?php
	return;
}

if ( 'custom' !== $source ) {
	return;
}

$type = isset( $slot['type'] ) && 'code' === $slot['type'] ? 'code' : 'image';

if ( 'code' === $type ) {
	$code = isset( $slot['code'] ) ? trim( (string) $slot['code'] ) : '';
	if ( '' === $code ) {
		return;
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $label ); ?>">
		<span class="ogc-ad-slot__label"><?php echo esc_html( $label ); ?></span>
		<?php
		// Stored pre-sanitized by the Ads Manager schema (scoped wp_kses / unfiltered_html).
		echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
	<?php
	return;
}

$image = isset( $slot['image'] ) ? esc_url( (string) $slot['image'] ) : '';
if ( '' === $image ) {
	return;
}

$lazy = ! isset( $slot['lazy'] ) || $slot['lazy'];
$img  = sprintf(
	'<img class="ogc-ad-slot__img" src="%1$s" alt="%2$s" %3$s decoding="async"%4$s>',
	esc_url( $image ),
	esc_attr( $label ),
	$lazy ? 'loading="lazy"' : 'loading="eager"',
	( ! empty( $slot['width'] ) && ! empty( $slot['height'] ) )
		? sprintf( ' width="%d" height="%d"', (int) $slot['width'], (int) $slot['height'] )
		: ''
);

$link = isset( $slot['url'] ) ? esc_url( (string) $slot['url'] ) : '';
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $label ); ?>">
	<span class="ogc-ad-slot__label"><?php echo esc_html( $label ); ?></span>
	<?php
	if ( $link ) {
		printf(
			'<a class="ogc-ad-slot__link" href="%1$s" target="_blank" rel="noopener nofollow sponsored">%2$s</a>',
			esc_url( $link ),
			$img // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
		);
	} else {
		echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
	}
	?>
</div>
