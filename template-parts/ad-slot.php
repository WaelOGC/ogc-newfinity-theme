<?php
/**
 * Advertisement slot — rendered only when Theme Options → Ads Manager has
 * the master switch on and this slot enabled with content.
 *
 * Expected $args:
 * - variant: sidebar|inline (default sidebar) — also the Ads Manager slot key
 * - label: optional visible label
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! get_theme_mod( 'ogc_ads_enabled', false ) ) {
	return;
}

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'variant' => 'sidebar',
		'label'   => __( 'Advertisement', 'ogc-newfinity' ),
	)
);

$variant = sanitize_key( $args['variant'] );

if ( function_exists( 'ogc_get_ads_config' ) ) {
	$ads_config = ogc_get_ads_config();
} else {
	$ads_raw    = get_theme_mod( 'ogc_ads_config_json', '' );
	$ads_config = is_array( $ads_raw ) ? $ads_raw : json_decode( (string) $ads_raw, true );
}

if ( ! is_array( $ads_config ) || ! isset( $ads_config[ $variant ] ) ) {
	return;
}

$slot = is_object( $ads_config[ $variant ] ) ? (array) $ads_config[ $variant ] : $ads_config[ $variant ];

if ( ! is_array( $slot ) || empty( $slot['enabled'] ) ) {
	return;
}

$type  = isset( $slot['type'] ) && 'code' === $slot['type'] ? 'code' : 'image';
$image = '';
$code  = '';

if ( 'image' === $type ) {
	$image = isset( $slot['image'] ) ? esc_url( (string) $slot['image'] ) : '';
	if ( '' === $image ) {
		return;
	}
} else {
	$code = isset( $slot['code'] ) ? trim( (string) $slot['code'] ) : '';
	if ( '' === $code ) {
		return;
	}
}

$link    = isset( $slot['url'] ) ? esc_url( (string) $slot['url'] ) : '';
$classes = array( 'ogc-ad-slot', 'ogc-ad-slot--' . $variant, 'ogc-ad-slot--filled' );
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<span class="ogc-ad-slot__label"><?php echo esc_html( $args['label'] ); ?></span>
	<?php
	if ( 'image' === $type ) {
		$img = sprintf(
			'<img class="ogc-ad-slot__img" src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
			esc_url( $image ),
			esc_attr( $args['label'] )
		);

		if ( $link ) {
			printf(
				'<a class="ogc-ad-slot__link" href="%1$s" target="_blank" rel="noopener nofollow sponsored">%2$s</a>',
				esc_url( $link ),
				$img // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
			);
		} else {
			echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
		}
	} else {
		// Stored pre-sanitized by the Ads Manager schema (scoped wp_kses allow-list).
		echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</div>
