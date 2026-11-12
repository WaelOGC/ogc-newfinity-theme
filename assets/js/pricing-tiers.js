/**
 * OGC NewFinity — Pricing hero isometric tier slabs.
 * Drop-in: template-parts/hero-pricing-tiers.php ([data-ogc-pricing-tiers]).
 */
(function () {
	'use strict';

	var roots = document.querySelectorAll('[data-ogc-pricing-tiers]');
	if (!roots.length) {
		return;
	}

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	var narrowMq = window.matchMedia('(max-width: 899px)');

	function ogcReducedMotion() {
		return rmMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}

	function refresh(root) {
		root.classList.toggle('is-reduced', ogcReducedMotion());
		root.classList.toggle('is-compact', narrowMq.matches);
	}

	for (var i = 0; i < roots.length; i++) {
		refresh(roots[i]);
	}

	function onChange() {
		for (var j = 0; j < roots.length; j++) {
			refresh(roots[j]);
		}
	}

	if (typeof rmMq.addEventListener === 'function') {
		rmMq.addEventListener('change', onChange);
		narrowMq.addEventListener('change', onChange);
	} else if (typeof rmMq.addListener === 'function') {
		rmMq.addListener(onChange);
		narrowMq.addListener(onChange);
	}
})();
