/**
 * OGC Elementor widgets — lightweight front-end behaviors.
 * Style mirrors assets/js/admin-page-settings.js: small helpers, no framework.
 */
(function () {
	'use strict';

	function initImageReveal(root) {
		var nodes = (root || document).querySelectorAll(
			'.ogc-el-image-effects--reveal-on-scroll:not([data-ogc-reveal-bound])'
		);
		if (!nodes.length || !('IntersectionObserver' in window)) {
			nodes.forEach(function (el) {
				el.classList.add('is-revealed');
			});
			return;
		}
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-revealed');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.2 }
		);
		nodes.forEach(function (el) {
			el.setAttribute('data-ogc-reveal-bound', '1');
			io.observe(el);
		});
	}

	function buildDots(slider, count, active) {
		var host = slider.querySelector('[data-ogc-slider-dots]');
		if (!host) return;
		host.innerHTML = '';
		for (var i = 0; i < count; i++) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'ogc-el-slider__dot' + (i === active ? ' is-active' : '');
			btn.setAttribute('aria-label', 'Go to slide ' + (i + 1));
			btn.setAttribute('data-ogc-slider-dot', String(i));
			host.appendChild(btn);
		}
	}

	function goTo(slider, index) {
		var slides = slider.querySelectorAll('.ogc-el-slider__slide');
		if (!slides.length) return;
		var next = ((index % slides.length) + slides.length) % slides.length;
		slides.forEach(function (slide, i) {
			slide.classList.toggle('is-active', i === next);
		});
		var dots = slider.querySelectorAll('[data-ogc-slider-dot]');
		dots.forEach(function (dot, i) {
			dot.classList.toggle('is-active', i === next);
		});
		slider._ogcIndex = next;
	}

	function initSlider(slider) {
		if (slider.getAttribute('data-ogc-slider-bound')) return;
		slider.setAttribute('data-ogc-slider-bound', '1');

		var slides = slider.querySelectorAll('.ogc-el-slider__slide');
		if (!slides.length) return;

		slider._ogcIndex = 0;
		buildDots(slider, slides.length, 0);

		var autoplay = slider.getAttribute('data-autoplay') === '1';
		var speed = parseInt(slider.getAttribute('data-speed') || '4500', 10) || 4500;
		var timer = null;

		function clear() {
			if (timer) {
				clearInterval(timer);
				timer = null;
			}
		}

		function arm() {
			clear();
			if (!autoplay || slides.length < 2) return;
			timer = setInterval(function () {
				goTo(slider, (slider._ogcIndex || 0) + 1);
			}, speed);
		}

		var prev = slider.querySelector('[data-ogc-slider-prev]');
		var next = slider.querySelector('[data-ogc-slider-next]');
		if (prev) {
			prev.addEventListener('click', function () {
				goTo(slider, (slider._ogcIndex || 0) - 1);
				arm();
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				goTo(slider, (slider._ogcIndex || 0) + 1);
				arm();
			});
		}
		slider.addEventListener('click', function (e) {
			var dot = e.target.closest('[data-ogc-slider-dot]');
			if (!dot) return;
			goTo(slider, parseInt(dot.getAttribute('data-ogc-slider-dot'), 10) || 0);
			arm();
		});

		arm();
	}

	function boot(root) {
		initImageReveal(root);
		(root || document).querySelectorAll('[data-ogc-slider]').forEach(initSlider);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			boot(document);
		});
	} else {
		boot(document);
	}

	// Elementor editor / preview re-renders.
	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) return;
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			var el = $scope && $scope[0] ? $scope[0] : null;
			boot(el || document);
		});
	});
})();
