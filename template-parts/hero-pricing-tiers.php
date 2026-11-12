<?php
/**
 * Pricing hero — isometric floating tier slabs (page-specific).
 * Initialized by assets/js/pricing-tiers.js via [data-ogc-pricing-tiers].
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ogc-pricing-tiers" data-ogc-pricing-tiers aria-hidden="true">
	<div class="ogc-pricing-tiers__stage">
		<div class="ogc-pricing-tiers__glow"></div>
		<div class="ogc-pricing-tiers__slab ogc-pricing-tiers__slab--entry" data-tier="entry"></div>
		<div class="ogc-pricing-tiers__slab ogc-pricing-tiers__slab--core" data-tier="core"></div>
		<div class="ogc-pricing-tiers__slab ogc-pricing-tiers__slab--advanced" data-tier="advanced"></div>
		<span class="ogc-pricing-tiers__pill ogc-pricing-tiers__pill--advanced"><?php esc_html_e( 'Advanced', 'ogc-newfinity' ); ?></span>
		<span class="ogc-pricing-tiers__pill ogc-pricing-tiers__pill--core"><?php esc_html_e( 'Core', 'ogc-newfinity' ); ?></span>
		<span class="ogc-pricing-tiers__pill ogc-pricing-tiers__pill--entry"><?php esc_html_e( 'Entry', 'ogc-newfinity' ); ?></span>
	</div>
</div>
