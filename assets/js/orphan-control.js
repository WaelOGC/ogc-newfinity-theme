/**
 * OGC NewFinity — orphan / widow prevention (site-wide).
 * Joins the last two words of text blocks with a non-breaking space
 * so a single-word final line never stands alone.
 */
(function () {
	'use strict';

	var SELECTOR = [
		'.no-orphans',
		'.ogc-home-hero__lead',
		'.ogc-page-hero__lead',
		'.ogc-section-intro',
		'.ogc-service-card__text',
		'.ogc-trust-card__text',
		'.ogc-stand-card__text',
		'.ogc-pillar-card__text',
		'.ogc-about-dual__body p',
		'.ogc-mission-intro__text',
		'.ogc-home-cta__text',
		'.ogc-footer__about',
		'.ogc-page-quote__text',
		'.ogc-blog-card__excerpt',
		'.ogc-blog-empty'
	].join(',');

	var NBSP = '\u00A0';
	var debounceTimer = 0;

	function originalText(el) {
		if (!el.getAttribute('data-ogc-orphan-src')) {
			el.setAttribute('data-ogc-orphan-src', el.textContent);
		}
		return el.getAttribute('data-ogc-orphan-src') || el.textContent;
	}

	function protect(el) {
		var src = originalText(el);
		if (!src) {
			return;
		}

		/* Collapse whitespace for splitting; preserve readable spacing when rebuilding. */
		var cleaned = src.replace(/\s+/g, ' ').trim();
		var parts = cleaned.split(' ');
		if (parts.length < 2) {
			el.textContent = cleaned;
			return;
		}

		var last = parts.pop();
		var prev = parts.pop();
		parts.push(prev + NBSP + last);
		el.textContent = parts.join(' ');
	}

	function run() {
		var nodes = document.querySelectorAll(SELECTOR);
		for (var i = 0; i < nodes.length; i++) {
			protect(nodes[i]);
		}
	}

	function onResize() {
		window.clearTimeout(debounceTimer);
		debounceTimer = window.setTimeout(run, 150);
	}

	function boot() {
		run();
		window.addEventListener('resize', onResize, { passive: true });
	}

	/* Expose for AJAX-replaced blog markup (and similar dynamic content). */
	window.ogcOrphanControlRerun = run;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
