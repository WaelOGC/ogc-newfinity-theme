/**
 * OGC NewFinity — front-end scripts.
 * Mobile nav toggle + header scroll state. Space background lives in space-background.js.
 */
(function () {
	'use strict';

	var SCROLL_THRESHOLD = 20;

	function initMobileNav() {
		var toggle = document.getElementById('ogc-nav-toggle');
		var shell = document.querySelector('.ogc-site-header');
		var nav = document.getElementById('ogc-primary-nav');

		if (!toggle || !shell || !nav) {
			return;
		}

		var openLabel = toggle.getAttribute('aria-label') || 'Open menu';
		var closeLabel = 'Close menu';

		function setOpen(isOpen) {
			shell.classList.toggle('is-nav-open', isOpen);
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			toggle.setAttribute('aria-label', isOpen ? closeLabel : openLabel);
		}

		toggle.addEventListener('click', function () {
			setOpen(!shell.classList.contains('is-nav-open'));
		});

		nav.addEventListener('click', function (event) {
			var link = event.target.closest('a');
			if (link && window.matchMedia('(max-width: 991px)').matches) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && shell.classList.contains('is-nav-open')) {
				setOpen(false);
				toggle.focus();
			}
		});

		window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 992px)').matches) {
				setOpen(false);
			}
		});
	}

	function initHeaderScroll() {
		var shell = document.querySelector('.ogc-site-header');
		if (!shell) {
			return;
		}

		var ticking = false;

		function update() {
			ticking = false;
			shell.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
		}

		function onScroll() {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}

		update();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	function init() {
		initMobileNav();
		initHeaderScroll();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
